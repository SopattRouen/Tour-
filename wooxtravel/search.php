<?php
require 'includes/header.php';
require 'config/config.php';

$allSearchs = []; // initialize to avoid undefined variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['country_id']) || empty($_POST['price'])) {
        echo "<script>alert('Some inputs are empty');</script>";
    } else {
        $country_id = $_POST['country_id']; // 
        $price = $_POST['price']; //

        // ✅ Prepare correctly using prepare() instead of query()
        $searchs = $conn->prepare("SELECT * FROM cities WHERE country_id = :country_id AND price <= :price");
        $searchs->execute([
            ':country_id' => $country_id,
            ':price' => $price
        ]);

        $allSearchs = $searchs->fetchAll(PDO::FETCH_OBJ);
    }
}
?>


<div class="page-heading">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h4>Search Result</h4>
        <h2>Amazing Prices &amp; More</h2>
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
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
        </div>
      </div>
      <?php if (!empty($allSearchs)) : ?>
        <?php foreach($allSearchs as $search) : ?>
          <div class="col-lg-6 col-sm-6">
            <div class="item">
              <div class="row">
                <div class="col-lg-6">
                  <div class="image">
                  <img src="<?php echo APPURLFILE . '/' . $search->image; ?>" alt="">

                  </div>
                </div>
                <div class="col-lg-6 align-self-center">
                  <div class="content">
                    <span class="info">*Limited Offer Today</span>
                    <h4><?php echo htmlspecialchars($search->name); ?></h4>
                    <div class="row">
                      <div class="col-6">
                        <i class="fa fa-clock"></i>
                        <span class="list"><?php echo htmlspecialchars($search->trip_days); ?></span>
                      </div>
                      <div class="col-6">
                        <i class="fa fa-map"></i>
                        <span class="list">Daily Places</span>
                      </div>
                    </div>
                    <p>Limited Price: $<?php echo htmlspecialchars($search->price); ?> per person</p>
                    <div class="main-button">
                      <a href="reservation.php?id=<?php echo (int) $search->id; ?>">Make a Reservation</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <div class="col-12 text-center">
          <p>No results found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
