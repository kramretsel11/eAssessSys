<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class EvaluationController extends Controller
{
    protected $db;
    protected $request;
    protected $auditLogModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->request = service('request');
        $this->auditLogModel = new \App\Models\AuditLogModel();
    }

    /**
     * Get evaluations assigned to current evaluator
     */
    public function getAssignedEvaluations()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user || $user['userType'] != 4) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. Evaluator role required.'
                ])->setStatusCode(403);
            }

            $query = $this->db->table('tbl_assessment_requests ar')
                ->select('ar.*, p.ownerFirstName, p.ownerLastName, p.propertyAddress, p.municipality, p.barangay')
                ->join('tbl_property p', 'p.propertyId = ar.propertyId')
                ->where('ar.assignedEvaluator', $user['userId'])
                ->whereIn('ar.requestStatus', ['pending', 'in-progress', 'under-evaluation'])
                ->orderBy('ar.requestDate', 'DESC');

            $evaluations = $query->get()->getResultArray();

            // Format the response
            $formattedEvaluations = [];
            foreach ($evaluations as $evaluation) {
                $formattedEvaluations[] = [
                    'id' => $evaluation['requestId'],
                    'requestNumber' => 'REQ-' . date('Y') . '-' . str_pad($evaluation['requestId'], 3, '0', STR_PAD_LEFT),
                    'requestType' => $evaluation['requestType'],
                    'propertyAddress' => $evaluation['propertyAddress'],
                    'municipality' => $evaluation['municipality'],
                    'barangay' => $evaluation['barangay'],
                    'ownerName' => $evaluation['ownerFirstName'] . ' ' . $evaluation['ownerLastName'],
                    'status' => $this->mapStatus($evaluation['requestStatus']),
                    'priority' => $evaluation['priority'] ?? 'medium',
                    'submittedDate' => $evaluation['requestDate'],
                    'assessedValue' => $evaluation['assessedValue'] ?? 0,
                    'marketValue' => $evaluation['marketValue'] ?? 0,
                    'classification' => $evaluation['propertyClassification'] ?? '',
                    'notes' => $evaluation['evaluatorNotes'] ?? ''
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedEvaluations
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting assigned evaluations: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve evaluations'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get evaluator statistics
     */
    public function getEvaluatorStats()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user || $user['userType'] != 4) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. Evaluator role required.'
                ])->setStatusCode(403);
            }

            $userId = $user['userId'];
            $currentMonth = date('Y-m');
            $today = date('Y-m-d');

            // Get pending reviews
            $pendingReviews = $this->db->table('tbl_assessment_requests')
                ->where('assignedEvaluator', $userId)
                ->whereIn('requestStatus', ['pending', 'under-evaluation'])
                ->countAllResults();

            // Get completed today
            $completedToday = $this->db->table('tbl_assessment_requests')
                ->where('assignedEvaluator', $userId)
                ->where('requestStatus', 'approved')
                ->where('DATE(evaluatedDate)', $today)
                ->countAllResults();

            // Get certificates generated
            $certificates = $this->db->table('tbl_certificates c')
                ->join('tbl_assessment_requests ar', 'ar.requestId = c.requestId')
                ->where('ar.assignedEvaluator', $userId)
                ->where('DATE(c.generatedDate) >=', $currentMonth . '-01')
                ->countAllResults();

            // Calculate average evaluation time
            $avgTimeQuery = $this->db->query("
                SELECT AVG(TIMESTAMPDIFF(HOUR, requestDate, evaluatedDate)) as avg_hours 
                FROM tbl_assessment_requests 
                WHERE assignedEvaluator = ? 
                AND requestStatus = 'approved' 
                AND evaluatedDate IS NOT NULL
                AND DATE(evaluatedDate) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ", [$userId]);
            
            $avgTimeResult = $avgTimeQuery->getRow();
            $averageTime = $avgTimeResult ? round($avgTimeResult->avg_hours, 1) . 'h' : '0h';

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'pendingReviews' => $pendingReviews,
                    'completedToday' => $completedToday,
                    'certificates' => $certificates,
                    'averageTime' => $averageTime
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting evaluator stats: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve statistics'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get recent evaluations for evaluator
     */
    public function getRecentEvaluations()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user || $user['userType'] != 4) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. Evaluator role required.'
                ])->setStatusCode(403);
            }

            $query = $this->db->table('tbl_assessment_requests ar')
                ->select('ar.*, p.ownerFirstName, p.ownerLastName, p.propertyAddress, p.municipality')
                ->join('tbl_property p', 'p.propertyId = ar.propertyId')
                ->where('ar.assignedEvaluator', $user['userId'])
                ->orderBy('ar.requestDate', 'DESC')
                ->limit(10);

            $evaluations = $query->get()->getResultArray();

            // Format the response
            $formattedEvaluations = [];
            foreach ($evaluations as $evaluation) {
                $formattedEvaluations[] = [
                    'id' => $evaluation['requestId'],
                    'requestNumber' => 'REQ-' . date('Y') . '-' . str_pad($evaluation['requestId'], 3, '0', STR_PAD_LEFT),
                    'requestType' => $evaluation['requestType'],
                    'propertyAddress' => $evaluation['propertyAddress'],
                    'municipality' => $evaluation['municipality'],
                    'status' => $this->mapStatus($evaluation['requestStatus']),
                    'submittedDate' => $evaluation['requestDate']
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedEvaluations
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting recent evaluations: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve recent evaluations'
            ])->setStatusCode(500);
        }
    }

    /**
     * Update evaluation (approve/reject/save progress)
     */
    public function updateEvaluation()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user || $user['userType'] != 4) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. Evaluator role required.'
                ])->setStatusCode(403);
            }

            $requestId = $this->request->getPost('requestId');
            $action = $this->request->getPost('action'); // 'approve', 'reject', 'save'
            $assessedValue = $this->request->getPost('assessedValue');
            $marketValue = $this->request->getPost('marketValue');
            $classification = $this->request->getPost('classification');
            $notes = $this->request->getPost('notes');

            if (!$requestId || !$action) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Request ID and action are required'
                ])->setStatusCode(400);
            }

            $updateData = [
                'assessedValue' => $assessedValue,
                'marketValue' => $marketValue,
                'propertyClassification' => $classification,
                'evaluatorNotes' => $notes,
                'evaluatedBy' => $user['userId'],
                'evaluatedDate' => date('Y-m-d H:i:s')
            ];

            switch ($action) {
                case 'approve':
                    $updateData['requestStatus'] = 'approved';
                    break;
                case 'reject':
                    $updateData['requestStatus'] = 'rejected';
                    break;
                case 'save':
                    $updateData['requestStatus'] = 'in-progress';
                    break;
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid action'
                    ])->setStatusCode(400);
            }

            $result = $this->db->table('tbl_assessment_requests')
                ->where('requestId', $requestId)
                ->where('assignedEvaluator', $user['userId'])
                ->update($updateData);

            if ($result) {
                // Log the action
                $this->auditLogModel->logActivity([
                    'userId' => $user['userId'],
                    'action' => 'evaluation_' . $action,
                    'description' => "Evaluation {$action}d for request {$requestId}",
                    'ipAddress' => $this->request->getIPAddress(),
                    'userAgent' => $this->request->getUserAgent()
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Evaluation updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update evaluation'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error updating evaluation: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update evaluation'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get available certificates for evaluator
     */
    public function getEvaluatorCertificates()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user || $user['userType'] != 4) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. Evaluator role required.'
                ])->setStatusCode(403);
            }

            $query = $this->db->table('tbl_assessment_requests ar')
                ->select('ar.*, p.ownerFirstName, p.ownerLastName, p.propertyAddress, p.municipality, c.certificateId, c.certificateNumber, c.generatedDate as certDate')
                ->join('tbl_property p', 'p.propertyId = ar.propertyId')
                ->join('tbl_certificates c', 'c.requestId = ar.requestId', 'left')
                ->where('ar.assignedEvaluator', $user['userId'])
                ->whereIn('ar.requestStatus', ['approved'])
                ->orderBy('ar.evaluatedDate', 'DESC');

            $certificates = $query->get()->getResultArray();

            // Format the response
            $formattedCertificates = [];
            foreach ($certificates as $cert) {
                $formattedCertificates[] = [
                    'id' => $cert['requestId'],
                    'certificateNumber' => $cert['certificateNumber'] ?: 'CERT-' . date('Y') . '-' . str_pad($cert['requestId'], 3, '0', STR_PAD_LEFT),
                    'requestNumber' => 'REQ-' . date('Y') . '-' . str_pad($cert['requestId'], 3, '0', STR_PAD_LEFT),
                    'propertyOwner' => $cert['ownerFirstName'] . ' ' . $cert['ownerLastName'],
                    'propertyAddress' => $cert['propertyAddress'],
                    'municipality' => $cert['municipality'],
                    'classification' => $cert['propertyClassification'],
                    'assessedValue' => $cert['assessedValue'],
                    'marketValue' => $cert['marketValue'],
                    'status' => $cert['certificateId'] ? 'generated' : 'pending',
                    'dateGenerated' => $cert['certDate'] ?: $cert['evaluatedDate'],
                    'notes' => $cert['evaluatorNotes']
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedCertificates
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting evaluator certificates: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve certificates'
            ])->setStatusCode(500);
        }
    }

    /**
     * Generate certificate for approved evaluation
     */
    public function generateCertificate()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user || $user['userType'] != 4) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. Evaluator role required.'
                ])->setStatusCode(403);
            }

            $requestId = $this->request->getPost('requestId');
            if (!$requestId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Request ID is required'
                ])->setStatusCode(400);
            }

            // Check if request is approved and assigned to this evaluator
            $request = $this->db->table('tbl_assessment_requests')
                ->where('requestId', $requestId)
                ->where('assignedEvaluator', $user['userId'])
                ->where('requestStatus', 'approved')
                ->get()->getRowArray();

            if (!$request) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Request not found or not approved'
                ])->setStatusCode(404);
            }

            // Check if certificate already exists
            $existingCert = $this->db->table('tbl_certificates')
                ->where('requestId', $requestId)
                ->get()->getRowArray();

            if ($existingCert) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate already exists for this request'
                ])->setStatusCode(400);
            }

            // Generate certificate number
            $certificateNumber = 'CERT-' . date('Y') . '-' . str_pad($requestId, 3, '0', STR_PAD_LEFT);

            // Insert certificate record
            $certificateData = [
                'requestId' => $requestId,
                'certificateNumber' => $certificateNumber,
                'certificateType' => 'property_assessment',
                'generatedBy' => $user['userId'],
                'generatedDate' => date('Y-m-d H:i:s'),
                'status' => 'generated'
            ];

            $result = $this->db->table('tbl_certificates')->insert($certificateData);

            if ($result) {
                // Log the action
                $this->auditLogModel->logActivity([
                    'userId' => $user['userId'],
                    'action' => 'certificate_generated',
                    'description' => "Certificate {$certificateNumber} generated for request {$requestId}",
                    'ipAddress' => $this->request->getIPAddress(),
                    'userAgent' => $this->request->getUserAgent()
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Certificate generated successfully',
                    'data' => [
                        'certificateNumber' => $certificateNumber,
                        'certificateId' => $this->db->insertID()
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to generate certificate'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error generating certificate: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate certificate'
            ])->setStatusCode(500);
        }
    }

    /**
     * Map internal status to frontend status
     */
    private function mapStatus($status)
    {
        $statusMap = [
            'pending' => 'pending',
            'under-evaluation' => 'in-progress',
            'in-progress' => 'in-progress',
            'approved' => 'approved',
            'rejected' => 'rejected'
        ];

        return $statusMap[$status] ?? $status;
    }

    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        $authService = new \App\Controllers\Auth();
        return $authService->getCurrentUserData();
    }
}