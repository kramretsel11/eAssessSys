<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssessmentRequestModel;
use TCPDF;

class CertificateController extends BaseController
{
    protected $assessmentRequestModel;
    
    public function __construct()
    {
        $this->assessmentRequestModel = new AssessmentRequestModel();
    }
    
    /**
     * Generate Property Ownership Certificate
     */
    public function generateOwnershipCertificate($requestId)
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
            // Get the assessment request details
            $request = $this->assessmentRequestModel->find($requestId);
            
            if (!$request) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Assessment request not found'
                ]);
            }
            
            // Check if request is approved
            if ($request['requestStatus'] !== 'Approved') {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Certificate can only be generated for approved requests'
                ]);
            }
            
            // Generate the certificate PDF
            $pdf = $this->createOwnershipCertificate($request);
            
            // Set headers for PDF download
            $filename = 'Property_Ownership_Certificate_' . $requestId . '_' . date('Y-m-d') . '.pdf';
            
            return $this->response
                ->setContentType('application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($pdf);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error generating certificate: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate Tax Declaration Certificate
     */
    public function generateTaxDeclarationCertificate($requestId)
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
            // Get the assessment request details
            $request = $this->assessmentRequestModel->find($requestId);
            
            if (!$request) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Assessment request not found'
                ]);
            }
            
            // Check if request is approved
            if ($request['requestStatus'] !== 'Approved') {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Certificate can only be generated for approved requests'
                ]);
            }
            
            // Generate the certificate PDF
            $pdf = $this->createTaxDeclarationCertificate($request);
            
            // Set headers for PDF download
            $filename = 'Tax_Declaration_Certificate_' . $requestId . '_' . date('Y-m-d') . '.pdf';
            
            return $this->response
                ->setContentType('application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($pdf);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error generating certificate: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create Property Ownership Certificate PDF
     */
    private function createOwnershipCertificate($request)
    {
        // Create new PDF document in LANDSCAPE orientation
        $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Aurora Provincial Government');
        $pdf->SetAuthor('Office of the Provincial Assessor');
        $pdf->SetTitle('Property Ownership Certification');
        $pdf->SetSubject('Property Ownership Certificate');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins for landscape
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('times', '', 10);
        
        // Header content
        $html = $this->getOwnershipCertificateHTML($request);
        
        // Print the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        return $pdf->Output('', 'S');
    }
    
    /**
     * Create Tax Declaration Certificate PDF
     */
    private function createTaxDeclarationCertificate($request)
    {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Aurora Provincial Government');
        $pdf->SetAuthor('Office of the Provincial Assessor');
        $pdf->SetTitle('Tax Declaration Certification');
        $pdf->SetSubject('Tax Declaration Certificate');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('times', '', 12);
        
        // Header content
        $html = $this->getTaxDeclarationCertificateHTML($request);
        
        // Print the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        return $pdf->Output('', 'S');
    }
    
    /**
     * Get HTML content for Property Ownership Certificate
     */
    private function getOwnershipCertificateHTML($request)
    {
        $currentDate = date('jS');
        $currentMonth = date('F');
        $currentYear = date('Y');
        
        // Extract kind of building from generalDescription JSON
        $kindOfProperty = $this->extractKindOfProperty($request['generalDescription']);
        
        return '
        <style>
            .header { text-align: center; margin-bottom: 25px; }
            .title { font-size: 16px; font-weight: bold; margin: 8px 0; }
            .subtitle { font-size: 12px; margin: 4px 0; }
            .content { margin: 15px 0; line-height: 1.5; }
            .property-table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 15px 0; 
                font-size: 9px; 
                table-layout: fixed;
            }
            .property-table th, .property-table td { 
                border: 1px solid #000; 
                padding: 6px; 
                text-align: center; 
                vertical-align: middle; 
                word-wrap: break-word;
                overflow: hidden;
            }
            .property-table th { 
                background-color: #f0f0f0; 
                font-weight: bold; 
                font-size: 8px; 
            }
            .signature-section { margin-top: 30px; }
            .signature-line { border-bottom: 1px solid #000; width: 200px; display: inline-block; }
            .footer-info { margin-top: 25px; font-size: 9px; }
            .two-column { display: table; width: 100%; }
            .left-column { display: table-cell; width: 50%; padding-right: 20px; }
            .right-column { display: table-cell; width: 50%; vertical-align: top; }
        </style>
        
        <div class="header">
            <div style="font-size: 12px; margin-bottom: 8px;">Republic of the Philippines</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 4px;">PROVINCIAL GOVERNMENT OF AURORA</div>
            <div style="font-size: 12px; margin-bottom: 15px;">BALER</div>
            <div style="font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 25px;">OFFICE OF THE PROVINCIAL ASSESSOR</div>
            <div class="title" style="letter-spacing: 3px; margin-top: 25px;">C E R T I F I C A T I O N</div>
        </div>
        
        <div class="content">
            <p style="margin-bottom: 15px;"><strong>TO WHOM IT MAY CONCERN:</strong></p>
            
            <p style="text-indent: 30px; margin-bottom: 15px; font-size: 11px;">
                THIS IS TO CERTIFY that, according to the records filed in this office, 
                <strong style="text-decoration: underline;">' . htmlspecialchars($request['ownerName']) . '</strong>, 
                resident/s of <strong style="text-decoration: underline;">' . htmlspecialchars($request['ownerAddress']) . '</strong>, is the 
                declared owner of the hereunder listed property in this province, more particularly described as follows:
            </p>
            
            <table class="property-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Tax Dec No.</th>
                        <th style="width: 8%;">Lot No.</th>
                        <th style="width: 10%;">Title No.</th>
                        <th style="width: 20%;">Location of Property</th>
                        <th style="width: 8%;">Area (sq.m.)</th>
                        <th style="width: 12%;">Kind of Property</th>
                        <th style="width: 12%;">Improvements</th>
                        <th style="width: 10%;">Market Value (₱)</th>
                        <th style="width: 10%;">Assessed Value (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-size: 8px;">' . htmlspecialchars($request['arpNo'] ?? '') . '</td>
                        <td style="font-size: 8px;">' . htmlspecialchars($request['lotNo'] ?? '') . '</td>
                        <td style="font-size: 8px;">' . htmlspecialchars($request['octTctCloaNo'] ?? '') . '</td>
                        <td style="font-size: 8px; text-align: left; padding: 4px;">' . htmlspecialchars($request['street'] . ', ' . $request['barangay'] . ', ' . $request['municipality']) . '</td>
                        <td style="font-size: 8px;">' . htmlspecialchars($request['areaNo'] ?? '0') . '</td>
                        <td style="font-size: 8px;">' . htmlspecialchars($kindOfProperty) . '</td>
                        <td style="font-size: 8px;">' . htmlspecialchars($request['otherImprovements'] ?? 'N/A') . '</td>
                        <td style="font-size: 8px;">' . number_format(0, 2) . '</td>
                        <td style="font-size: 8px;">' . number_format(0, 2) . '</td>
                    </tr>
                </tbody>
            </table>
            
            <p style="text-indent: 30px; margin: 25px 0; font-size: 11px;">
                Issued this <strong style="text-decoration: underline;">' . $currentDate . '</strong> day of 
                <strong style="text-decoration: underline;">' . $currentMonth . '</strong>, ' . $currentYear . ' at Baler Aurora upon request of the interested party for all legal purposes it may serve.
            </p>
        </div>
        
        <div class="two-column" style="margin-top: 40px;">
            <div class="left-column">
                <div class="footer-info">
                    <div style="font-size: 10px;">
                        <div>Paid Under O.R NO.: ___________</div>
                        <div>Issued on: ___________________</div>
                        <div>Issued at: ___________________</div>
                    </div>
                </div>
            </div>
            <div class="right-column">
                <div class="signature-section" style="text-align: center;">
                    <div style="margin-bottom: 50px;"></div>
                    <div style="font-weight: bold; font-size: 11px;">JOSE ANGELITO Q. HERNANDEZ, REA</div>
                    <div style="margin-top: 5px; font-size: 10px;">Acting Provincial Assessor</div>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Get HTML content for Tax Declaration Certificate
     */
    private function getTaxDeclarationCertificateHTML($request)
    {
        $currentDate = date('jS');
        $currentMonth = date('F');
        $currentYear = date('Y');
        
        // Extract kind of building from generalDescription JSON
        $kindOfProperty = $this->extractKindOfProperty($request['generalDescription']);
        
        return '
        <style>
            .header { text-align: center; margin-bottom: 30px; }
            .title { font-size: 18px; font-weight: bold; margin: 10px 0; }
            .content { margin: 20px 0; line-height: 1.6; text-align: justify; }
            .signature-section { margin-top: 40px; }
            .footer-info { margin-top: 30px; font-size: 10px; }
            .two-column { display: table; width: 100%; margin-top: 40px; }
            .left-column { display: table-cell; width: 50%; padding-right: 20px; vertical-align: top; }
            .right-column { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
        </style>
        
        <div class="header">
            <div style="font-size: 14px; margin-bottom: 10px;">Republic of the Philippines</div>
            <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">PROVINCIAL GOVERNMENT OF AURORA</div>
            <div style="font-size: 14px; margin-bottom: 20px;">BALER</div>
            <div style="font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 30px;">OFFICE OF THE PROVINCIAL ASSESSOR</div>
            <div class="title" style="letter-spacing: 3px; margin-top: 30px;">C E R T I F I C A T I O N</div>
        </div>
        
        <div class="content">
            <p style="margin-bottom: 20px;"><strong>TO WHOM IT MAY CONCERN:</strong></p>
            
            <p style="text-indent: 30px; margin-bottom: 20px;">
                THIS IS TO CERTIFY that, Tax Declaration No. 
                <strong style="text-decoration: underline;">' . htmlspecialchars($request['arpNo'] ?? 'TBD-' . date('Y') . '-' . str_pad($request['id'], 6, '0', STR_PAD_LEFT)) . '</strong> 
                declared in the name of <strong style="text-decoration: underline;">' . htmlspecialchars($request['ownerName']) . '</strong>, 
                located at <strong style="text-decoration: underline;">' . htmlspecialchars($request['street'] . ', ' . $request['barangay'] . ', ' . $request['municipality']) . '</strong> 
                containing an area of <strong style="text-decoration: underline;">' . htmlspecialchars($request['areaNo'] ?? '0') . '</strong> sq.m., 
                and tax begin with the year <strong style="text-decoration: underline;">' . $currentYear . '</strong>.
            </p>
            
            <p style="text-indent: 30px; margin-bottom: 20px;">
                Further certify that there is <strong style="text-decoration: underline;">no</strong> (house/building) erected on the said 
                <strong style="text-decoration: underline;">' . htmlspecialchars($kindOfProperty) . '</strong> as per available records filed in this office.
            </p>
            
            <p style="text-indent: 30px; margin-bottom: 20px;">
                This certification has been issued upon the request of the interested party for whatever legal intent and purposes it may serve.
            </p>
            
            <p style="text-indent: 30px; margin: 30px 0;">
                Issued this <strong style="text-decoration: underline;">' . $currentDate . '</strong> day of 
                <strong style="text-decoration: underline;">' . $currentMonth . '</strong>, ' . $currentYear . ' at Provincial Assessor\'s office, Capitol 
                Bldg. Baler Aurora.
            </p>
        </div>
        
        <div class="two-column">
            <div class="left-column">
                <div class="footer-info">
                    <div>Paid Under O.R NO.: ___________</div>
                    <div>Issued on: ___________________</div>
                    <div>Issued at: ___________________</div>
                    <div>Verified by: __________________</div>
                    <div>Control No.: __________________</div>
                </div>
            </div>
            <div class="right-column">
                <div class="signature-section">
                    <div style="margin-bottom: 60px;"></div>
                    <div style="font-weight: bold;">JOSE ANGELITO Q. HERNANDEZ, REA</div>
                    <div style="margin-top: 5px;">Acting Provincial Assessor</div>
                </div>
            </div>
        </div>';
    }
    
    /**
     * List available certificates for a request
     */
    public function getAvailableCertificates($requestId)
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
            $request = $this->assessmentRequestModel->find($requestId);
            
            if (!$request) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Assessment request not found'
                ]);
            }
            
            $certificates = [];
            
            if ($request['requestStatus'] === 'Approved') {
                $certificates = [
                    [
                        'type' => 'ownership',
                        'name' => 'Property Ownership Certificate',
                        'description' => 'Official certification of property ownership',
                        'url' => base_url('api/v1/certificates/ownership/' . $requestId)
                    ],
                    [
                        'type' => 'tax_declaration',
                        'name' => 'Tax Declaration Certificate',
                        'description' => 'Official tax declaration certification',
                        'url' => base_url('api/v1/certificates/tax-declaration/' . $requestId)
                    ]
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'request_id' => $requestId,
                    'status' => $request['requestStatus'],
                    'certificates' => $certificates,
                    'can_generate' => $request['requestStatus'] === 'Approved'
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error fetching certificates: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Extract kind of property from generalDescription JSON
     */
    private function extractKindOfProperty($generalDescription)
    {
        // Default value
        $defaultKind = 'Land';
        
        // If it's already a simple string, return it
        if (is_string($generalDescription) && !$this->isJson($generalDescription)) {
            return $generalDescription ?: $defaultKind;
        }
        
        // Try to parse JSON
        if (is_string($generalDescription)) {
            $decoded = json_decode($generalDescription, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded['kindOfBldg'] ?? $defaultKind;
            }
        }
        
        // If it's already an array
        if (is_array($generalDescription)) {
            return $generalDescription['kindOfBldg'] ?? $defaultKind;
        }
        
        return $defaultKind;
    }
    
    /**
     * Check if string is JSON
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}