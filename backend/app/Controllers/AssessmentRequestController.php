<?php

namespace App\Controllers;

use App\Models\AssessmentRequestModel;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\AuditLogger;

class AssessmentRequestController extends ResourceController
{
    protected $model;
    protected $auditLogger;

    public function __construct()
    {
        $this->model = new AssessmentRequestModel();
        $this->auditLogger = new AuditLogger();
        helper(['url']);
    }

    // List all assessment requests
    public function getRequests()
    {
        $data = $this->model->orderBy('id', 'DESC')->findAll();

        // decode JSON fields for client convenience
        $jsonFields = ['generalDescription','structuralChecklist','additionalItems','propertyAppraisal','propertyBounderies','landAppraisal','otherImprovements','propertyAssessment','requestStatus'];
        foreach ($data as &$row) {
            foreach ($jsonFields as $f) {
                if (isset($row[$f]) && is_string($row[$f])) {
                    $decoded = json_decode($row[$f], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $row[$f] = $decoded;
                    }
                }
            }
        }

        return $this->respond(['status' => 200, 'data' => $data]);
    }

    // Get single request by id
    public function getRequest($id = null)
    {
        if (!$id) {
            return $this->respond(['status' => 400, 'message' => 'Missing id']);
        }

        $row = $this->model->find($id);
        if (!$row) {
            return $this->respond(['status' => 404, 'message' => 'Not found']);
        }

        $jsonFields = ['generalDescription','structuralChecklist','additionalItems','propertyAppraisal','propertyBounderies','landAppraisal','otherImprovements','propertyAssessment','requestStatus'];
        foreach ($jsonFields as $f) {
            if (isset($row[$f]) && is_string($row[$f])) {
                $decoded = json_decode($row[$f], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row[$f] = $decoded;
                }
            }
        }

        return $this->respond(['status' => 200, 'data' => $row]);
    }

    // Create a new assessment request (no validation per requirements)
    public function createRequest()
    {
        $req = json_decode($this->request->getBody());

        // Ensure object/array fields are stored as JSON strings
        $payload = [];
        foreach ((array)$req as $k => $v) {
            if (in_array($k, ['generalDescription','structuralChecklist','additionalItems','propertyAppraisal','propertyBounderies','landAppraisal','otherImprovements','propertyAssessment','requestStatus'])) {
                $payload[$k] = is_null($v) ? null : json_encode($v);
            } else {
                $payload[$k] = $v;
            }
        }

        $result = $this->model->insert($payload);
        
        if ($result) {
            // Log assessment request creation
            $this->auditLogger->log(
                'Assessment Request Created',
                'New assessment request created',
                $payload['userId'] ?? null,
                [
                    'request_id' => $this->model->getInsertID(),
                    'property_address' => $payload['propertyAddress'] ?? 'N/A',
                    'request_type' => $payload['requestType'] ?? 'N/A'
                ]
            );
        }
        
        return $this->respond(['status' => 200, 'message' => 'Assessment request created']);
    }

    // Update an existing assessment request
    public function updateRequest()
    {
        $req = json_decode($this->request->getBody());
        $id = $req->id ?? null;
        if (!$id) {
            return $this->respond(['status' => 400, 'message' => 'Missing id']);
        }

        $payload = [];
        foreach ((array)$req as $k => $v) {
            if ($k === 'id') continue;
            if (in_array($k, ['generalDescription','structuralChecklist','additionalItems','propertyAppraisal','propertyBounderies','landAppraisal','otherImprovements','propertyAssessment','requestStatus'])) {
                $payload[$k] = is_null($v) ? null : json_encode($v);
            } else {
                $payload[$k] = $v;
            }
        }

        $this->model->update($id, $payload);
        return $this->respond(['status' => 200, 'message' => 'Assessment request updated']);
    }

    // Update only requestStatus
    public function updateRequestStatus()
    {
        $req = json_decode($this->request->getBody());
        $id = $req->id ?? null;
        $status = $req->requestStatus ?? null;
        if (!$id) {
            return $this->respond(['status' => 400, 'message' => 'Missing id']);
        }

        // Get current request for audit logging
        $currentRequest = $this->model->find($id);
        $currentStatus = $currentRequest ? json_decode($currentRequest['requestStatus'], true) : null;

        $payload = ['requestStatus' => is_null($status) ? null : json_encode($status)];
        $result = $this->model->update($id, $payload);
        
        if ($result) {
            // Log status update
            $this->auditLogger->log(
                'Assessment Request Status Update',
                'Request status updated',
                $currentRequest['userId'] ?? null,
                [
                    'request_id' => $id,
                    'previous_status' => $currentStatus,
                    'new_status' => $status,
                    'property_address' => $currentRequest['propertyAddress'] ?? 'N/A'
                ]
            );
        }
        
        return $this->respond(['status' => 200, 'message' => 'Request status updated']);
    }

    // Track assessment request by tracking number, id, arpNo or ownerName
    public function trackRequest()
    {
        $req = json_decode($this->request->getBody());

        $tracking = $req->tracking ?? null;
        $arpNo = $req->arpNo ?? null;
        $ownerName = $req->ownerName ?? null;

        if (!$tracking && !$arpNo && !$ownerName) {
            return $this->respond(['status' => 400, 'message' => 'Provide tracking, arpNo or ownerName']);
        }

        $row = null;

        // Priority: tracking (exact match on tracking_number or id)
        if ($tracking) {
            $row = $this->model->where('tracking_number', $tracking)->first();
            if (!$row && is_numeric($tracking)) {
                $row = $this->model->find($tracking);
            }
        }

        // Next: search by ARP number (exact)
        if (!$row && $arpNo) {
            $row = $this->model->where('arpNo', $arpNo)->first();
        }

        // Next: search by owner name (partial match)
        if (!$row && $ownerName) {
            $row = $this->model->like('ownerName', $ownerName)->first();
        }

        if (!$row) {
            return $this->respond(['status' => 404, 'message' => 'Assessment request not found']);
        }

        // Decode JSON fields for client convenience
        $jsonFields = ['generalDescription','structuralChecklist','additionalItems','propertyAppraisal','propertyBounderies','landAppraisal','otherImprovements','propertyAssessment','requestStatus'];
        foreach ($jsonFields as $f) {
            if (isset($row[$f]) && is_string($row[$f])) {
                $decoded = json_decode($row[$f], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row[$f] = $decoded;
                }
            }
        }

        return $this->respond(['status' => 200, 'data' => $row]);
    }
}
