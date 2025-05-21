<?php
ob_start(); // Start output buffering to allow header() later

require '../config/config.php';
require '../includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['email'])) {
    header("Location: " . APPURL);
    exit;
}

// Registration logic
if (isset($_POST['submit'])) {
    // Validate fields
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill all fields');</script>";
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format');</script>";
        } else {
            $apiUrl ='http://host.docker.internal:8000/api/auth/register';

            $payload = json_encode([
                "name" => $username,
                "email" => $email,
                "password" => $password
            ]);

            // Initialize cURL request
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                echo "<script>alert('cURL Error: $error');</script>";
            } else {
                $result = json_decode($response, true);

                if ($httpCode === 201 || $httpCode === 200) {
                    echo "<script>alert('User registered successfully');</script>";
                    header("Location: login.php");
                    exit;
                } else {
                    $message = isset($result['message']) ? $result['message'] : 'Failed to register user';
                    echo "<script>alert('Error: " . htmlspecialchars($message) . "');</script>";
                }
            }
        }
    }
}

ob_end_flush(); // Flush output buffer
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
              <div class="col-md-12">
                <fieldset>
                    <label for="Role" class="form-label">Select Role</label>
                    <select name="role" class="role" required>
                        <option value="USER">User</option>
                        <!-- <option value="ADMIN">Admin</option> -->
                    </select>
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