<?php
  ob_start(); // Start output buffering
  session_start(); 
  require '../config/config.php';
  require '../includes/header.php';

  // Redirect if already logged in
  if (isset($_SESSION['email'])) {
    header("location: ".APPURL."");
    exit(); // Stop execution after redirect
  }

  if (isset($_POST['submit'])) {
      if (empty($_POST['email']) || empty($_POST['password'])) {
          echo "<script>alert('Please fill all fields');</script>";
      } else {
          $email = trim($_POST['email']);
          $password = trim($_POST['password']);

          $login = $conn->prepare("SELECT * FROM users WHERE email = :email");
          $login->execute([":email" => $email]);
          $fetch = $login->fetch(PDO::FETCH_ASSOC);

          if ($fetch && password_verify($password, $fetch['mypassword'])) {
              $_SESSION['email'] = $fetch['email'];
              $_SESSION['user_id'] = intval($fetch['id']);
              header("location: ".APPURL."");
              exit(); // Stop script after redirect
          } else {
              echo "<script>alert('Invalid email or password!');</script>";
          }
      }
  }
?>


<div class="reservation-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <form id="reservation-form" name="gs" method="POST" action="login.php">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Login</h4>
                        </div>
                        <div class="col-md-12">
                            <fieldset>
                                <label for="email" class="form-label">Your Email</label>
                                <input type="text" name="email" class="email" placeholder="Enter your email" required>
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset>
                                <label for="password" class="form-label">Your Password</label>
                                <input type="password" name="password" class="password" placeholder="Enter your password" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" name="submit" class="main-button">Login</button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
