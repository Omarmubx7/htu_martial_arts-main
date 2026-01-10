<?php
/**
 * includes/flash.php
 *
 * Very simple flash messages stored in $_SESSION.
 * Pages can set a message before redirecting, and the next page shows it once.
 */

if (!function_exists('addFlashToast')) {
    // Add a toast message to be shown on the next page load.
    // $type should be: success | danger | warning | info
    function addFlashToast($message, $type = 'info')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $type = strtolower(trim((string)$type));
        $allowed = ['success', 'danger', 'warning', 'info'];
        if (!in_array($type, $allowed, true)) {
            $type = 'info';
        }

        if (!isset($_SESSION['flash_toasts']) || !is_array($_SESSION['flash_toasts'])) {
            $_SESSION['flash_toasts'] = [];
        }

        $_SESSION['flash_toasts'][] = [
            'type' => $type,
            'message' => (string)$message,
        ];
    }
}

if (!function_exists('popFlashToasts')) {
    // Read and clear queued toast messages.
    function popFlashToasts()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $toasts = [];
        if (isset($_SESSION['flash_toasts']) && is_array($_SESSION['flash_toasts'])) {
            $toasts = $_SESSION['flash_toasts'];
        }

        unset($_SESSION['flash_toasts']);
        return $toasts;
    }
}
