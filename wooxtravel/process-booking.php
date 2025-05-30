<?php
session_start();
ob_start(); // Start output buffering to prevent header errors
require 'config/config.php';

// Check if booking details exist in session
if (!isset($_SESSION['booking_details'])) {
    header("Location: " . APPURL);
    exit();
}

// No need to process POST data again - just redirect to payment
$_SESSION['payment'] = $_SESSION['booking_details']['total'];
header("Location: pay.php");
exit();
//