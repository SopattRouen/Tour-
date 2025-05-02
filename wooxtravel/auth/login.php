<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config/config.php';
require '../includes/header.php';

if (isset($_SESSION['jwt_token'])) {
    header("Location: " . APPURL);
    exit();
}

if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill all fields');</script>";
        exit();
    }
    var_dump($_SESSION); // Should show stored session data

    $username = trim($_POST['email']);
    $password = trim($_POST['password']);

    $api_url = "http://host.docker.internal:8000/api/auth/login";
    $ch = curl_init($api_url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'username' => $username,
            'password' => $password
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        error_log("cURL Error: $error");
        echo "<script>alert('Connection failed: $error');</script>";
        curl_close($ch);
        exit();
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        echo "<script>alert('Invalid API response format');</script>";
        exit();
    }

    if (isset($data['access_token'])) {
        $_SESSION['jwt_token'] = $data['access_token'];
        $_SESSION['user'] = $data['user'];
        $_SESSION['email'] = $data['user']['email'] ?? $data['user']['phone'] ?? 'user';
        $_SESSION['user_id'] = $data['user']['id']; // ✅ Fixed line

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        ob_end_clean();
        
        header("Location: " . APPURL);
        exit();
    } else {
        $error_msg = $data['message'] ?? 'Invalid credentials';
        echo "<script>alert('Login failed: $error_msg');</script>";
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
