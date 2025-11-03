<?php
// Simple test to check if our Transaction API endpoints work

require_once __DIR__ . '/vendor/autoload.php';

// Test the TransactionController directly
use App\Controllers\TransactionController;
use App\Models\AssessmentRequestModel;

// Initialize the model
$model = new AssessmentRequestModel();

// Get all requests to see current status
echo "Current Assessment Requests:\n";
$requests = $model->findAll();
foreach ($requests as $request) {
    echo "ID: {$request['id']}, Owner: {$request['ownerName']}, Status: {$request['requestStatus']}\n";
}

// Test updating status
echo "\nTesting status update...\n";

try {
    // Update request ID 1 to "Approved"
    $updated = $model->update(1, [
        'requestStatus' => 'Approved',
        'updated_at' => date('Y-m-d H:i:s'),
        'approvedBy' => 1,
        'approvedDate' => date('Y-m-d H:i:s')
    ]);
    
    if ($updated) {
        echo "Successfully updated request ID 1 to Approved status\n";
        
        // Verify the update
        $updatedRequest = $model->find(1);
        echo "Updated status: {$updatedRequest['requestStatus']}\n";
    } else {
        echo "Failed to update request\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>