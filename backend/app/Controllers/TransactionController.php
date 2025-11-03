<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssessmentRequestModel;

class TransactionController extends BaseController
{
    protected $assessmentRequestModel;
    
    public function __construct()
    {
        $this->assessmentRequestModel = new AssessmentRequestModel();
    }
    
    /**
     * Get requests by status for quick actions
     */
    public function getRequestsByStatus($status = null)
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized Access'
            ]);
        }
        
        try {
            $builder = $this->assessmentRequestModel->builder();
            
            if ($status) {
                // Map frontend status names to database values
                $statusMap = [
                    'pending' => 'Pending',
                    'under-review' => 'Under Review',
                    'approved' => 'Approved', 
                    'rejected' => 'Rejected',
                    'completed' => 'Completed'
                ];
                
                $dbStatus = $statusMap[$status] ?? $status;
                $builder->where('requestStatus', $dbStatus);
            }
            
            $requests = $builder->select('
                id, 
                ownerName as owner_name,
                municipality as property_location,
                requestStatus as status,
                created_at, 
                updated_at,
                areaNo as land_area,
                generalDescription as property_type
            ')
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $requests,
                'count' => count($requests)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error fetching requests: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update request status (validate, approve, reject)
     */
    public function updateStatus()
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized Access'
            ]);
        }
        
        try {
            $input = $this->request->getJSON(true);
            
            $validation = \Config\Services::validation();
            $validation->setRules([
                'id' => 'required|integer',
                'status' => 'required|in_list[Pending,Under Review,Approved,Rejected,Completed]',
                'remarks' => 'permit_empty|string'
            ]);
            
            if (!$validation->run($input)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }
            
            $id = $input['id'];
            $status = $input['status'];
            $remarks = $input['remarks'] ?? '';
            
            // Check if request exists
            $request = $this->assessmentRequestModel->find($id);
            if (!$request) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Request not found'
                ]);
            }
            
            // Update the request
            $updateData = [
                'requestStatus' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (!empty($remarks)) {
                $updateData['memorada'] = $remarks;
            }
            
            // Add reviewer information if available
            // You can extend this to include user authentication
            $updateData['approvedBy'] = 1; // System Admin ID
            $updateData['approvedDate'] = date('Y-m-d H:i:s');
            
            $updated = $this->assessmentRequestModel->update($id, $updateData);
            
            if ($updated) {
                // Log the status change
                $logData = [
                    'request_id' => $id,
                    'old_status' => $request['requestStatus'] ?? 'Unknown',
                    'new_status' => $status,
                    'changed_by' => 'System Admin',
                    'changed_at' => date('Y-m-d H:i:s'),
                    'remarks' => $remarks
                ];
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Request {$status} successfully",
                    'data' => [
                        'id' => $id,
                        'status' => $status,
                        'updated_at' => $updateData['updated_at'],
                        'can_generate_certificate' => $status === 'Approved'
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to update request status'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Bulk update multiple requests
     */
    public function bulkUpdateStatus()
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized Access'
            ]);
        }
        
        try {
            $input = $this->request->getJSON(true);
            
            $validation = \Config\Services::validation();
            $validation->setRules([
                'ids' => 'required|is_array',
                'ids.*' => 'required|integer',
                'status' => 'required|in_list[Pending,Under Review,Approved,Rejected,Completed]',
                'remarks' => 'permit_empty|string'
            ]);
            
            if (!$validation->run($input)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }
            
            $ids = $input['ids'];
            $status = $input['status'];
            $remarks = $input['remarks'] ?? '';
            
            $updateData = [
                'requestStatus' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'approvedBy' => 1, // System Admin ID
                'approvedDate' => date('Y-m-d H:i:s')
            ];
            
            if (!empty($remarks)) {
                $updateData['memorada'] = $remarks;
            }
            
            $successCount = 0;
            $failedIds = [];
            
            foreach ($ids as $id) {
                $updated = $this->assessmentRequestModel->update($id, $updateData);
                if ($updated) {
                    $successCount++;
                } else {
                    $failedIds[] = $id;
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully updated {$successCount} requests",
                'data' => [
                    'success_count' => $successCount,
                    'failed_ids' => $failedIds,
                    'total_requested' => count($ids)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error in bulk update: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get quick statistics for dashboard
     */
    public function getQuickStats()
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized Access'
            ]);
        }
        
        try {
            $stats = [
                'pending_approvals' => $this->assessmentRequestModel
                    ->where('requestStatus', 'Pending')
                    ->orWhere('requestStatus', 'Under Review')
                    ->countAllResults(),
                    
                'validation_queue' => $this->assessmentRequestModel
                    ->where('requestStatus', 'Pending')
                    ->countAllResults(),
                    
                'new_requests' => $this->assessmentRequestModel
                    ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                    ->countAllResults(),
                    
                'total_requests' => $this->assessmentRequestModel->countAllResults()
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error fetching quick stats: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test method to manually approve a request (for testing)
     */
    public function testApprove($requestId)
    {
        try {
            // Find the request
            $request = $this->assessmentRequestModel->find($requestId);
            if (!$request) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Request not found'
                ]);
            }
            
            // Update to approved
            $updated = $this->assessmentRequestModel->update($requestId, [
                'requestStatus' => 'Approved',
                'updated_at' => date('Y-m-d H:i:s'),
                'approvedBy' => 1,
                'approvedDate' => date('Y-m-d H:i:s'),
                'memorada' => 'Approved via test endpoint'
            ]);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Request approved successfully',
                    'data' => [
                        'id' => $requestId,
                        'status' => 'Approved',
                        'previous_status' => $request['requestStatus']
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to approve request'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error approving request: ' . $e->getMessage()
            ]);
        }
    }
}