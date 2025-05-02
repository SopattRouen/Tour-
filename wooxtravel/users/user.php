<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../includes/header.php';
require '../config/config.php';

// Redirect to homepage if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . APPURL . "");
    exit();
}
       
// Validate the presence of 'id' in URL and fetch bookings for the user
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $AllUserBookings = $stmt->fetchAll(PDO::FETCH_OBJ);
} else {
    // Redirect to 404 if 'id' is not present or invalid
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
                        <th scope="col">Name</th>
                        <th scope="col">Number of Guests</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Checkin_date</th>
                        <th scope="col">Destination</th>
                        <th scope="col">Status</th>
                        <th scope="col">Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($AllUserBookings as $Booking) : ?>
                        <tr>
                            <td><?php echo $Booking->name; ?></td>
                            <td><?php echo $Booking->num_of_guests; ?></td>
                            <td><?php echo $Booking->phone_number; ?></td>
                            <td><?php echo (new DateTime($Booking->checkin_date))->format('Y-m-d'); ?></td>
                            <td><?php echo $Booking->destination; ?></td>
                            <td><?php echo $Booking->status; ?></td>
                            <td>$<?php echo $Booking->payment; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
