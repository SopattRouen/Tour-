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

    // Get all cities with their trips and booking counts
    $stmt = $conn->prepare("
        SELECT 
            ci.id AS city_id,
            ci.name AS city_name,
            ci.image AS city_image,
            t.id AS trip_id,
            t.trip_days,
            t.price,
            t.start_date,
            t.end_date,
            COUNT(b.id) AS booking_count
        FROM cities ci
        LEFT JOIN trips t ON ci.id = t.city_id
        LEFT JOIN bookings b ON t.id = b.trip_id
        WHERE ci.country_id = :id
        GROUP BY ci.id, ci.name, ci.image, t.id, t.trip_days, t.price, t.start_date, t.end_date
    ");
    $stmt->execute(['id' => $id]);
    $allCities = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Count of cities for the country
    $stmt = $conn->prepare("SELECT COUNT(id) AS num_city FROM cities WHERE country_id = :id");
    $stmt->execute(['id' => $id]);
    $num_cities = $stmt->fetch(PDO::FETCH_OBJ);

    // Total bookings for the country
    $stmt = $conn->prepare("
        SELECT COUNT(b.id) AS count_bookings
        FROM countries co
        JOIN cities ci ON co.id = ci.country_id
        JOIN trips t ON ci.id = t.city_id
        JOIN bookings b ON t.id = b.trip_id
        WHERE co.id = :id
    ");
    $stmt->execute(['id' => $id]);
    $num_bookings = $stmt->fetch(PDO::FETCH_OBJ);

} catch (PDOException $e) {
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
                    <h2>Welcome To <?php echo htmlspecialchars($singleCountry->name); ?></h2>
                    <p><?php echo htmlspecialchars($singleCountry->description); ?></p>
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
                        <h2><?php echo htmlspecialchars($singleCountry->name); ?>'s <em>Cities &amp; Towns</em></h2>
                    </div>
                    <div class="col-lg-12">
                        <div class="owl-cites-town owl-carousel">
                            <?php foreach ($singleImage as $image) : ?>
                                <div class="item">
                                    <div class="thumb">
                                        <img src="<?php echo htmlspecialchars(APPURLFILE . '/' . $image->image); ?>" alt="<?php echo htmlspecialchars($image->name); ?>">
                                        <h4><?php echo htmlspecialchars($image->name); ?></h4>
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
                    <p>Discover amazing travel packages in our most popular destinations.</p>
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
                                <img src="<?php echo htmlspecialchars(APPURLFILE . '/' . $city->city_image); ?>" alt="<?php echo htmlspecialchars($city->city_name); ?>">
                                <div class="text">
                                    <h4><?php echo htmlspecialchars($city->city_name); ?><br><span><i class="fa fa-users"></i> <?php echo htmlspecialchars($city->booking_count); ?> Check Ins</span></h4>
                                    <h6>$<?php echo htmlspecialchars($city->price); ?><br><span>/person</span></h6>
                                    <div class="line-dec"></div>
                                    <ul>
                                        <li>Deal Includes:</li>
                                        <li><i class="fa fa-taxi"></i> <?php echo htmlspecialchars($city->trip_days); ?> Days Trip > Hotel Included</li>
                                        <li><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($city->start_date); ?> Departure Date </li>
                                        <li><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($city->end_date); ?> Return Date </li>
                                        <li><i class="fa fa-plane"></i> Airplane Bill Included</li>
                                        <li><i class="fa fa-building"></i> Daily Places Visit</li>
                                    </ul>
                                    <?php if(isset($_SESSION['user_id'])) : ?>
                                        <div class="main-button">
                                            <a href="reservation.php?id=<?php echo htmlspecialchars($city->trip_id); ?>">Make a Reservation</a>
                                        </div>
                                    <?php else: ?>
                                        <p>Please <a href="<?php echo htmlspecialchars(APPURL); ?>/auth/login.php">Login</a> to make a reservation</p>
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
                    <img src="assets/images/about-left-image.jpg" alt="About our country">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-heading">
                    <h2>Discover More About Our Country</h2>
                    <p>Explore the wonders and unique experiences our country has to offer.</p>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="info-item">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h4><?php echo htmlspecialchars($num_cities->num_city); ?>+</h4>
                                    <span>Amazing Places</span>
                                </div>
                                <div class="col-lg-6">
                                    <h4><?php echo htmlspecialchars($num_bookings->count_bookings); ?>+</h4>
                                    <span>Different Check-ins</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p>Our country offers diverse landscapes, rich culture, and unforgettable experiences for every traveler. From bustling cities to serene natural wonders, there's something for everyone.</p>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>