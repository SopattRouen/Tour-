<?php 
require 'includes/header.php';
require 'config/config.php'; 

try {
    // Fetch cities with their trips data
    $cities = $conn->query("
        SELECT 
            c.id,
            c.name,
            c.image,
            t.price,
            t.trip_days
        FROM cities c
        JOIN trips t ON c.id = t.city_id
        ORDER BY t.price DESC 
        LIMIT 4
    ");
    $cities->execute();
    $allCities = $cities->fetchAll(PDO::FETCH_OBJ);

    // Fetch countries
    $countries = $conn->query("SELECT * FROM countries");
    $countries->execute();
    $allCountries = $countries->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>

<div class="page-heading">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h4>Discover Our Weekly Offers</h4>
        <h2>Amazing Prices &amp; More</h2>
      </div>
    </div>
  </div>
</div>

<div class="search-form">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <form id="search-form" method="POST" role="search" action="search.php">
          <div class="row">
            <div class="col-lg-2">
              <h4>Sort Deals By:</h4>
            </div>
            <div class="col-lg-4">
              <fieldset>
                <select name="country_id" class="form-select" aria-label="Default select example" id="chooseLocation">
                  <option value="" selected>Destinations</option>
                  <?php foreach($allCountries as $country) : ?>
                    <option value="<?php echo (int) $country->id; ?>">
                      <?php echo htmlspecialchars($country->name); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </fieldset>
            </div>
            <div class="col-lg-4">
              <fieldset>
                <select name="price" class="form-select" aria-label="Default select example" id="choosePrice">
                  <option value="" selected>Price Range</option>
                  <option value="100">less than $100</option>
                  <option value="250">less than $250</option>
                  <option value="500">less than $500</option>
                  <option value="1000">less than $1,000</option>
                  <option value="2500">less than $2,500</option>
                </select>
              </fieldset>
            </div>
            <div class="col-lg-2">                        
              <fieldset>
                <button type="submit" name="submit" class="border-button">Search Results</button>
              </fieldset>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="amazing-deals">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 offset-lg-3">
        <div class="section-heading text-center">
          <h2>Best Weekly Offers In Each City</h2>
          <p>Discover amazing travel packages in our most popular destinations.</p>
        </div>
      </div>
      <?php if (!empty($allCities)) : ?>
        <?php foreach($allCities as $city) : ?>
          <div class="col-lg-6 col-sm-6">
            <div class="item">
              <div class="row">
                <div class="col-lg-6">
                  <div class="image">
                    <img src="<?php echo htmlspecialchars(APPURLFILE . '/' . $city->image); ?>" alt="<?php echo htmlspecialchars($city->name); ?>">
                  </div>
                </div>
                <div class="col-lg-6 align-self-center">
                  <div class="content">
                    <span class="info">*Limited Offer Today</span>
                    <h4><?php echo htmlspecialchars($city->name); ?></h4>
                    <div class="row">
                      <div class="col-6">
                        <i class="fa fa-clock"></i>
                        <span class="list"><?php echo htmlspecialchars($city->trip_days); ?> Days</span>
                      </div>
                      <div class="col-6">
                        <i class="fa fa-map"></i>
                        <span class="list">Daily Places</span>
                      </div>
                    </div>
                    <p>Limited Price: $<?php echo number_format(htmlspecialchars($city->price), 2); ?> per person</p>
                    <?php if(isset($_SESSION['user_id'])) : ?>
                      <div class="main-button">
                        <a href="reservation.php?id=<?php echo (int) $city->id; ?>">Make a Reservation</a>
                      </div>
                    <?php else: ?>
                      <p>Please <a href="<?php echo htmlspecialchars(APPURL); ?>/auth/login.php">Login</a> to make a reservation</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <div class="col-12 text-center">
          <p>No deals found at the moment.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>