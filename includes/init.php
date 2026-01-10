<?php
/**
 * includes/init.php
 * Bootstraps every page with a session, database connection, and shared helpers.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/lookups.php';
require_once __DIR__ . '/membership_rules.php';
require_once __DIR__ . '/booking_store.php';
    