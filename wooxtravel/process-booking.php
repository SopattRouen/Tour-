<?php
session_start();
require 'config/config.php';

if (!isset($_SESSION['booking_details'])) {
    header("location: " . APPURL);
    exit();
}

$booking = $_SESSION['booking_details'];

try {
    // Prepare API payload
    $payload = [
        "phone_number" => $booking['phone_number'],
        "num_of_guests" => $booking['num_of_guests'],
        "checkin_date" => $booking['checkin_date'],
        "city_id" => $booking['city_id']
    ];

    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://host.docker.internal:8000/api/admin/bookings");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer " . $_SESSION['jwt_token']
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode == 200 || $httpCode == 201) {
        $_SESSION['payment'] = $booking['total'];
        header("location: pay.php");
        exit();
    } else {
        $_SESSION['error'] = 'Failed to make reservation: ' . ($result['message'] ?? 'Unknown error');
        header("location: reservation.php?id=" . $booking['city_id']);
        exit();
    }
} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    $_SESSION['error'] = 'Something went wrong';
    header("location: reservation.php?id=" . $booking['city_id']);
    exit();
}