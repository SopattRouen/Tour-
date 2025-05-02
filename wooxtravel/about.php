<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require 'config/config.php';
require 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: 404.php");
  exit();
}

$id = intval($_GET['id']);

try {
  // Get country info
  $stmt = $conn->prepare("SELECT * FROM countries WHERE id = :id");
  $stmt->execute(['id' => $id]);
  $singleCountry = $stmt->fetch(PDO::FETCH_OBJ);

  if (!$singleCountry) {
    header("Location: 404.php");
    exit();
  }

  // Get city images
  $stmt = $conn->prepare("SELECT * FROM cities WHERE country_id = :id");
  $stmt->execute(['id' => $id]);
  $singleImage = $stmt->fetchAll(PDO::FETCH_OBJ);

  // Get all cities with booking count
  $stmt = $conn->prepare("
    SELECT 
      cities.id AS id, cities.name AS name, cities.image AS image,
      cities.trip_days AS trip_days, cities.price AS price,
      COUNT(bookings.city_id) AS count_bookings 
    FROM cities 
    LEFT JOIN bookings ON cities.id = bookings.city_id 
    WHERE cities.country_id = :id 
    GROUP BY cities.id, cities.name, cities.image, cities.trip_days, cities.price
  ");
  $stmt->execute(['id' => $id]);
  $allCities = $stmt->fetchAll(PDO::FETCH_OBJ);

  // Count of cities for the country
  $stmt = $conn->prepare("SELECT COUNT(country_id) AS num_city FROM cities WHERE country_id = :id");
  $stmt->execute(['id' => $id]);
  $num_cities = $stmt->fetch(PDO::FETCH_OBJ);

  // Total bookings for the country
  $stmt = $conn->prepare("
    SELECT COUNT(bookings.city_id) AS count_bookings
    FROM cities 
    JOIN bookings ON cities.id = bookings.city_id 
    WHERE cities.country_id = :id
  ");
  $stmt->execute(['id' => $id]);
  $num_bookings = $stmt->fetch(PDO::FETCH_OBJ);

} catch (PDOException $e) {
  // Optionally log this
  echo "Database error: " . $e->getMessage();
  exit();
}
?>
  <!-- ***** Main Banner Area Start ***** -->
  <div class="about-main-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="content">
            <div class="blur-bg"></div>
            <h4>EXPLORE OUR COUNTRY</h4>
            <div class="line-dec"></div>
            <h2>Welcome To <?php echo $singleCountry->name; ?></h2>
            <p><?php echo $singleCountry->description; ?></p>
            <div class="main-button">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Main Banner Area End ***** -->
  
  <div class="cities-town">
    <div class="container">
      <div class="row">
        <div class="slider-content">
          <div class="row">
            <div class="col-lg-12">
              <h2><?php echo $singleCountry->name; ?>’s <em>Cities &amp; Towns</em></h2>
            </div>
            <div class="col-lg-12">
              <div class="owl-cites-town owl-carousel">
                <?php foreach ($singleImage as $image) : ?>
                  <div class="item">
                    <div class="thumb">
                    <img src="<?php echo APPURLFILE . '/' . $image->image; ?>" alt="">
                      <h4><?php echo $image->name; ?></h4>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="weekly-offers">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-3">
          <div class="section-heading text-center">
            <h2 style="color: white;">Best Weekly Offers In Each City</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="owl-weekly-offers owl-carousel">
            <?php foreach ($allCities as $city) : ?>
              <div class="item">
                <div class="thumb">
                <img src="<?php echo APPURLFILE . '/' . $city->image; ?>" alt="">
                  <div class="text">
                    <h4><?php echo $city->name; ?><br><span><i class="fa fa-users"></i> <?php echo $city->count_bookings; ?> Check Ins</span></h4>
                    <h6>$ <?php echo $city->price; ?><br><span>/person</span></h6>
                    <div class="line-dec"></div>
                    <ul>
                      <li>Deal Includes:</li>
                      <li><i class="fa fa-taxi"></i>  <?php echo $city->trip_days; ?> Days Trip > Hotel Included</li>
                      <li><i class="fa fa-plane"></i> Airplane Bill Included</li>
                      <li><i class="fa fa-building"></i> Daily Places Visit</li>
                    </ul>
                    <?php if(isset($_SESSION['user_id'])) : ?>
                      <div class="main-button">
                        <a href="reservation.php?id=<?php echo $city->id; ?>">Make a Reservation</a>
                      </div>
                    
                      <?php else: ?>
                        <p >Please <a href="<?php echo APPURL;?>/auth/login.php">Login</a> to make a reservation</p>
                    <?php endif; ?>
                    
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="more-about">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 align-self-center">
          <div class="left-image">
            <img src="assets/images/about-left-image.jpg" alt="">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="section-heading">
            <h2>Discover More About Our Country</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
          </div>
          <div class="row">
           
            <div class="col-lg-12">
              <div class="info-item">
                <div class="row">
                  <div class="col-lg-6">
                    <h4><?php echo $num_cities->num_city; ?>+</h4>
                    <span>Amazing Places</span>
                  </div>
                  <div class="col-lg-6">
                    <h4><?php echo $num_bookings->count_bookings; ?>+</h4>
                    <span>Different Check-ins</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
          
        </div>
      </div>
    </div>
  </div>


  
<?php require 'includes/footer.php'; ?>