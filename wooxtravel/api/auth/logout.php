<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// If you are storing the JWT in a cookie, delete the cookie (optional)
setcookie("accessToken", "", time() - 3600, "/"); // Set to expire in the past

// Respond with a success message
echo json_encode([
    "success" => true,
    "message" => "Logged out successfully"
]);

exit();
?>
