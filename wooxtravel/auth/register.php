<?php
    require '../config/config.php';
    require '../includes/header.php';

    // Redirect if user is already logged in
    if (isset($_SESSION['email'])) {
        header("location: ".APPURL."");
    }

    if (isset($_POST['submit'])) {
        // Check if fields are empty
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            echo "<script>alert('Please fill all fields');</script>";
        } else {
            // Sanitize and validate inputs
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('Invalid email format');</script>";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                try {
                    // Insert into the database
                    $register = $conn->prepare("INSERT INTO users(username, email, mypassword) VALUES(:username, :email, :mypassword)");
                    $register->execute([
                        ":username" => $username,
                        ":email" => $email,
                        ":mypassword" => $hashedPassword
                    ]);

                    // Check if the insert was successful
                    if ($register->rowCount() > 0) {
                        echo "<script>alert('User registered successfully');";
                        header("location: login.php");
                    } else {
                        echo "<script>alert('Failed to register user');</script>";
                    }
                } catch (PDOException $e) {
                    error_log("Database error: " . $e->getMessage());
                    echo "<script>alert('An error occurred. Please try again later.');</script>";
                }
            }
        }
    }
?>

  <div class="reservation-form">
    <div class="container">
      <div class="row">
        
        <div class="col-lg-12">
          <form id="reservation-form" name="gs" method="POST" role="search" action="register.php">
            <div class="row">
              <div class="col-lg-12">
                <h4>Register</h4>
              </div>
              <div class="col-md-12">
                <fieldset>
                    <label for="Name" class="form-label">Username</label>
                    <input type="text" name="username" class="username" placeholder="username" autocomplete="on" required>
                </fieldset>
              </div>

              <div class="col-md-12">
                  <fieldset>
                      <label for="Name" class="form-label">Your Email</label>
                      <input type="text" name="email" class="email" placeholder="email" autocomplete="on" required>
                  </fieldset>
              </div>
           
              <div class="col-md-12">
                <fieldset>
                    <label for="Name" class="form-label">Your Password</label>
                    <input type="password" name="password" class="password" placeholder="password" autocomplete="on" required>
                </fieldset>
              </div>
              <div class="col-lg-12">                        
                  <fieldset>
                      <button type="submit" name="submit" class="main-button">register</button>
                  </fieldset>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php require '../includes/footer.php'; ?>