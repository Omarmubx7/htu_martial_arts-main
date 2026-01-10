<?php
/**
 * includes/auth.php
 * Helper utilities for session-based authentication and authorization.
 */

if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        return !empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

if (!function_exists('currentUserId')) {
    function currentUserId()
    {
        return isLoggedIn() ? intval($_SESSION['user_id']) : null;
    }
}

if (!function_exists('redirectTo')) {
    function redirectTo($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin($redirect = 'login.php')
    {
        if (!isLoggedIn()) {
            redirectTo($redirect);
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin($redirect = 'login.php')
    {
        requireLogin($redirect);
        if (!isAdmin()) {
            redirectTo($redirect);
        }
    }
}
