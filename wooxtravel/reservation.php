<?php
session_start();
ob_start();

require 'includes/header.php'; 
require 'config/config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . APPURL);
    exit();
}

// Validate and get city ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: 404.php");
    exit();
}

$id = (int)$_GET['id'];

// Get trip details (now joining with cities to get city name)
$trip = $conn->prepare("
    SELECT t.id, t.price, t.start_date, c.name AS city_name 
    FROM trips t
    JOIN cities c ON t.city_id = c.id
    WHERE t.id = :id
");

$trip->execute([':id' => $id]);
$getTrip = $trip->fetch(PDO::FETCH_OBJ);

if (!$getTrip) {
    header("Location: 404.php");
    exit();
}

// Default form values
$phone_number = '';
$num_of_guests = 1;
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize inputs
    $phone_number = preg_replace('/[^0-9+]/', '', trim($_POST['phone_number']));
    $num_of_guests = (int)$_POST['num_of_guests'];
    
    // Validate phone number
    if (empty($phone_number)) {
        $errors['phone_number'] = 'Phone number is required';
    } elseif (strlen($phone_number) < 10) {
        $errors['phone_number'] = 'Phone number is too short';
    }
    
    // Validate number of guests
    if ($num_of_guests < 1 || $num_of_guests > 10) {
        $errors['num_of_guests'] = 'Number of guests must be between 1 and 10';
    }
    
    // If no errors, proceed with booking
    if (empty($errors)) {
        // $_SESSION['booking_details'] = [
        //     "phone_number" => $phone_number,
        //     "num_of_guests" => $num_of_guests,
        //     "trip_id" => $id,
        //     "city_name" => $getTrip->city_name,
        //     "price" => $getTrip->price,
        //     "total" => $num_of_guests * $getTrip->price
        // ];
        $_SESSION['booking_details'] = [
          "phone_number" => $phone_number,
          "num_of_guests" => $num_of_guests,
          "trip_id" => $id,
          "city_name" => $getTrip->city_name,
          "price" => $getTrip->price,
          "checkin_date" => $getTrip->start_date,
          "total" => $num_of_guests * $getTrip->price
      ];
      
        
        header("Location: booking-summary.php");
        exit();
    }
}
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
                <input type="text" name="phone_number" class="Number" placeholder="Ex. +xxx xxx xxx" autocomplete="on" required
                       value="<?php echo htmlspecialchars($phone_number); ?>">
              </fieldset>
            </div>

            <div class="col-lg-6">
              <fieldset>
                <label for="chooseGuests" class="form-label" style="color: black;">Number Of Guests</label>
                <select name="num_of_guests" class="form-select" aria-label="Default select example" id="chooseGuests" style="color: black;" required>
                  <option disabled <?php echo $num_of_guests === '' ? 'selected' : ''; ?>>ex. 3 or 4 or 5</option>
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($num_of_guests == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </fieldset>
            </div>

            <div class="col-lg-12">
              <fieldset>
                <label class="form-label">Destination</label>
                <input type="text" class="form-control" 
                       value="<?php echo htmlspecialchars($getTrip->city_name); ?>" readonly>
              </fieldset>
            </div>

            <div class="col-lg-6">
              <fieldset>
                <label class="form-label">Price Per Person</label>
                <input type="text" class="form-control" 
                       value="$<?php echo number_format($getTrip->price, 2); ?>" readonly>
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
