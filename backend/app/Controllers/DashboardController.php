<?php

namespace App\Controllers;

use App\Models\AssessmentRequestModel;
use CodeIgniter\RESTful\ResourceController;

class DashboardController extends ResourceController
{
    protected $assessmentModel;

    public function __construct()
    {
        $this->assessmentModel = new AssessmentRequestModel();
        helper(['url']);
    }

    // Get dashboard statistics
    public function getStatistics()
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            $response = [
                'success' => false,
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        try {
            // Get all assessment requests
            $allRequests = $this->assessmentModel->findAll();
            $totalRequests = count($allRequests);
            
            // Count by status for admin dashboard
            $pendingRequests = 0;
            $approvedRequests = 0;
            $rejectedRequests = 0;
            
            foreach ($allRequests as $request) {
                $status = strtolower($request['requestStatus'] ?? '');
                if (stripos($status, 'pending') !== false || stripos($status, 'new') !== false || 
                    stripos($status, 'declared') !== false || stripos($status, 'submitted') !== false) {
                    $pendingRequests++;
                } elseif (stripos($status, 'approved') !== false || stripos($status, 'completed') !== false) {
                    $approvedRequests++;
                } elseif (stripos($status, 'reject') !== false || stripos($status, 'cancel') !== false || 
                         stripos($status, 'denied') !== false) {
                    $rejectedRequests++;
                }
            }
            
            // Estimate certificates generated (assuming each approved request can generate multiple certificates)
            $certificatesGenerated = $approvedRequests;

            $statistics = [
                'totalRequests' => $totalRequests,
                'pendingRequests' => $pendingRequests,
                'approvedRequests' => $approvedRequests,
                'rejectedRequests' => $rejectedRequests,
                'certificatesGenerated' => $certificatesGenerated,
                // Keep original stats for compatibility
                'totalRpus' => $totalRequests,
                'totalCancelled' => $rejectedRequests,
                'totalNewlyDeclared' => $pendingRequests,
                'totalProperty' => $totalRequests
            ];

            return $this->respond([
                'status' => 'success',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get municipality data
    public function getMunicipalities()
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            $response = [
                'success' => false,
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        try {
            // Aurora province municipalities
            $municipalities = [
                'Maria Aurora', 'San Luis', 'Baler', 'Dipaculao', 
                'Dingalan', 'Dinalungan', 'Casiguran', 'Dilasag'
            ];

            $municipalityData = [];
            $index = 1;

            foreach ($municipalities as $municipality) {
                $count = $this->assessmentModel
                    ->where('municipality', $municipality)
                    ->countAllResults();

                $municipalityData[] = [
                    'id' => $index++,
                    'name' => $municipality,
                    'totalProperties' => $count
                ];
            }

            return $this->respond([
                'success' => true,
                'data' => $municipalityData
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Error fetching municipality data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get chart data for assets per municipality
    public function getAssetsChartData()
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            $response = [
                'success' => false,
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        try {
            $municipalities = [
                'Maria Aurora', 'San Luis', 'Baler', 'Dipaculao', 
                'Dingalan', 'Dinalungan', 'Casiguran', 'Dilasag'
            ];

            $chartData = [];
            
            foreach ($municipalities as $municipality) {
                // For now, we'll categorize based on general property types
                // You can modify this based on your actual data structure
                $total = $this->assessmentModel
                    ->where('municipality', $municipality)
                    ->countAllResults();
                
                // Simple distribution for demo - you can modify based on actual data
                $land = round($total * 0.6); // 60% land
                $buildings = round($total * 0.3); // 30% buildings  
                $machineries = $total - $land - $buildings; // remainder

                $chartData[] = [
                    'municipality' => $municipality,
                    'land' => $land,
                    'buildings' => $buildings,
                    'machineries' => max(0, $machineries)
                ];
            }

            return $this->respond([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Error fetching chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get recent assessment activities
    public function getRecentActivities($limit = 5)
    {
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$authorization) {
            $response = [
                'success' => false,
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        try {
            $recentActivities = $this->assessmentModel
                ->select('id, arpNo, ownerName, municipality, requestStatus, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->findAll();

            // Process the data
            $processedActivities = [];
            foreach ($recentActivities as $activity) {
                $status = 'Pending';
                
                // Extract status from JSON if it's JSON formatted
                if (!empty($activity['requestStatus'])) {
                    $decoded = json_decode($activity['requestStatus'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                        $status = $decoded;
                    } else if (is_string($activity['requestStatus'])) {
                        $status = $activity['requestStatus'];
                    }
                }

                $processedActivities[] = [
                    'id' => $activity['id'],
                    'arpNo' => $activity['arpNo'] ?: 'N/A',
                    'ownerName' => $activity['ownerName'] ?: 'Unknown',
                    'municipality' => $activity['municipality'] ?: 'N/A',
                    'type' => 'Assessment',
                    'status' => $status,
                    'createdAt' => $activity['created_at']
                ];
            }

            return $this->respond([
                'success' => true,
                'data' => $processedActivities
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Error fetching recent activities: ' . $e->getMessage()
            ], 500);
        }
    }
}