<?php
session_start();
require 'config/config.php';

header('Content-Type: application/json');

// Verify required data
if (!isset($_SESSION['jwt_token']) || !isset($_SESSION['booking_details']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Get the POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['orderData']) || !isset($data['bookingDetails'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

// Prepare the payload for the API
$payload = [
    'trip_id' => $data['bookingDetails']['trip_id'],
    'user_id' => $data['userId'],
    'phone_number' => $data['bookingDetails']['phone_number'],
    'num_of_guests' => $data['bookingDetails']['num_of_guests'],
    'total_price' => $data['bookingDetails']['total'],
    'payment_id' => $data['orderData']['id'],
    'payment_status' => $data['orderData']['status'],
    'payment_amount' => $data['orderData']['purchase_units'][0]['amount']['value'],
    'payment_currency' => $data['orderData']['purchase_units'][0]['amount']['currency_code']
];

// Initialize cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://host.docker.internal:8000/api/admin/bookings");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/x-www-form-urlencoded",
    "Authorization: Bearer " . $_SESSION['jwt_token']
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'message' => 'API connection error: ' . curl_error($ch)]);
    curl_close($ch);
    exit();
}

curl_close($ch);

// Process the API response
$responseData = json_decode($response, true);
$success = ($httpCode >= 200 && $httpCode < 300);

if ($success) {
    unset($_SESSION['booking_details']);
    unset($_SESSION['payment']);
    
    echo json_encode([
        'success' => true,
        'message' => $responseData['message'] ?? 'Booking successful'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $responseData['message'] ?? 'Booking failed',
        'trip_id' => $payload['trip_id']
    ]);
}