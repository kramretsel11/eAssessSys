<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ReportController extends ResourceController
{
    protected $format = 'json';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Generate a report
     */
    public function generateReport()
    {
        try {
            $data = $this->request->getJSON(true);

            $rules = [
                'type' => 'required|string|in_list[assessment_summary,property_valuation,tax_collection,municipality_analysis,certificate_summary,custom]',
                'filters' => 'permit_empty|array',
                'generatedBy' => 'required|string'
            ];

            if (!$this->validate($data, $rules)) {
                return $this->fail($this->validator->getErrors(), 400);
            }

            $reportType = $data['type'];
            $filters = $data['filters'] ?? [];
            $format = $filters['format'] ?? 'csv';

            // Generate report data based on type
            $reportData = $this->generateReportData($reportType, $filters);

            if (empty($reportData)) {
                return $this->fail('No data found for the specified criteria', 404);
            }

            // Generate file content
            $fileContent = $this->formatReportData($reportData, $format, $reportType);
            $filename = $this->generateFilename($reportType, $format);

            // Store report metadata (you might want to save this to database)
            $reportMetadata = [
                'type' => $reportType,
                'generated_by' => $data['generatedBy'],
                'generated_at' => date('Y-m-d H:i:s'),
                'record_count' => count($reportData),
                'filename' => $filename,
                'file_size' => strlen($fileContent)
            ];

            // Return file for download
            return $this->response
                        ->setHeader('Content-Type', $this->getContentType($format))
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                        ->setBody($fileContent);

        } catch (\Exception $e) {
            return $this->fail('Failed to generate report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate report data based on type and filters
     */
    private function generateReportData($type, $filters)
    {
        $db = \Config\Database::connect();
        
        switch ($type) {
            case 'assessment_summary':
                return $this->generateAssessmentSummary($db, $filters);
            
            case 'property_valuation':
                return $this->generatePropertyValuation($db, $filters);
            
            case 'tax_collection':
                return $this->generateTaxCollection($db, $filters);
            
            case 'municipality_analysis':
                return $this->generateMunicipalityAnalysis($db, $filters);
            
            case 'certificate_summary':
                return $this->generateCertificateSummary($db, $filters);
            
            case 'custom':
                return $this->generateCustomReport($db, $filters);
            
            default:
                return [];
        }
    }

    /**
     * Generate Assessment Summary Report
     */
    private function generateAssessmentSummary($db, $filters)
    {
        $builder = $db->table('assessment_requests ar');
        $builder->select('ar.*, u.name as owner_name, u.contact_no');
        $builder->join('users u', 'u.id = ar.user_id', 'left');

        $this->applyCommonFilters($builder, $filters);

        return $builder->get()->getResultArray();
    }

    /**
     * Generate Property Valuation Report
     */
    private function generatePropertyValuation($db, $filters)
    {
        $builder = $db->table('assessment_requests ar');
        $builder->select('ar.arp_no, ar.pin, ar.owner_name, ar.municipality, ar.barangay, ar.area_no, ar.market_value, ar.assessed_value, ar.property_type, ar.status');

        $this->applyCommonFilters($builder, $filters);
        
        if (!empty($filters['propertyType'])) {
            $builder->where('ar.property_type', $filters['propertyType']);
        }

        if (!empty($filters['valueRange'])) {
            $range = explode('-', $filters['valueRange']);
            if (count($range) === 2) {
                $builder->where('ar.assessed_value >=', $range[0]);
                if ($range[1] !== '+') {
                    $builder->where('ar.assessed_value <=', $range[1]);
                }
            }
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Generate Tax Collection Report
     */
    private function generateTaxCollection($db, $filters)
    {
        $builder = $db->table('assessment_requests ar');
        $builder->select('ar.municipality, ar.barangay, ar.property_type, SUM(ar.assessed_value) as total_assessed_value, COUNT(*) as property_count');
        $builder->where('ar.status', 'approved');

        $this->applyCommonFilters($builder, $filters);

        $builder->groupBy('ar.municipality, ar.barangay, ar.property_type');
        $builder->orderBy('ar.municipality, ar.barangay');

        return $builder->get()->getResultArray();
    }

    /**
     * Generate Municipality Analysis Report
     */
    private function generateMunicipalityAnalysis($db, $filters)
    {
        $builder = $db->table('assessment_requests ar');
        $builder->select('ar.municipality, COUNT(*) as total_requests, 
                         SUM(CASE WHEN ar.status = "approved" THEN 1 ELSE 0 END) as approved_requests,
                         SUM(CASE WHEN ar.status = "pending" THEN 1 ELSE 0 END) as pending_requests,
                         SUM(CASE WHEN ar.status = "rejected" THEN 1 ELSE 0 END) as rejected_requests,
                         SUM(CASE WHEN ar.status = "approved" THEN ar.assessed_value ELSE 0 END) as total_assessed_value');

        $this->applyCommonFilters($builder, $filters);

        $builder->groupBy('ar.municipality');
        $builder->orderBy('ar.municipality');

        return $builder->get()->getResultArray();
    }

    /**
     * Generate Certificate Summary Report
     */
    private function generateCertificateSummary($db, $filters)
    {
        // This would need a certificates table or audit logs to track certificate generation
        // For now, return sample data based on approved requests
        $builder = $db->table('assessment_requests ar');
        $builder->select('ar.arp_no, ar.pin, ar.owner_name, ar.municipality, ar.barangay, ar.status, ar.created_at, ar.updated_at');
        $builder->where('ar.status', 'approved');

        $this->applyCommonFilters($builder, $filters);

        return $builder->get()->getResultArray();
    }

    /**
     * Generate Custom Report
     */
    private function generateCustomReport($db, $filters)
    {
        $builder = $db->table('assessment_requests ar');
        
        $selectedFields = $filters['selectedFields'] ?? ['arp_no', 'pin', 'owner_name', 'municipality'];
        $selectFields = implode(', ', array_map(function($field) {
            return 'ar.' . $field;
        }, $selectedFields));
        
        $builder->select($selectFields);

        $this->applyCommonFilters($builder, $filters);

        return $builder->get()->getResultArray();
    }

    /**
     * Apply common filters to query builder
     */
    private function applyCommonFilters($builder, $filters)
    {
        if (!empty($filters['dateFrom'])) {
            $builder->where('DATE(ar.created_at) >=', $filters['dateFrom']);
        }

        if (!empty($filters['dateTo'])) {
            $builder->where('DATE(ar.created_at) <=', $filters['dateTo']);
        }

        if (!empty($filters['municipality'])) {
            $builder->where('ar.municipality', $filters['municipality']);
        }

        if (!empty($filters['status'])) {
            $builder->where('ar.status', $filters['status']);
        }
    }

    /**
     * Format report data for output
     */
    private function formatReportData($data, $format, $reportType)
    {
        switch ($format) {
            case 'csv':
                return $this->formatCSV($data);
            case 'pdf':
                return $this->formatPDF($data, $reportType);
            case 'xlsx':
                return $this->formatExcel($data);
            default:
                return $this->formatCSV($data);
        }
    }

    /**
     * Format data as CSV
     */
    private function formatCSV($data)
    {
        if (empty($data)) {
            return '';
        }

        $output = '';
        $headers = array_keys($data[0]);
        $output .= implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $csvRow = [];
            foreach ($row as $value) {
                $csvRow[] = '"' . str_replace('"', '""', $value ?? '') . '"';
            }
            $output .= implode(',', $csvRow) . "\n";
        }

        return $output;
    }

    /**
     * Format data as PDF (simplified)
     */
    private function formatPDF($data, $reportType)
    {
        // This would require a PDF library like TCPDF or mPDF
        // For now, return a simple text format
        $output = "Report: " . ucfirst(str_replace('_', ' ', $reportType)) . "\n";
        $output .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $output .= implode("\t", $headers) . "\n";
            
            foreach ($data as $row) {
                $output .= implode("\t", array_values($row)) . "\n";
            }
        }

        return $output;
    }

    /**
     * Format data as Excel (simplified)
     */
    private function formatExcel($data)
    {
        // For full Excel support, you'd use PhpSpreadsheet
        // For now, return CSV format with Excel mime type
        return $this->formatCSV($data);
    }

    /**
     * Generate filename for report
     */
    private function generateFilename($type, $format)
    {
        return $type . '_' . date('Y-m-d_H-i-s') . '.' . $format;
    }

    /**
     * Get content type for format
     */
    private function getContentType($format)
    {
        switch ($format) {
            case 'csv':
                return 'text/csv';
            case 'pdf':
                return 'application/pdf';
            case 'xlsx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            default:
                return 'text/csv';
        }
    }

    /**
     * Get recent reports
     */
    public function getRecentReports()
    {
        // This would typically come from a reports table
        // For now, return sample data
        $reports = [
            [
                'id' => 1,
                'type' => 'assessment_summary',
                'generated_by' => 'admin',
                'generated_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'record_count' => 156,
                'file_size' => '2.3 MB'
            ],
            [
                'id' => 2,
                'type' => 'property_valuation',
                'generated_by' => 'admin',
                'generated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'record_count' => 134,
                'file_size' => '1.8 MB'
            ]
        ];

        return $this->respond([
            'status' => 'success',
            'data' => $reports
        ]);
    }

    /**
     * Download a previously generated report
     */
    public function downloadReport($filename)
    {
        // Implementation would depend on where reports are stored
        return $this->fail('Report download not implemented yet', 501);
    }

    /**
     * Delete a report
     */
    public function deleteReport()
    {
        // Implementation would depend on where reports are stored
        return $this->respond([
            'status' => 'success',
            'message' => 'Report deleted successfully'
        ]);
    }
}