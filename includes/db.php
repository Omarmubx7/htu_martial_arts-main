<?php
/**
 * includes/db.php
 * Database connection (mysqli)
 */

// Database server hostname or IP address
$servername = "127.0.0.1";

// MySQL username - default XAMPP user is "root"
$username = "root";

// MySQL password - default XAMPP password is empty string ""
$password = "";

// Database name
$dbname = "htu_martial_arts";

// MySQL port number - XAMPP often uses 3307
$port = 3307;

// Create connection once (some pages may include init multiple times via other includes)
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}
