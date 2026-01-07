<?php
session_start();
include 'includes/db.php';
include 'includes/membership_rules.php';
include 'includes/bookings.php';

// Require login - must be authenticated to book a class
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$class_id = intval($_GET['id'] ?? 0);  // Get class ID from URL, convert to integer for safety

// Fetch the class details from database using prepared statement
// This gets the class name, schedule, martial art, and age group - need this info to display confirmation
$stmt = $conn->prepare("SELECT id, class_name, martial_art, day_of_week, start_time, end_time, age_group FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();  // Get the class data

// If class doesn't exist, send user back to classes list
if (!$class) {
    header('Location: classes_premium.php');
    exit;
}

// Verify user can actually book this class based on their membership and chosen martial art
// This calls a function from membership_rules.php that checks permissions
try {
    $conn->begin_transaction();

    $access_check = canUserBookClass(
        $_SESSION['user_id'],
        $class['martial_art'],
        $class['age_group'] === 'Kids',
        $class['class_name']
    );

    if (!$access_check['can_book']) {
        throw new RuntimeException($access_check['reason'] ?? 'You cannot book this class.');
    }

    if (bookingExists($_SESSION['user_id'], $class_id)) {
        throw new RuntimeException('You already booked this class.');
    }

    if (!recordBooking($_SESSION['user_id'], $class_id)) {
        throw new RuntimeException('Failed to save your booking.');
    }

    if (!incrementUserSessions($_SESSION['user_id'])) {
        throw new RuntimeException('Failed to update your weekly session count.');
    }

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION['booking_error'] = $e->getMessage();
    header('Location: classes_premium.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - HTU Martial Arts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/sport-theme.css">
    <!-- styles now use shared site classes -->
</head>
<body class="bg-dark text-white">

<div class="page-section d-flex align-items-center justify-content-center">
    <!-- Confirmation modal - white box on dark background -->
    <!-- style="max-width: 600px" limits width on large screens -->
    <div class="glass-panel narrow-container" style="color: black;">
        <div class="text-center p-4 p-md-5">
            <!-- Back Button - navigate back to classes page -->
            <!-- style="text-align: left; margin-bottom: 20px" pushes it to top left -->
            <div class="text-start mb-3">
                <a href="classes_premium.php" class="btn btn-light btn-sm no-underline"><i class="bi bi-arrow-left me-2"></i>Back to Classes</a>
            </div>
            
            <!-- Success icon - emoji checkmark to show booking succeeded -->
            <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
            
            <!-- Main confirmation heading -->
            <!-- style="font-family: 'Oswald'" uses the same font as other headings for consistency -->
            <h1 class="fw-bold text-deep-dark" style="margin-bottom: 20px; text-transform: uppercase;">Booking Confirmed!</h1>
            
            <!-- Box showing the class details the user just booked -->
            <!-- style="background: #f5f5f5" light gray background, "padding: 30px" plenty of spacing -->
            <div class="card p-4 my-4 text-start">
                <h3 class="fw-bold text-primary" style="text-transform: uppercase;">Class Details</h3>
                
                <!-- Show the name of the class they booked -->
                <div style="margin-bottom: 15px;">
                    <strong>Class:</strong><br>
                    <?php echo htmlspecialchars($class['class_name']); ?>
                </div>
                
                <!-- Show which day of the week the class happens -->
                <div style="margin-bottom: 15px;">
                    <strong>Day:</strong><br>
                    <?php echo htmlspecialchars($class['day_of_week']); ?>
                </div>
                
                <!-- Show the class time in readable format using date() to convert from 24hr to 12hr AM/PM -->
                <div style="margin-bottom: 15px;">
                    <strong>Time:</strong><br>
                    <?php echo date('g:i A', strtotime($class['start_time'])); ?> - 
                    <?php echo date('g:i A', strtotime($class['end_time'])); ?>
                </div>
                
                <!-- Unique confirmation number combining date/time and class ID -->
                <!-- This helps customer service identify the booking if needed -->
                <div style="margin-bottom: 15px;">
                    <strong>Confirmation #:</strong><br>
                    <?php echo 'CLASS-' . date('YmdHis') . '-' . $class_id; ?>
                </div>
            </div>
            
            <!-- Let user know confirmation was sent to their email -->
            <p class="text-muted mb-4">A confirmation email has been sent to your registered email address.</p>
            
            <!-- Action buttons -->
            <!-- Two options: book another class or view all bookings -->
            <div class="d-flex gap-3 justify-content-center">
                <a href="classes_premium.php" class="btn btn-primary fw-600">Book Another</a>
                <a href="dashboard.php" class="btn btn-outline fw-600">My Bookings</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
