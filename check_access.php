<?php
/**
 * check_access.php
 * AJAX endpoint that validates if user can book a specific class
 * Returns JSON response with booking eligibility and reason for denial if applicable
 * Called by JavaScript when user tries to book a class
 */

require_once 'includes/init.php';

// Set response type to JSON so frontend knows it's JSON data
header('Content-Type: application/json');

// Check if user is logged in - required to book any class
$userId = currentUserId();
if ($userId === null) {
    echo json_encode([
        'canBook' => false,
        'reason' => 'Please login to book classes.'
    ]);
    exit;
}

// Get the class ID from the URL parameter (e.g., ?class=5)
// intval() converts it to integer to prevent SQL injection
$class_id = intval($_GET['class'] ?? 0);

$stmt = $conn->prepare("\n    SELECT class_name, martial_art, age_group \n    FROM classes \n    WHERE id = ?\n");
// Execute query
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

// If the class doesn't exist in database, tell user it's not found
if (!$class) {
    echo json_encode([
        'canBook' => false,
        'reason' => 'Class not found.'
    ]);
    exit;
}

// Determine if this is a Kids class or Adult class
// This matters for membership type checking (Elite vs Junior plans)
$is_kids_class = ($class['age_group'] === 'Kids');

// Call the membership rules function to check if user can book this class
// It checks membership tier, session limits, martial art restrictions, etc.
// Returns array with 'can_book' boolean and 'reason' message
$result = canUserBookClass($userId, $class['martial_art'], $is_kids_class, $class['class_name']);

// Send back the booking eligibility as JSON
// Frontend receives this and allows or prevents the booking
echo json_encode([
    'canBook' => $result['can_book'],
    'reason' => $result['reason']
]);
?>
