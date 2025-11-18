<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'details',
        'ip_address',
        'user_agent',
        'created_at'
    ];
    
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    
    protected $validationRules = [
        'user_id' => 'required|integer',
        'action' => 'required|string|max_length[100]',
        'resource_type' => 'required|string|max_length[50]',
        'resource_id' => 'permit_empty|integer',
        'details' => 'permit_empty|string',
        'ip_address' => 'permit_empty|string|max_length[45]',
        'user_agent' => 'permit_empty|string|max_length[255]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a valid integer'
        ],
        'action' => [
            'required' => 'Action is required',
            'max_length' => 'Action cannot exceed 100 characters'
        ],
        'resource_type' => [
            'required' => 'Resource type is required',
            'max_length' => 'Resource type cannot exceed 50 characters'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setCreatedAt'];
    
    protected function setCreatedAt(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }
    
    /**
     * Log an activity
     */
    public function logActivity($userId, $action, $resourceType, $resourceId = null, $details = '', $request = null)
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'details' => $details,
            'ip_address' => $request ? $request->getIPAddress() : '',
            'user_agent' => $request ? $request->getUserAgent() : '',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get logs by user
     */
    public function getLogsByUser($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Get logs by action
     */
    public function getLogsByAction($action, $limit = 50)
    {
        return $this->where('action', $action)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Get logs by resource
     */
    public function getLogsByResource($resourceType, $resourceId = null, $limit = 50)
    {
        $builder = $this->where('resource_type', $resourceType);
        
        if ($resourceId !== null) {
            $builder->where('resource_id', $resourceId);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                      ->limit($limit)
                      ->findAll();
    }
    
    /**
     * Get logs within date range
     */
    public function getLogsByDateRange($startDate, $endDate, $limit = 100)
    {
        return $this->where('DATE(created_at) >=', $startDate)
                   ->where('DATE(created_at) <=', $endDate)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Get activity statistics
     */
    public function getActivityStats($days = 30)
    {
        $startDate = date('Y-m-d', strtotime('-' . $days . ' days'));
        
        // Daily activity counts
        $dailyActivity = $this->select('DATE(created_at) as date, COUNT(*) as count')
                            ->where('DATE(created_at) >=', $startDate)
                            ->groupBy('DATE(created_at)')
                            ->orderBy('date', 'ASC')
                            ->findAll();
        
        // Action distribution
        $actionStats = $this->select('action, COUNT(*) as count')
                           ->where('DATE(created_at) >=', $startDate)
                           ->groupBy('action')
                           ->orderBy('count', 'DESC')
                           ->findAll();
        
        // Resource type distribution
        $resourceStats = $this->select('resource_type, COUNT(*) as count')
                             ->where('DATE(created_at) >=', $startDate)
                             ->groupBy('resource_type')
                             ->orderBy('count', 'DESC')
                             ->findAll();
        
        return [
            'daily_activity' => $dailyActivity,
            'action_stats' => $actionStats,
            'resource_stats' => $resourceStats
        ];
    }
    
    /**
     * Clean up old logs
     */
    public function cleanup($days = 90)
    {
        $cutoffDate = date('Y-m-d', strtotime('-' . $days . ' days'));
        return $this->where('DATE(created_at) <', $cutoffDate)->delete();
    }
}