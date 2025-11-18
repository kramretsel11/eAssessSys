<?php

namespace App\Libraries;

use App\Models\AuditLogModel;
use CodeIgniter\HTTP\RequestInterface;

class AuditLogger
{
    protected $auditModel;
    
    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
    }
    
    /**
     * Log an activity
     */
    public function log($userId, $action, $resourceType, $resourceId = null, $details = '', RequestInterface $request = null)
    {
        try {
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
            
            return $this->auditModel->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log audit trail: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log authentication events
     */
    public function logAuth($userId, $action, $details = '', RequestInterface $request = null)
    {
        return $this->log($userId, $action, 'Authentication', null, $details, $request);
    }
    
    /**
     * Log assessment request events
     */
    public function logAssessmentRequest($userId, $action, $requestId, $details = '', RequestInterface $request = null)
    {
        return $this->log($userId, $action, 'Assessment Request', $requestId, $details, $request);
    }
    
    /**
     * Log certificate events
     */
    public function logCertificate($userId, $action, $certificateId, $details = '', RequestInterface $request = null)
    {
        return $this->log($userId, $action, 'Certificate', $certificateId, $details, $request);
    }
    
    /**
     * Log configuration events
     */
    public function logConfiguration($userId, $action, $configType, $configId = null, $details = '', RequestInterface $request = null)
    {
        return $this->log($userId, $action, $configType, $configId, $details, $request);
    }
    
    /**
     * Log report events
     */
    public function logReport($userId, $action, $details = '', RequestInterface $request = null)
    {
        return $this->log($userId, $action, 'Report', null, $details, $request);
    }
    
    /**
     * Log system events
     */
    public function logSystem($userId, $action, $details = '', RequestInterface $request = null)
    {
        return $this->log($userId, $action, 'System', null, $details, $request);
    }
}