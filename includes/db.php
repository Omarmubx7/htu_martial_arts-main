<?php
/**
 * includes/db.php
 * Database connection file - handles connecting to MySQL database
 * This file is included in all PHP files that need database access
 */

// Database server hostname or IP address
// 127.0.0.1 is localhost (your local computer)
$servername = "127.0.0.1"; // Or "127.0.0.1"

// MySQL username - default XAMPP user is "root"
$username = "root";        // Default XAMPP user

// MySQL password - default XAMPP password is empty string ""
$password = "";            // Default XAMPP password is empty

// The name of the database we're connecting to
$dbname = "htu_martial_arts";

// MySQL port number - default is 3306, but XAMPP sometimes uses 3307
// CHANGE THIS if your MySQL runs on a different port
$port = 3307;              // CHANGE THIS if you used a different port (e.g. 3307)

// Create new mysqli connection object
// Parameters: server, user, password, database name, port
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check if connection failed (e.g., wrong credentials, database doesn't exist)
// If it fails, die() stops the script and shows the error message
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// If we reach here, connection is successful and $conn is ready to use
?>
