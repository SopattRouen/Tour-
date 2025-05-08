<?php
session_start();
ob_start();

require 'includes/header.php'; 
require 'config/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("location: " . APPURL);
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $city = $conn->prepare("SELECT * FROM cities WHERE id = :id");
    $city->execute([':id' => $id]);
    $getCity = $city->fetch(PDO::FETCH_OBJ);
    if (!$getCity) {
        header("location: 404.php");
        exit();
    }
} else {
    header("location: 404.php");
    exit();
}

if (isset($_POST['submit'])) {
    if (
        empty($_POST['phone_number']) ||
        empty($_POST['num_of_guests']) ||
        empty($_POST['checkin_date']) 
    ) {
        echo "<script>alert('Please fill all fields');</script>";
    } else {
        $phone_number = preg_replace('/[^0-9]/', '', trim($_POST['phone_number']));
        $num_of_guests = (int) trim($_POST['num_of_guests']);
        $checkin_date = trim($_POST['checkin_date']);
        $city_id = $id;
        
        // Store booking details in session for summary and API call
        $_SESSION['booking_details'] = [
            "phone_number" => $phone_number,
            "num_of_guests" => $num_of_guests,
            "checkin_date" => $checkin_date,
            "city_id" => $city_id,
            "city_name" => $getCity->name,
            "price" => $getCity->price,
            "total" => $num_of_guests * $getCity->price
        ];
        
        // Redirect to summary page instead of directly to payment
        header("location: booking-summary.php");
        exit();
    }
}

ob_end_flush();
?>

<!-- HTML content remains unchanged -->
<div class="second-page-heading">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h4>Book Prefered Deal Here</h4>
        <h2>Make Your Reservation</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt uttersi labore et dolore magna aliqua is ipsum suspendisse ultrices gravida</p>
        <div class="main-button"><a href="about.php">Discover More</a></div>
      </div>
    </div>
  </div>
</div>

<div class="more-info reservation-info">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-sm-6">
        <div class="info-item">
          <i class="fa fa-phone"></i>
          <h4>Make a Phone Call</h4>
          <a href="#">+123 456 789 (0)</a>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="info-item">
          <i class="fa fa-envelope"></i>
          <h4>Contact Us via Email</h4>
          <a href="#">company@email.com</a>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="info-item">
          <i class="fa fa-map-marker"></i>
          <h4>Visit Our Offices</h4>
          <a href="#">24th Street North Avenue London, UK</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="reservation-form">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <form id="reservation-form" method="POST" role="search" action="reservation.php?id=<?php echo $id; ?>">
          <div class="row">
            <div class="col-lg-12">
              <h4>Make Your <em>Reservation</em> Through This <em>Form</em></h4>
            </div>
            <div class="col-lg-6">
              <fieldset>
                <label for="Number" class="form-label">Your Phone Number</label>
                <input type="text" name="phone_number" class="Number" placeholder="Ex. +xxx xxx xxx" autocomplete="on" required>
              </fieldset>
            </div>
            <div class="col-lg-6">
                <fieldset>
                  <label for="chooseGuests" class="form-label" style="color: black;">Number Of Guests</label>
                  <select name="num_of_guests" class="form-select" aria-label="Default select example" id="chooseGuests" style="color: black;" required>
                    <option selected disabled>ex. 3 or 4 or 5</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                  </select>
                </fieldset>
            </div>
            <div class="col-lg-6">
              <fieldset>
                <label for="Number" class="form-label">Check In Date</label>
                <input type="date" name="checkin_date" class="date" required>
              </fieldset>
            </div>
            <div class="col-lg-12">
              <fieldset>
                <label class="form-label">Destination</label>
                <input type="text" value="<?php echo htmlspecialchars($getCity->name); ?>" class="form-control" readonly>
              </fieldset>
            </div>

            <div class="col-lg-12">                        
              <fieldset>
                <button name="submit" type="submit" class="main-button">Make Your Reservation and Pay Now</button>
              </fieldset>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
