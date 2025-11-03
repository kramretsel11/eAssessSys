<?php

// Test API endpoints manually

$baseUrl = 'http://localhost:8080/e_assessment/api/v1';

// Test quick stats endpoint
$quickStatsUrl = $baseUrl . '/transactions/quick-stats';

echo "Testing Quick Stats API...\n";
echo "URL: $quickStatsUrl\n\n";

// Initialize curl
$ch = curl_init();

// Set options
curl_setopt($ch, CURLOPT_URL, $quickStatsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Add basic auth header (you may need to add proper auth)
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer test-token' // Replace with actual token
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_error($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n";
}

curl_close($ch);

// Test status endpoint
echo "\n\nTesting Status API...\n";
$statusUrl = $baseUrl . '/transactions/status/pending';
echo "URL: $statusUrl\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $statusUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n";
}

curl_close($ch);

echo "\nTest completed.\n";
?>