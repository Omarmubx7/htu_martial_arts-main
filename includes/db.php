<?php
/**
 * includes/db.php
 * Database connection (mysqli)
 */

// Database server hostname or IP address
// TIP: On shared hosting, set these via env vars if possible.
$servername = getenv('DB_HOST') ?: "sql306.infinityfree.com";

// MySQL username
$username = getenv('DB_USER') ?: "if0_40779796";

// MySQL password
// IMPORTANT: remove accidental trailing spaces (common copy/paste issue)
$password = getenv('DB_PASS');
if ($password === false || $password === null) {
    $password = "bthxxGE0EJo5";
}
$password = rtrim($password);

// Database name
$dbname = getenv('DB_NAME') ?: "if0_40779796_htu_martial_arts";

// MySQL port
// InfinityFree / most hosts use 3306 (XAMPP sometimes uses 3307 locally).
$port = intval(getenv('DB_PORT') ?: 3306);

// Create connection once (some pages may include init multiple times via other includes)
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        error_log('DB connection failed: ' . $conn->connect_error);
        die('Database connection failed. Please try again later.');
    }

    // Ensure consistent character encoding
    if (!$conn->set_charset('utf8mb4')) {
        error_log('Failed to set DB charset: ' . $conn->error);
    }
}
