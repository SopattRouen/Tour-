<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../includes/header.php';
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . APPURL);
    exit();
}

// Check if ID is provided via URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://host.docker.internal:8000/api/admin/bookings/getByUser/$userId?order=asc");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $_SESSION['jwt_token'],
        "Accept: application/json"
    ]);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $responseData = json_decode($apiResponse, true);
        $AllUserBookings = $responseData['data'] ?? [];
         // Sort by created_at or checkin_date descending
    } else {
        echo "<script>alert('Failed to load bookings');</script>";
        $AllUserBookings = [];
    }
} else {
    header("Location: 404.php");
    exit();
}
?>

<div class="container text-white">
    <div class="row">
        <div class="col-md-12">
            <table class="table text-white" style="margin-top: 150px; margin-bottom:100px;">
                <thead>
                    <tr>
                        <th scope="col">N.o</th>
                        <th scope="col">Reciept Number</th>
                        <th scope="col">Name</th>
                        <th scope="col">Guests</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Booked At</th>
                        <th scope="col">Check-in Date</th>
                        <th scope="col">Destination</th>
                        <th scope="col">Trip Days</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($AllUserBookings as $index => $booking): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td> <!-- N.o -->
                        <td><?php echo htmlspecialchars($booking['receipt_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['num_of_guests']); ?></td>
                        <td><?php echo htmlspecialchars($booking['phone_number']); ?></td>
                        <td><?php echo (new DateTime($booking['booked_at']))->format('Y-m-d'); ?></td>
                        <td><?php echo (new DateTime($booking['checkin_date']))->format('Y-m-d'); ?></td>
                        <td><?php echo htmlspecialchars($booking['city_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['trip_days']); ?></td>
                        <td>$<?php echo htmlspecialchars($booking['price']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
