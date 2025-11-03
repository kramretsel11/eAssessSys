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
            // Get total RPUS count
            $totalRpus = $this->assessmentModel->countAllResults();
            
            // Get total cancelled - check if requestStatus contains cancelled
            $this->assessmentModel->resetQuery();
            $cancelledRequests = $this->assessmentModel->findAll();
            $totalCancelled = 0;
            foreach ($cancelledRequests as $request) {
                $status = $request['requestStatus'] ?? '';
                if (is_string($status) && (stripos($status, 'cancel') !== false || stripos($status, 'reject') !== false)) {
                    $totalCancelled++;
                }
            }
            
            // Get total newly declared - check if requestStatus contains new or declared
            $this->assessmentModel->resetQuery();
            $newRequests = $this->assessmentModel->findAll();
            $totalNewlyDeclared = 0;
            foreach ($newRequests as $request) {
                $status = $request['requestStatus'] ?? '';
                if (is_string($status) && (stripos($status, 'new') !== false || stripos($status, 'declared') !== false)) {
                    $totalNewlyDeclared++;
                }
            }
            
            // Total property is same as total RPUS for now
            $totalProperty = $totalRpus;

            $statistics = [
                'totalRpus' => $totalRpus,
                'totalCancelled' => $totalCancelled,
                'totalNewlyDeclared' => $totalNewlyDeclared,
                'totalProperty' => $totalProperty
            ];

            return $this->respond([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
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