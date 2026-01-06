<?php
/**
 * logout.php
 * Handles user logout by destroying the session and redirecting to home page
 * This is called when user clicks the Logout button in the navigation menu
 */

// Start the current session (needs to be active before we can destroy it)
session_start();

// Completely destroy the session and clear all session variables
// This removes the user from $_SESSION, effectively logging them out
session_destroy();

// Send user back to home page after logout
header("Location: index.php");

// Exit to stop any further code execution after redirect
exit();
?>
