<?php
session_start();

require '../config/config.php';
require '../vendor/autoload.php';

use \Firebase\JWT\JWT;

header('Content-Type: application/json');

// CORS Headers - Allow cross-origin requests from Angular frontend
header("Access-Control-Allow-Origin: http://localhost:4444");  // Your Angular app's URL
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");  // Allow methods
header("Access-Control-Allow-Headers: Content-Type, Authorization");  // Allow specific headers

// If the request is an OPTIONS request, just send a 200 response
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Secret key for JWT token (use a strong, unique key in production)
define('JWT_SECRET_KEY', 'swqfvtehysadweyhukncw');

// Check if the user is already logged in
if (isset($_SESSION['email'])) {
    echo json_encode([
        "success" => true,
        "message" => "Already logged in"
    ]);
    exit();
}

// Handle the POST request for login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON request
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Please fill all fields"
    ]);
    exit();
}


 else {
    $email = trim($data['email']);
    $password = trim($data['password']);

        try {
            $login = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $login->execute([":email" => $email]);
            $fetch = $login->fetch(PDO::FETCH_ASSOC);

            // Check if user exists and password matches
            if ($fetch && password_verify($password, $fetch['mypassword'])) {
                
                // Check if user role is 'admin'
                if ($fetch['role'] !== 'ADMIN') {
                    http_response_code(403); // Forbidden
                    echo json_encode([
                        "success" => false,
                        "message" => "You do not have admin access"
                    ]);
                    exit();
                }

                // Generate JWT token
                $payload = [
                    "user_id" => $fetch['id'],
                    "email" => $fetch['email'],
                    "role" => $fetch['role'],
                    "iat" => time(), // Issued at time
                    "exp" => time() + 3600 // Expiration time (1 hour)
                ];

                // Encode JWT with the secret key and algorithm 'HS256'
                $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');

                // Set session variables for admin user
                $_SESSION['email'] = $fetch['email'];
                $_SESSION['user_id'] = intval($fetch['id']);
                $_SESSION['role'] = $fetch['role']; // Store user role in session

                // Return the response with JWT token
                echo json_encode([
                    "success" => true,
                    "message" => "Login successful",
                    "accessToken" => $jwt // Return the token
                ]);
            } else {
                http_response_code(401); // Unauthorized
                echo json_encode([
                    "success" => false,
                    "message" => "Invalid email or password"
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}
?>
