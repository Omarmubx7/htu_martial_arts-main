<?php
require_once 'includes/init.php';
requireLogin();

$class_id = intval($_GET['id'] ?? 0);
$userId = currentUserId();
if ($userId === null) {
    redirectTo('login.php');
}

$stmt = $conn->prepare("SELECT id, class_name, martial_art, day_of_week, start_time, end_time, age_group FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    redirectTo('classes_premium.php');
}

try {
    $conn->begin_transaction();

    $access_check = canUserBookClass(
        $userId,
        $class['martial_art'],
        $class['age_group'] === 'Kids',
        $class['class_name']
    );

    if (!$access_check['can_book']) {
        throw new RuntimeException($access_check['reason'] ?? 'You cannot book this class.');
    }

    if (bookingExists($userId, $class_id)) {
        throw new RuntimeException('You already booked this class.');
    }

    if (!recordBooking($userId, $class_id)) {
        throw new RuntimeException('Failed to save your booking.');
    }

    if (!incrementUserSessions($userId)) {
        throw new RuntimeException('Failed to update your weekly session count.');
    }

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION['booking_error'] = $e->getMessage();
    redirectTo('classes_premium.php');
}

$pageTitle = 'Booking Confirmed';
include 'includes/header.php';
?>
<section class="page-section py-5">
    <div class="container">
        <div class="narrow-container">
            <div class="glass-panel p-4 p-md-5 text-center">
                <div class="text-start mb-4">
                    <a href="classes_premium.php" class="btn btn-outline btn-sm fw-bold">
                        <i class="bi bi-arrow-left me-2"></i>Back to Classes
                    </a>
                </div>
                <div class="display-4 text-primary mb-3" aria-hidden="true">✅</div>
                <h1 class="fw-bold text-deep-dark mb-3">Booking Confirmed!</h1>
                <p class="text-muted mb-4">Your spot is locked in. We have emailed you confirmation and the details are below.</p>
                <div class="card border-0 shadow-sm text-start mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold text-primary" style="text-transform: uppercase;">Class Details</h3>
                        <div class="mb-3">
                            <strong>Class:</strong><br>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Day:</strong><br>
                            <?php echo htmlspecialchars($class['day_of_week']); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Time:</strong><br>
                            <?php echo date('g:i A', strtotime($class['start_time'])); ?> -
                            <?php echo date('g:i A', strtotime($class['end_time'])); ?>
                        </div>
                        <div>
                            <strong>Confirmation #:</strong><br>
                            <?php echo 'CLASS-' . date('YmdHis') . '-' . $class_id; ?>
                        </div>
                    </div>
                </div>
                <p class="text-muted mb-4">Confirmation has been sent to your registered email address.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="classes_premium.php" class="btn btn-primary fw-bold">Book Another</a>
                    <a href="dashboard.php" class="btn btn-outline fw-bold">My Bookings</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
