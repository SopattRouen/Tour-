<?php
require 'includes/header.php';
require 'config/config.php';

  $allSearches = []; // Initialize empty array for results
  $searchMessage = ''; // Message to display to users
  $preservedCountryId = ''; // To preserve form selection
  $preservedPrice = ''; // To preserve form selection

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
          // Validate inputs
          if (empty($_POST['country_id']) || empty($_POST['price'])) {
              $searchMessage = '<div class="alert alert-warning">Please select both destination and price range.</div>';
          } else {
              $country_id = (int)$_POST['country_id']; // Force integer type
              $price = (int)$_POST['price']; // Force integer type
              
              // Preserve form values
              $preservedCountryId = $country_id;
              $preservedPrice = $price;

              // Search cities with their trips that match criteria
              $searchQuery = $conn->prepare("
                  SELECT 
                      c.id,
                      c.name,
                      c.image,
                      t.price,
                      t.trip_days,
                      co.name AS country_name
                  FROM cities c
                  JOIN trips t ON c.id = t.city_id
                  JOIN countries co ON c.country_id = co.id
                  WHERE c.country_id = :country_id 
                  AND t.price <= :price
                  ORDER BY t.price ASC
              ");
              
              $searchQuery->execute([
                  ':country_id' => $country_id,
                  ':price' => $price
              ]);

              $allSearches = $searchQuery->fetchAll(PDO::FETCH_OBJ);

              // if (empty($allSearches)) {
              //     $searchMessage = '<div class="alert alert-info">No trips found matching your criteria. Try a different search.</div>';
              // }
          }
      } catch (PDOException $e) {
          $searchMessage = '<div class="alert alert-danger">Database error. Please try again later.</div>';
          // Log the error for debugging: error_log($e->getMessage());
      }
  }

  // Fetch all countries for the dropdown
  $countries = $conn->query("SELECT * FROM countries ORDER BY name");
  $allCountries = $countries->fetchAll(PDO::FETCH_OBJ);
?>

<div class="page-heading">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h4>Search Results</h4>
                <h2>Find Your Perfect Trip</h2>
            </div>
        </div>
    </div>
</div>

<div class="search-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <form id="search-form" method="POST" role="search">
                    <div class="row">
                        <div class="col-lg-2">
                            <h4>Search Again:</h4>
                        </div>
                        <div class="col-lg-4">
                            <fieldset>
                                <select name="country_id" class="form-select" aria-label="Country selection">
                                    <option value="">All Destinations</option>
                                    <?php foreach($allCountries as $country) : ?>
                                        <option value="<?php echo (int)$country->id; ?>" 
                                            <?php echo ($preservedCountryId == $country->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-4">
                            <fieldset>
                                <select name="price" class="form-select" aria-label="Price range">
                                    <option value="">Price Range</option>
                                    <option value="100" <?php echo ($preservedPrice == 100) ? 'selected' : ''; ?>>Under $100</option>
                                    <option value="250" <?php echo ($preservedPrice == 250) ? 'selected' : ''; ?>>Under $250</option>
                                    <option value="500" <?php echo ($preservedPrice == 500) ? 'selected' : ''; ?>>Under $500</option>
                                    <option value="1000" <?php echo ($preservedPrice == 1000) ? 'selected' : ''; ?>>Under $1,000</option>
                                    <option value="2500" <?php echo ($preservedPrice == 2500) ? 'selected' : ''; ?>>Under $2,500</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-2">                        
                            <fieldset>
                                <button type="submit" class="border-button">Search Again</button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="search-results">
    <div class="container">
        <?php if (!empty($searchMessage)) : ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php echo $searchMessage; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading text-center">
                    <h2 style="color: white;">Available Trips</h2>
                    <p>Explore these amazing travel options that match your search.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($allSearches)) : ?>
                <?php foreach($allSearches as $trip) : ?>
                    <div class="col-lg-6 col-sm-6 mb-4">
                        <div class="trip-card">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="trip-image">
                                        <img src="<?php echo htmlspecialchars(APPURLFILE . '/' . $trip->image); ?>" 
                                             alt="<?php echo htmlspecialchars($trip->name); ?>" 
                                             class="img-fluid">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="trip-details">
                                        <span class="badge bg-warning">Limited Offer</span>
                                        <h3 style="color: white;"><?php echo htmlspecialchars($trip->name); ?></h3>
                                        <p class="country"><?php echo htmlspecialchars($trip->country_name); ?></p>
                                        
                                        <div class="trip-meta">
                                            <div class="meta-item">
                                                <i class="fa fa-clock"></i>
                                                <span><?php echo htmlspecialchars($trip->trip_days); ?> days</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="fa fa-map-marker-alt"></i>
                                                <span>Multiple locations</span>
                                            </div>
                                        </div>
                                        
                                        <p class="price">From <strong>$<?php echo number_format($trip->price, 2); ?></strong> per person</p>
                                        
                                        <a href="reservation.php?id=<?php echo (int)$trip->id; ?>" 
                                           class="btn btn-primary">
                                            Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        No trips found matching your criteria. Try adjusting your search filters.
                    </div>
                </div>
            <?php else : ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        Use the search form above to find available trips.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>