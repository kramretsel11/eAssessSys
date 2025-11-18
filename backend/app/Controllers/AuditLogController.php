<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AuditLogController extends ResourceController
{
    protected $modelName = 'App\Models\AuditLogModel';
    protected $format = 'json';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Get all audit logs with pagination and filtering
     */
    public function getLogs()
    {
        try {
            $page = $this->request->getVar('page') ?? 1;
            $limit = $this->request->getVar('limit') ?? 20;
            $search = $this->request->getVar('search') ?? '';
            $action = $this->request->getVar('action') ?? '';
            $userId = $this->request->getVar('user_id') ?? '';
            $dateFrom = $this->request->getVar('date_from') ?? '';
            $dateTo = $this->request->getVar('date_to') ?? '';

            $offset = ($page - 1) * $limit;

            $builder = $this->model->builder();
            $builder->select('audit_logs.*, users.username, users.name as user_name')
                   ->join('users', 'users.id = audit_logs.user_id', 'left');

            // Apply filters
            if (!empty($search)) {
                $builder->groupStart()
                       ->like('audit_logs.action', $search)
                       ->orLike('audit_logs.details', $search)
                       ->orLike('users.username', $search)
                       ->orLike('users.name', $search)
                       ->groupEnd();
            }

            if (!empty($action)) {
                $builder->where('audit_logs.action', $action);
            }

            if (!empty($userId)) {
                $builder->where('audit_logs.user_id', $userId);
            }

            if (!empty($dateFrom)) {
                $builder->where('DATE(audit_logs.created_at) >=', $dateFrom);
            }

            if (!empty($dateTo)) {
                $builder->where('DATE(audit_logs.created_at) <=', $dateTo);
            }

            // Get total count for pagination
            $totalRecords = $builder->countAllResults(false);

            // Get paginated results
            $logs = $builder->orderBy('audit_logs.created_at', 'DESC')
                           ->limit($limit, $offset)
                           ->get()
                           ->getResultArray();

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'logs' => $logs,
                    'pagination' => [
                        'page' => (int)$page,
                        'limit' => (int)$limit,
                        'total' => $totalRecords,
                        'pages' => ceil($totalRecords / $limit)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->fail('Failed to fetch audit logs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get audit log by ID
     */
    public function getLog($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('Log ID is required', 400);
            }

            $builder = $this->model->builder();
            $builder->select('audit_logs.*, tblusers.username, CONCAT(tblusers.firstName, " ", tblusers.lastName) as user_name')
                   ->join('tblusers', 'tblusers.id = audit_logs.user_id', 'left');

            if (!$log) {
                return $this->fail('Audit log not found', 404);
            }

            return $this->respond([
                'status' => 'success',
                'data' => $log
            ]);

        } catch (\Exception $e) {
            return $this->fail('Failed to fetch audit log: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create new audit log
     */
    public function createLog()
    {
        try {
            $data = $this->request->getJSON(true);

            $rules = [
                'user_id' => 'required|integer',
                'action' => 'required|string|max_length[100]',
                'resource_type' => 'required|string|max_length[50]',
                'resource_id' => 'permit_empty|integer',
                'details' => 'permit_empty|string',
                'ip_address' => 'permit_empty|string|max_length[45]',
                'user_agent' => 'permit_empty|string|max_length[255]'
            ];

            if (!$this->validate($data, $rules)) {
                return $this->fail($this->validator->getErrors(), 400);
            }

            $logData = [
                'user_id' => $data['user_id'],
                'action' => $data['action'],
                'resource_type' => $data['resource_type'],
                'resource_id' => $data['resource_id'] ?? null,
                'details' => $data['details'] ?? '',
                'ip_address' => $data['ip_address'] ?? $this->request->getIPAddress(),
                'user_agent' => $data['user_agent'] ?? $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $logId = $this->model->insert($logData);

            if (!$logId) {
                return $this->fail('Failed to create audit log', 500);
            }

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Audit log created successfully',
                'data' => ['id' => $logId]
            ]);

        } catch (\Exception $e) {
            return $this->fail('Failed to create audit log: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get audit log statistics
     */
    public function getStatistics()
    {
        try {
            $builder = $this->model->builder();

            // Total logs
            $totalLogs = $builder->countAllResults(false);

            // Logs today
            $logsToday = $builder->where('DATE(created_at)', date('Y-m-d'))
                               ->countAllResults(false);

            // Logs this week
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $logsThisWeek = $builder->where('DATE(created_at) >=', $weekStart)
                                  ->countAllResults(false);

            // Most active users
            $activeUsers = $builder->select('tblusers.username, CONCAT(tblusers.firstName, " ", tblusers.lastName) as name, COUNT(*) as log_count')
                                 ->join('tblusers', 'tblusers.id = audit_logs.user_id')
                                 ->where('DATE(audit_logs.created_at) >=', date('Y-m-d', strtotime('-30 days')))
                                 ->groupBy('audit_logs.user_id')
                                 ->orderBy('log_count', 'DESC')
                                 ->limit(5)
                                 ->get()
                                 ->getResultArray();

            // Most common actions
            $commonActions = $builder->select('action, COUNT(*) as action_count')
                                   ->where('DATE(created_at) >=', date('Y-m-d', strtotime('-30 days')))
                                   ->groupBy('action')
                                   ->orderBy('action_count', 'DESC')
                                   ->limit(10)
                                   ->get()
                                   ->getResultArray();

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'total_logs' => $totalLogs,
                    'logs_today' => $logsToday,
                    'logs_this_week' => $logsThisWeek,
                    'active_users' => $activeUsers,
                    'common_actions' => $commonActions
                ]
            ]);

        } catch (\Exception $e) {
            return $this->fail('Failed to fetch statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Export audit logs to CSV
     */
    public function exportLogs()
    {
        try {
            $search = $this->request->getVar('search') ?? '';
            $action = $this->request->getVar('action') ?? '';
            $userId = $this->request->getVar('user_id') ?? '';
            $dateFrom = $this->request->getVar('date_from') ?? '';
            $dateTo = $this->request->getVar('date_to') ?? '';

            $builder = $this->model->builder();
            $builder->select('audit_logs.*, tblusers.username, CONCAT(tblusers.firstName, " ", tblusers.lastName) as user_name')
                   ->join('tblusers', 'tblusers.id = audit_logs.user_id', 'left');

            // Apply same filters as getLogs
            if (!empty($search)) {
                $builder->groupStart()
                       ->like('audit_logs.action', $search)
                       ->orLike('audit_logs.details', $search)
                       ->orLike('tblusers.username', $search)
                       ->orLike('tblusers.firstName', $search)
                       ->orLike('tblusers.lastName', $search)
                       ->groupEnd();
            }

            if (!empty($action)) {
                $builder->where('audit_logs.action', $action);
            }

            if (!empty($userId)) {
                $builder->where('audit_logs.user_id', $userId);
            }

            if (!empty($dateFrom)) {
                $builder->where('DATE(audit_logs.created_at) >=', $dateFrom);
            }

            if (!empty($dateTo)) {
                $builder->where('DATE(audit_logs.created_at) <=', $dateTo);
            }

            $logs = $builder->orderBy('audit_logs.created_at', 'DESC')
                           ->get()
                           ->getResultArray();

            // Generate CSV content
            $csvContent = "ID,User,Action,Resource Type,Resource ID,Details,IP Address,Date Time\n";
            
            foreach ($logs as $log) {
                $csvContent .= sprintf(
                    "%d,%s,%s,%s,%s,%s,%s,%s\n",
                    $log['id'],
                    $log['user_name'] ?? $log['username'] ?? 'Unknown',
                    $log['action'],
                    $log['resource_type'],
                    $log['resource_id'] ?? '',
                    '"' . str_replace('"', '""', $log['details']) . '"',
                    $log['ip_address'],
                    $log['created_at']
                );
            }

            $filename = 'audit_logs_' . date('Y-m-d_H-i-s') . '.csv';

            return $this->response
                        ->setHeader('Content-Type', 'text/csv')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                        ->setBody($csvContent);

        } catch (\Exception $e) {
            return $this->fail('Failed to export audit logs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete old audit logs (cleanup)
     */
    public function cleanup()
    {
        try {
            $days = $this->request->getVar('days') ?? 90; // Default 90 days
            
            $cutoffDate = date('Y-m-d', strtotime('-' . $days . ' days'));
            
            $deletedCount = $this->model->where('DATE(created_at) <', $cutoffDate)
                                      ->delete();

            return $this->respond([
                'status' => 'success',
                'message' => "Deleted {$deletedCount} audit logs older than {$days} days",
                'data' => ['deleted_count' => $deletedCount]
            ]);

        } catch (\Exception $e) {
            return $this->fail('Failed to cleanup audit logs: ' . $e->getMessage(), 500);
        }
    }
}