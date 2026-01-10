<?php
/**
 * includes/flash.php
 * Session-based flash messages for the Bootstrap Toast component.
 */

if (!function_exists('addFlashToast')) {
    function addFlashToast($message, $type = 'info')
    {
        $type = strtolower(trim($type));
        $allowed = ['success', 'danger', 'warning', 'info'];
        if (!in_array($type, $allowed, true)) {
            $type = 'info';
        }

        if (!isset($_SESSION['flash_toasts']) || !is_array($_SESSION['flash_toasts'])) {
            $_SESSION['flash_toasts'] = [];
        }

        $_SESSION['flash_toasts'][] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('popFlashToasts')) {
    function popFlashToasts()
    {
        $toasts = [];
        if (isset($_SESSION['flash_toasts']) && is_array($_SESSION['flash_toasts'])) {
            $toasts = $_SESSION['flash_toasts'];
        }

        unset($_SESSION['flash_toasts']);
        return $toasts;
    }
}
