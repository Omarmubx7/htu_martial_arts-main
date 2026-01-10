<?php
/**
 * includes/booking_store.php
 * Database operations for bookings + weekly session counters.
 */

function recordBooking($userId, $classId)
{
    global $conn;

    $userId = intval($userId);
    $classId = intval($classId);
    if ($userId <= 0 || $classId <= 0) {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, class_id, status) VALUES (?, ?, 'confirmed')");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('ii', $userId, $classId);
    $success = $stmt->execute();
    $stmt->close();

    return (bool)$success;
}

function bookingExists($userId, $classId)
{
    global $conn;

    $userId = intval($userId);
    $classId = intval($classId);
    if ($userId <= 0 || $classId <= 0) {
        return false;
    }

    $stmt = $conn->prepare('SELECT 1 FROM bookings WHERE user_id = ? AND class_id = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('ii', $userId, $classId);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    return $exists;
}

function incrementUserSessions($userId)
{
    global $conn;

    $userId = intval($userId);
    if ($userId <= 0) {
        return false;
    }

    $stmt = $conn->prepare('UPDATE users SET sessions_used_this_week = sessions_used_this_week + 1 WHERE id = ?');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $userId);
    $success = $stmt->execute();
    $stmt->close();

    return (bool)$success;
}

function cancelBooking($bookingId, $userId)
{
    global $conn;

    $bookingId = intval($bookingId);
    $userId = intval($userId);
    if ($bookingId <= 0 || $userId <= 0) {
        return false;
    }

    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'confirmed'");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('ii', $bookingId, $userId);
    $success = $stmt->execute();
    $stmt->close();

    return (bool)$success;
}

function decrementUserSessions($userId)
{
    global $conn;

    $userId = intval($userId);
    if ($userId <= 0) {
        return false;
    }

    $stmt = $conn->prepare('UPDATE users SET sessions_used_this_week = GREATEST(sessions_used_this_week - 1, 0) WHERE id = ?');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $userId);
    $success = $stmt->execute();
    $stmt->close();

    return (bool)$success;
}
