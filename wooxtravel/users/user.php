<?php
    ob_start();
    require '../includes/header.php';
    require '../config/config.php';

    if (isset($_SESSION['email'])) {
        header("location: ".APPURL."");
        exit(); // Stop execution after redirect
    }
    if(isset($_GET['id '])) {
        $id = $_GET['id'];
        $user_bookings = $conn->query ("SELECT * FROM bookings WHERE user_id='$id'"); $user_bookings->execute();
        $AllUserBookings = $user_bookings->fetchAll (PDO:: FETCH_OBJ);
        // var_dump ($user_bookings);
    }else {
        header ("location: 404.php");
        exit();
    }
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <table class="table" style="margin-top: 150px; color: var(--text-color); margin-bottom:100px">
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
                    <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    :root {
        --text-color: #000; /* Default to black */
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --text-color: #fff; /* White for dark mode */
        }
    }
</style>
<?php require '../includes/footer.php'; ?>