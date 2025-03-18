<footer>
  <div class="container">
    <div class="row">
      <!-- Contact Section -->
      <div class="col-lg-4">
        <h5>Contact</h5>
        <p>📞 Phone: <a href="tel:+855">60486849</a></p>
        <p>📧 Email: <a href="mailto:info@margintravel.com">info@margintravel.com</a></p>
        <p>🌐 Website: <a href="#">www.margintravel.com</a></p>
      </div>

      <!-- About Section -->
      <div class="col-lg-4">
        <h5>About</h5>
        <p>Explore the world with Margin Travel. Your adventure starts here! We specialize in unforgettable travel experiences, from exotic getaways to cultural explorations. Whether you're seeking relaxation, adventure, or unique local experiences, we've got you covered.</p>
      </div>

      <!-- Follow Us Section -->
      <div class="col-lg-4">
        <h5>Follow Us</h5>
        <p><a href="#">Facebook</a></p>
        <p><a href="#">Instagram</a></p>
        <p><a href="#">Twitter</a></p>
      </div>
    </div>

    <hr>

    <div class="row">
      <div class="col-lg-12 text-center">
        <p>Copyright © 2036 <a href="#">Margin Travel</a> Company. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>




  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="<?php echo APPURL; ?>/vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo APPURL; ?>/vendor/bootstrap/js/bootstrap.min.js"></script>

  <script src="<?php echo APPURL; ?>/assets/js/isotope.min.js"></script>
  <script src="<?php echo APPURL; ?>/assets/js/owl-carousel.js"></script>
  <script src="<?php echo APPURL; ?>/assets/js/wow.js"></script>
  <script src="<?php echo APPURL; ?>/assets/js/tabs.js"></script>
  <script src="<?php echo APPURL; ?>/assets/js/popup.js"></script>
  <script src="<?php echo APPURL; ?>/assets/js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>    
  <script>
    function bannerSwitcher() {
      next = $('.sec-1-input').filter(':checked').next('.sec-1-input');
      if (next.length) next.prop('checked', true);
      else $('.sec-1-input').first().prop('checked', true);
    }

    var bannerTimer = setInterval(bannerSwitcher, 5000);

    $('nav .controls label').click(function() {
      clearInterval(bannerTimer);
      bannerTimer = setInterval(bannerSwitcher, 5000)
    });
  </script>

  </body>

</html>
