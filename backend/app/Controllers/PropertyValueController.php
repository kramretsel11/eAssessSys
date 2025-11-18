<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MarketValueModel;
use App\Models\AssessmentLevelModel;

class PropertyValueController extends Controller
{
    protected $db;
    protected $request;
    protected $marketValueModel;
    protected $assessmentLevelModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->request = service('request');
        $this->marketValueModel = new MarketValueModel();
        $this->assessmentLevelModel = new AssessmentLevelModel();
    }

    /**
     * Get market values by category and location
     */
    public function getMarketValues()
    {
        try {
            $categoryId = $this->request->getGet('categoryId');
            $municipality = $this->request->getGet('municipality');
            $kindOfLand = $this->request->getGet('kindOfLand');

            $query = $this->marketValueModel->select('tblmarket_values.*, tblcategory.name as category_name')
                ->join('tblcategory', 'tblcategory.id = tblmarket_values.categoryId');

            if ($categoryId) {
                $query->where('tblmarket_values.categoryId', $categoryId);
            }
            if ($municipality) {
                $query->where('tblmarket_values.municipality', $municipality);
            }
            if ($kindOfLand) {
                $query->where('tblmarket_values.kindOfLand', $kindOfLand);
            }

            $marketValues = $query->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $marketValues
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting market values: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve market values'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get assessment levels by category
     */
    public function getAssessmentLevels()
    {
        try {
            $categoryId = $this->request->getGet('categoryId');

            $query = $this->assessmentLevelModel->select('tblassessment_levels.*, tblcategory.name as category_name')
                ->join('tblcategory', 'tblcategory.id = tblassessment_levels.categoryId');

            if ($categoryId) {
                $query->where('tblassessment_levels.categoryId', $categoryId);
            }

            $assessmentLevels = $query->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $assessmentLevels
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting assessment levels: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve assessment levels'
            ])->setStatusCode(500);
        }
    }

    /**
     * Calculate property values automatically
     */
    public function calculatePropertyValues()
    {
        try {
            $data = $this->request->getJSON(true);
            
            $categoryId = $data['categoryId'] ?? null;
            $municipality = $data['municipality'] ?? null;
            $area = $data['area'] ?? 0;
            $kindOfLand = $data['kindOfLand'] ?? null;
            $propertyClass = $data['propertyClass'] ?? null;
            
            if (!$categoryId || !$municipality || !$area) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category, municipality, and area are required'
                ])->setStatusCode(400);
            }

            // Get market value rate
            $marketValueQuery = $this->marketValueModel
                ->where('categoryId', $categoryId)
                ->where('municipality', $municipality);
                
            if ($kindOfLand) {
                $marketValueQuery->where('kindOfLand', $kindOfLand);
            }
            
            $marketValueData = $marketValueQuery->first();
            
            if (!$marketValueData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No market value found for the specified criteria'
                ])->setStatusCode(404);
            }

            $unitPrice = $marketValueData['marketValue'];
            $marketValue = $area * $unitPrice;

            // Get assessment level
            $assessmentLevelQuery = $this->assessmentLevelModel
                ->where('categoryId', $categoryId)
                ->where('overValue <=', $marketValue)
                ->where('notOverValue >=', $marketValue)
                ->orderBy('overValue', 'DESC');
                
            $assessmentLevelData = $assessmentLevelQuery->first();
            
            if (!$assessmentLevelData) {
                // If no specific range found, get the highest range for the category
                $assessmentLevelData = $this->assessmentLevelModel
                    ->where('categoryId', $categoryId)
                    ->orderBy('overValue', 'DESC')
                    ->first();
            }
            
            $assessmentLevel = $assessmentLevelData ? $assessmentLevelData['assessmentLevel'] : 0.2; // Default 20%
            $assessedValue = $marketValue * ($assessmentLevel / 100);

            // Additional computation based on property type
            $computation = $this->generateComputationFormula($marketValueData, $assessmentLevelData, $area);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'marketValue' => round($marketValue, 2),
                    'assessedValue' => round($assessedValue, 2),
                    'unitPrice' => $unitPrice,
                    'area' => $area,
                    'assessmentLevel' => $assessmentLevel,
                    'computation' => $computation,
                    'marketValueData' => $marketValueData,
                    'assessmentLevelData' => $assessmentLevelData
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error calculating property values: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to calculate property values: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Calculate values for building/improvement properties
     */
    public function calculateBuildingValues()
    {
        try {
            $data = $this->request->getJSON(true);
            
            $categoryId = $data['categoryId'] ?? null;
            $totalFloorArea = $data['totalFloorArea'] ?? 0;
            $buildingType = $data['buildingType'] ?? null;
            $municipality = $data['municipality'] ?? null;
            
            if (!$categoryId || !$totalFloorArea) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category and total floor area are required'
                ])->setStatusCode(400);
            }

            // Get building market value rate
            $marketValueQuery = $this->marketValueModel
                ->where('categoryId', $categoryId);
                
            if ($municipality) {
                $marketValueQuery->where('municipality', $municipality);
            }
            
            if ($buildingType) {
                $marketValueQuery->where('categoryClass', $buildingType);
            }
            
            $marketValueData = $marketValueQuery->first();
            
            if (!$marketValueData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No market value found for building type'
                ])->setStatusCode(404);
            }

            $unitPrice = $marketValueData['marketValue'];
            $marketValue = $totalFloorArea * $unitPrice;

            // Get assessment level for buildings
            $assessmentLevelData = $this->assessmentLevelModel
                ->where('categoryId', $categoryId)
                ->where('overValue <=', $marketValue)
                ->where('notOverValue >=', $marketValue)
                ->first();
                
            if (!$assessmentLevelData) {
                $assessmentLevelData = $this->assessmentLevelModel
                    ->where('categoryId', $categoryId)
                    ->orderBy('overValue', 'DESC')
                    ->first();
            }
            
            $assessmentLevel = $assessmentLevelData ? $assessmentLevelData['assessmentLevel'] : 0.2;
            $assessedValue = $marketValue * ($assessmentLevel / 100);

            $computation = "Building Value = Floor Area × Unit Price\n";
            $computation .= "Market Value = {$totalFloorArea} sqm × ₱{$unitPrice} = ₱" . number_format($marketValue, 2) . "\n";
            $computation .= "Assessment Level = {$assessmentLevel}%\n";
            $computation .= "Assessed Value = ₱" . number_format($marketValue, 2) . " × {$assessmentLevel}% = ₱" . number_format($assessedValue, 2);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'marketValue' => round($marketValue, 2),
                    'assessedValue' => round($assessedValue, 2),
                    'unitPrice' => $unitPrice,
                    'totalFloorArea' => $totalFloorArea,
                    'assessmentLevel' => $assessmentLevel,
                    'computation' => $computation,
                    'marketValueData' => $marketValueData,
                    'assessmentLevelData' => $assessmentLevelData
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error calculating building values: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to calculate building values: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Calculate values for machinery/equipment
     */
    public function calculateMachineryValues()
    {
        try {
            $data = $this->request->getJSON(true);
            
            $categoryId = $data['categoryId'] ?? null;
            $acquisitionCost = $data['acquisitionCost'] ?? 0;
            $yearAcquired = $data['yearAcquired'] ?? date('Y');
            $machineryType = $data['machineryType'] ?? null;
            
            if (!$categoryId || !$acquisitionCost) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category and acquisition cost are required'
                ])->setStatusCode(400);
            }

            $currentYear = date('Y');
            $age = $currentYear - $yearAcquired;
            
            // Apply depreciation (typically 5-10% per year)
            $depreciationRate = 0.05; // 5% per year
            $maxDepreciation = 0.8; // Maximum 80% depreciation
            $totalDepreciation = min($age * $depreciationRate, $maxDepreciation);
            
            $marketValue = $acquisitionCost * (1 - $totalDepreciation);

            // Get assessment level for machinery
            $assessmentLevelData = $this->assessmentLevelModel
                ->where('categoryId', $categoryId)
                ->where('overValue <=', $marketValue)
                ->where('notOverValue >=', $marketValue)
                ->first();
                
            if (!$assessmentLevelData) {
                $assessmentLevelData = $this->assessmentLevelModel
                    ->where('categoryId', $categoryId)
                    ->orderBy('overValue', 'DESC')
                    ->first();
            }
            
            $assessmentLevel = $assessmentLevelData ? $assessmentLevelData['assessmentLevel'] : 0.8; // Default 80% for machinery
            $assessedValue = $marketValue * ($assessmentLevel / 100);

            $computation = "Machinery Valuation\n";
            $computation .= "Acquisition Cost: ₱" . number_format($acquisitionCost, 2) . "\n";
            $computation .= "Age: {$age} years\n";
            $computation .= "Depreciation: " . ($totalDepreciation * 100) . "%\n";
            $computation .= "Market Value = ₱" . number_format($acquisitionCost, 2) . " × " . (100 - ($totalDepreciation * 100)) . "% = ₱" . number_format($marketValue, 2) . "\n";
            $computation .= "Assessment Level: {$assessmentLevel}%\n";
            $computation .= "Assessed Value = ₱" . number_format($marketValue, 2) . " × {$assessmentLevel}% = ₱" . number_format($assessedValue, 2);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'marketValue' => round($marketValue, 2),
                    'assessedValue' => round($assessedValue, 2),
                    'acquisitionCost' => $acquisitionCost,
                    'age' => $age,
                    'depreciationRate' => $totalDepreciation,
                    'assessmentLevel' => $assessmentLevel,
                    'computation' => $computation,
                    'assessmentLevelData' => $assessmentLevelData
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error calculating machinery values: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to calculate machinery values: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Generate computation formula description
     */
    private function generateComputationFormula($marketValueData, $assessmentLevelData, $area)
    {
        $computation = "Property Valuation Formula:\n";
        $computation .= "Land Area: {$area} sqm\n";
        $computation .= "Unit Price: ₱" . number_format($marketValueData['marketValue'], 2) . " per sqm\n";
        $computation .= "Market Value = Area × Unit Price\n";
        $computation .= "Market Value = {$area} × ₱" . number_format($marketValueData['marketValue'], 2) . " = ₱" . number_format($area * $marketValueData['marketValue'], 2) . "\n";
        
        if ($assessmentLevelData) {
            $computation .= "Assessment Level: " . $assessmentLevelData['assessmentLevel'] . "%\n";
            $computation .= "Assessed Value = Market Value × Assessment Level\n";
            $computation .= "Assessed Value = ₱" . number_format($area * $marketValueData['marketValue'], 2) . " × " . $assessmentLevelData['assessmentLevel'] . "%";
        }
        
        return $computation;
    }
}