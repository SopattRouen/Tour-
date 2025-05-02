<?php
// try {
//     //host
//     define("HOST", "localhost");
//     // dbname
//     define ("DBNAME", "wooxtravel");
//     //username
//     define("USER", "root");
//     //pass
//     define ("PASS", "");

//     $conn = new PDO("mysq]: host=". HOST. ";dbname=".DBNAME. "", USER, PASS);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION) ;
// }
//  catch (PDOException $e) {
//     echo "Error: " . $e->getMessage();
//     echo $Exeption->getMessage();   
// }

    // try {
    //     // Define database constants
    //     define("HOST", "db"); // Use service name 'db' instead of 'localhost'
    //     define("DBNAME", "my_database");
    //     define("USER", "user");
    //     define("PASS", "password");

    //     // Create a PDO connection
    //     $conn = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, USER, PASS);
    //     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    //     echo "Connected successfully"; // Debugging message
    // } catch (PDOException $e) {
    //     echo "Connection failed: " . $e->getMessage();
    // }
    // Suppress any output before headers

    // Define database connection constants
    define("HOST", "db");  // Use Docker service name for MySQL
    define("DBNAME", "my_database");
    define("USER", "user");
    define("PASS", "password");
    define('APPURLFILE', 'http://127.0.0.1:8001');
    
    // Wait for MySQL to be ready
    $maxAttempts = 5;
    $attempt = 0;
    while ($attempt < $maxAttempts) {
        try {
            // Establish the PDO connection
            $conn = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME . ";charset=utf8", USER, PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Connection successful, exit loop
            break;
        } catch (PDOException $e) {
            $attempt++;
            error_log("DB Connection Error (attempt $attempt): " . $e->getMessage());
            sleep(3);  // Wait 3 seconds before retrying
        }
    }
    
    // Check if the connection was successful
    if (!isset($conn)) {
        die("<script>alert('Database connection failed! Check logs.');</script>");
    }
    
    
?>
