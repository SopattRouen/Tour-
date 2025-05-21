<?php require 'includes/header.php'; ?>
<?php require 'config/config.php'; ?>
<?php
// Selecting countries with continent name and average trip price
$countries = $conn->prepare("
  SELECT 
    countries.id AS id, 
    countries.name AS name, 
    countries.image AS image, 
    continents.name AS continent, 
    countries.population AS population, 
    countries.territory AS territory, 
    countries.description AS description, 
    AVG(trips.price) AS avg_price 
  FROM countries 
  JOIN continents ON countries.continent_id = continents.id 
  JOIN cities ON countries.id = cities.country_id 
  JOIN trips ON cities.id = trips.city_id 
  GROUP BY countries.id, countries.name, countries.image, continents.name, 
           countries.population, countries.territory, countries.description
");
$countries->execute();
$allCountries = $countries->fetchAll(PDO::FETCH_OBJ);
?>


  <!-- ***** Main Banner Area Start ***** -->
<section id="section-1">
  <div class="content-slider">
    <?php 
    // Limit to first 3 countries for radio buttons
    $limitedCountries = array_slice($allCountries, 0, 3); 
    $i = 0; // Counter for 'checked' attribute
    ?>
    
    <!-- Radio Buttons -->
    <?php foreach ($limitedCountries as $country): ?>
      <input 
        type="radio" 
        id="banner<?php echo $country->id; ?>" 
        class="sec-1-input" 
        name="banner" 
        <?php echo ($i === 0) ? 'checked' : ''; ?> 
      >
      <?php $i++; ?>
    <?php endforeach; ?>

    <div class="slider">
      <!-- Banners -->
      <?php foreach ($limitedCountries as $country): ?>
        <div id="top-banner-<?php echo $country->id; ?>" class="banner">
          <div class="banner-inner-wrapper header-text">
            <div class="main-caption">
              <h2>Take a Glimpse Into The Beautiful Country Of:</h2>
              <h1><?php echo $country->name; ?></h1>
              <div class="border-button">
                <a href="about.php?id=<?php echo $country->id; ?>">Go There</a>
              </div>
            </div>
            <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <div class="more-info">
                    <div class="row">
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-user"></i>
                        <h4><span>Population:</span><br><?php echo $country->population; ?> M</h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-globe"></i>
                        <h4><span>Territory:</span><br><?php echo $country->territory; ?> KM<em>2</em></h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <i class="fa fa-home"></i>
                        <h4><span>AVG Price:</span><br>$<?php echo $country->avg_price; ?></h4>
                      </div>
                      <div class="col-lg-3 col-sm-6 col-6">
                        <div class="main-button">
                          <a href="about.php?id=<?php echo $country->id; ?>">Explore More</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Navigation Controls -->
    <nav>
      <div class="controls">
        <?php foreach ($limitedCountries as $country): ?>
          <label for="banner<?php echo $country->id; ?>">
            <span class="progressbar">
              <span class="progressbar-fill"></span>
            </span>
            <span class="text"><?php echo $country->id; ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </nav>
  </div>
</section>
<!-- ***** Main Banner Area End ***** -->
  
  <div class="visit-country">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <div class="section-heading">
            <h2 style="color: white;">Visit One of Our Amazing Destinations Now</h2>
            <p>Embark on a journey filled with adventure, culture, and breathtaking landscapes. From the bustling streets of vibrant cities to the tranquil beauty of nature’s hidden gems, we offer unforgettable experiences tailored to your desires. Discover new cultures, taste exotic flavors, and create memories that will last a lifetime. Your next adventure awaits!</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8">
          <div class="items">
            <div class="row">
              <?php foreach ($allCountries as $country) : ?>
                <div class="col-lg-12">
                  <div class="item">
                    <div class="row">
                      <div class="col-lg-4 col-sm-5">
                        <div class="image">
                        <img src="<?php echo APPURLFILE . '/' . $country->image; ?>" alt="">

                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-7">
                        <div class="right-content">
                        <h4 style="color: white;"><?php echo $country->name; ?></h4>
                          <span><?php echo $country->continent; ?></span>
                          <div class="main-button">
                            <a href="about.php?id=<?php echo $country->id; ?>">Explore More</a>
                          </div>
                          <p><?php echo $country->description; ?></p>
                          <ul class="info">
                            <li><i class="fa fa-user"></i> <?php echo $country->population; ?> Mil People</li>
                            <li><i class="fa fa-globe"></i> <?php echo $country->territory; ?> km2</li>
                            <li><i class="fa fa-home"></i> $<?php echo $country->avg_price; ?></li>
                          </ul>
                          <div class="text-button">
                            <a href="about.php?id=<?php echo $country->id; ?>">Need Directions ? <i class="fa fa-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="side-bar-map">
            <div class="row">
              <div class="col-lg-12">
                <div id="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126529.47712257857!2d104.8561258587372!3d11.55637381861183!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3109511d4f3132d7%3A0xf362d4dabb7c440!2sPhnom%20Penh%2C%20Cambodia!5e1!3m2!1sen!2s!4v1642870052544!5m2!1sen!2s" width="100%" height="550px" frameborder="0" style="border:0; border-radius: 23px;" allowfullscreen=""></iframe>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php require 'includes/footer.php'; ?>
