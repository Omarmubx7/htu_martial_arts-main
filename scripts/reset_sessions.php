<?php
/**
 * scripts/reset_sessions.php
 * Reset the weekly session counter for every user (intended for weekly cron jobs)
 */

// Prevent HTTP access; script is designed for CLI execution only
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$stmt = $conn->prepare("UPDATE users SET sessions_used_this_week = 0");
if (!$stmt) {
    fwrite(STDERR, "Failed to prepare reset statement: {$conn->error}\n");
    exit(1);
}

if (!$stmt->execute()) {
    fwrite(STDERR, "Failed to reset sessions: " . $stmt->error . "\n");
    exit(1);
}

printf("Weekly session counter reset complete (%d rows affected).\n", $stmt->affected_rows);
$stmt->close();
$conn->close();
