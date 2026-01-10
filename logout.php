<?php
/**
 * logout.php
 * Handles user logout by destroying the session and redirecting to home page
 * This is called when user clicks the Logout button in the navigation menu
 */

require_once 'includes/init.php';

// Completely destroy the session and clear all session variables
// This removes the user from $_SESSION, effectively logging them out
session_destroy();

// Send user back to home page after logout
redirectTo('index.php');
?>
