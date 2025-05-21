<?php
session_start();
require 'includes/header.php';
require 'config/config.php';

if (!isset($_SESSION['booking_details'])) {
    header("location: " . APPURL);
    exit();
}

$booking = $_SESSION['booking_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary</title>
    <style>
        .summary-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .summary-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .summary-details {
            margin-bottom: 30px;
        }
        .summary-details,
        .summary-details .detail-label,
        .summary-details span {
            color: black !important;
        }

        .summary-details .total-row span {
            font-weight: bold;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .total-row {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-back {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-pay {
            background: #003087;
            color: white;
        }
    </style>
</head>
<body>
    <div class="summary-container">
        <div class="summary-header">
            <h2>Booking Summary</h2>
            <p>Please review your reservation details before proceeding to payment</p>
        </div>
        
        <div class="summary-details">
            <div class="detail-row">
                <span class="detail-label">Destination:</span>
                <span><?php echo htmlspecialchars($booking['city_name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Check-in Date:</span>
                <span><?php echo htmlspecialchars($booking['checkin_date']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Number of Guests:</span>
                <span><?php echo htmlspecialchars($booking['num_of_guests']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Price per Guest:</span>
                <span>$<?php echo htmlspecialchars($booking['price']); ?></span>
            </div>
            <div class="detail-row total-row">
                <span class="detail-label">Total Amount:</span>
                <span>$<?php echo htmlspecialchars($booking['total']); ?></span>
            </div>
        </div>
        
        <div class="action-buttons">
        <a href="reservation.php?id=<?php echo $booking['trip_id']; ?>"class="btn btn-back">Edit Reservation</a>
            <form action="process-booking.php" method="post">
                <button type="submit" class="btn btn-pay">Proceed to Payment</button>
            </form>
        </div>
    </div>

<?php require 'includes/footer.php'; ?>