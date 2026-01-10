<?php
require_once 'includes/init.php';
requireLogin();

$pageTitle = 'Dashboard';
$userId = currentUserId();

$dashboardError = '';
$successMessage = $_SESSION['success_message'] ?? '';
if (!empty($successMessage)) {
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_martial_art'])) {
        $primarySelection = trim($_POST['martial_art'] ?? '');
        $secondarySelection = trim($_POST['martial_art_secondary'] ?? '');

        $planStmt = $conn->prepare('SELECT m.type AS membership_type FROM users u LEFT JOIN memberships m ON u.membership_id = m.id WHERE u.id = ?');
        if ($planStmt) {
            $planStmt->bind_param('i', $userId);
            $planStmt->execute();
            $planResult = $planStmt->get_result();
            $currentPlanType = $planResult && $planResult->num_rows === 1 ? $planResult->fetch_assoc()['membership_type'] ?? '' : '';
            $planStmt->close();
        } else {
            $currentPlanType = '';
        }

        $normalizedPlan = normalizeMembershipType($currentPlanType);

        if (in_array($normalizedPlan, ['basic', 'intermediate'], true)) {
            if ($primarySelection === '') {
                $dashboardError = 'Pick a martial art before saving your profile.';
            }
            $secondarySelection = '';
        } elseif ($normalizedPlan === 'advanced') {
            if ($primarySelection === '' || $secondarySelection === '') {
                $dashboardError = 'Advanced members must select two martial arts.';
            } elseif (cleanArtName($primarySelection) === cleanArtName($secondarySelection)) {
                $dashboardError = 'Primary and secondary martial arts must be different.';
            }
        } else {
            $dashboardError = 'Martial art preferences can only be updated for Basic, Intermediate, or Advanced plans.';
        }

        if ($dashboardError === '') {
            $updateStmt = $conn->prepare('UPDATE users SET chosen_martial_art = ?, chosen_martial_art_2 = ? WHERE id = ?');
            if ($updateStmt) {
                $updateStmt->bind_param('ssi', $primarySelection, $secondarySelection, $userId);
                if ($updateStmt->execute()) {
                    $_SESSION['success_message'] = 'Martial arts updated.';
                    header('Location: dashboard.php');
                    exit();
                }
            }
            $dashboardError = 'Unable to save your preferences. Please try again.';
        }
    } elseif (isset($_POST['cancel_booking'])) {
        $bookingId = intval($_POST['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            $dashboardError = 'Invalid booking selected.';
        } elseif (cancelBooking($bookingId, $userId)) {
            decrementUserSessions($userId);
            $_SESSION['success_message'] = 'Booking cancelled and your session credit was restored.';
            header('Location: dashboard.php');
            exit();
        } else {
            $dashboardError = 'Unable to cancel the booking. Please try again.';
        }
    }
}

// Fetch user details with their membership information from database using prepared statements
// Using prepared statement with a JOIN to get both user info AND their membership plan
// The LEFT JOIN ensures we get user data even if they don't have a membership yet
$user = null;
$membership = null;
// This SELECT statement JOINs the users table with the memberships table
// So we get the user's name, email, role, AND their membership type, price, description all at once
$stmt = $conn->prepare('SELECT u.username, u.email, u.role, u.membership_id, u.chosen_martial_art, u.chosen_martial_art_2, m.type as membership_label, m.price, m.description, m.type as membership_type FROM users u LEFT JOIN memberships m ON u.membership_id = m.id WHERE u.id = ?');
$stmt->bind_param('i', $userId);  // 'i' means it's an integer parameter
$stmt->execute();
$result = $stmt->get_result();

// Fetch user's booked classes securely
$booked_classes = [];
$stmt_bookings = $conn->prepare('SELECT b.id as booking_id, c.id, c.class_name, c.day_of_week, c.start_time, c.end_time, b.booking_date, b.status FROM bookings b JOIN classes c ON b.class_id = c.id WHERE b.user_id = ? AND b.status = "confirmed" ORDER BY c.day_of_week, c.start_time');
$stmt_bookings->bind_param('i', $userId);
$stmt_bookings->execute();
$result_bookings = $stmt_bookings->get_result();
while ($row = $result_bookings->fetch_assoc()) {
    $booked_classes[] = $row;
}

// Check if user was found in database
if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();  // Get the user and membership data
    // If they have a membership_id, store the membership details in a separate array
    // SECURITY: Build membership array only when present
    if ($user['membership_id']) {
        $membership = [
            'name' => $user['membership_label'],
            'price' => $user['price'],
            'description' => $user['description']
        ];
    }
}

$userPrimaryMartialArt = $user ? ($user['chosen_martial_art'] ?? '') : '';
$userSecondaryMartialArt = $user ? ($user['chosen_martial_art_2'] ?? '') : '';
$currentPlanNormalized = $user ? normalizeMembershipType($user['membership_type'] ?? '') : '';

$martialArts = ['Jiu-jitsu', 'Judo', 'Karate', 'Muay Thai'];

include 'includes/header.php';
?>

<div class="container page-section">
    <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success text-center">
        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($dashboardError)): ?>
    <div class="alert alert-error text-center">
        <?php echo htmlspecialchars($dashboardError, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif; ?>
    <!-- Header with greeting - style="font-size: 2.5rem" makes it big and prominent -->
    <div class="text-center mb-5">
        <h2 class="fw-bold text-deep-dark" style="margin-bottom: 0.5rem;"><i class="bi bi-speedometer2 me-3 text-primary"></i>My Dashboard</h2>
        <p class="text-muted">Welcome back<?php echo $user ? ', ' . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') : ''; ?>!</p>
    </div>

    <!-- Two column layout for membership status and quick actions -->
    <div class="row g-4">
        <!-- Left column: Membership status card -->
        <div class="col-md-6">
            <div class="glass-panel h-100">
                <!-- Card header with icon -->
                <!-- style="font-size: 2.5rem; color: #DC143C" makes the icon big and red -->
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-person-badge text-primary" style="font-size: 2.5rem;"></i>
                    <h5 class="ms-3 mb-0 fw-bold text-deep-dark">Membership Status</h5>
                </div>
                <div>
                    <?php if ($membership): ?>
                    <!-- Display user's current membership plan -->
                    <!-- style="border-bottom: 1px solid #f0f0f0; padding-bottom: 12px" adds separators between items -->
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Current Plan:</span>
                        <span class="text-primary fw-bold"><?php echo htmlspecialchars($membership['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <!-- Show the monthly price -->
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Price:</span>
                        <span class="fw-bold text-deep-dark">$<?php echo number_format((float)$membership['price'], 2); ?>/month</span>
                    </div>
                    <!-- Status badge showing account is active -->
                    <!-- style="display: inline-block; background: rgba(52,227,127,0.15)" green background for active status -->
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Status:</span>
                        <span class="badge badge-success fw-600">Active</span>
                    </div>
                    <!-- Button to change membership plan -->
                    <!-- style="border: 2px solid #DC143C; color: #DC143C" creates an outlined red button -->
                    <a href="prices.php" class="btn btn-outline btn-sm no-underline fw-600"><i class="bi bi-arrow-right me-2"></i>Change Plan</a>
                    <?php else: ?>
                    <!-- Show this if user doesn't have a membership yet -->
                    <div class="text-center py-3">
                        <p class="text-muted mb-3">You don't have an active membership</p>
                        <a href="prices.php" class="btn btn-primary btn-sm no-underline fw-600">Choose a Plan</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right column: Quick actions card with gradient background -->
        <div class="col-md-6">
            <div class="card bg-primary text-white h-100 d-flex flex-column">
                <!-- Icon and heading -->
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-lightning-charge" style="font-size: 2.5rem;"></i>
                    <h5 class="ms-3 mb-0 fw-bold">Quick Actions</h5>
                </div>
                <!-- Description text -->
                <!-- style="flex-grow: 1" makes this paragraph expand to fill space, pushing button to bottom -->
                <p class="flex-grow-1">Browse the timetable and book your next class.</p>
                <!-- Link to classes page -->
                <a class="btn btn-light no-underline fw-600" href="classes_premium.php"><i class="bi bi-calendar-check me-2"></i>View Classes</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-12">
            <div class="glass-panel">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-shield-check text-primary" style="font-size: 2.5rem;"></i>
                    <h5 class="ms-3 mb-0 fw-bold text-deep-dark">Martial Art Preferences</h5>
                </div>
                <?php if (in_array($currentPlanNormalized, ['basic', 'intermediate', 'advanced'], true)): ?>
                <form method="POST" class="row g-3 align-items-end">
                    <input type="hidden" name="update_martial_art" value="1">
                    <div class="col-md-6">
                        <label class="form-label fw-600 text-deep-dark">Primary Martial Art</label>
                        <select name="martial_art" class="form-select" required>
                            <option value="">Select martial art...</option>
                            <?php foreach ($martialArts as $art): ?>
                                <option value="<?php echo htmlspecialchars($art, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($userPrimaryMartialArt === $art) ? 'selected' : ''; ?>><?php echo htmlspecialchars($art, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Change your main discipline anytime without altering your membership.</small>
                    </div>
                    <?php if ($currentPlanNormalized === 'advanced'): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-600 text-deep-dark">Secondary Martial Art</label>
                        <select name="martial_art_secondary" class="form-select" required>
                            <option value="">Select another martial art...</option>
                            <?php foreach ($martialArts as $art): ?>
                                <option value="<?php echo htmlspecialchars($art, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($userSecondaryMartialArt === $art) ? 'selected' : ''; ?>><?php echo htmlspecialchars($art, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Advanced accounts can book two disciplines; both fields are required.</small>
                    </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-pencil-square me-2"></i>Save martial arts</button>
                    </div>
                </form>
                <?php else: ?>
                <p class="text-muted mb-0">Martial art selection is available for Basic, Intermediate, or Advanced members. <a href="prices.php" class="text-primary fw-bold">Upgrade your plan</a> to manage your disciplines.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Social Features Section -->
    <div class="row g-4 mt-3">
        <div class="col-12">
            <!-- Booked Classes Section -->
            <div class="glass-panel">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-calendar-check text-primary" style="font-size: 2.5rem;"></i>
                    <h5 class="ms-3 mb-0 fw-bold text-deep-dark">My Bookings</h5>
                </div>
                <?php if (count($booked_classes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Booked</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booked_classes as $booking): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($booking['class_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['day_of_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(date('g:i A', strtotime($booking['start_time'])) . ' - ' . date('g:i A', strtotime($booking['end_time'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['booking_date'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge bg-success">Confirmed</span></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="booking_id" value="<?php echo intval($booking['booking_id']); ?>">
                                        <input type="hidden" name="cancel_booking" value="1">
                                        <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Cancel this booking? You will get the session back.');">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0">No bookings yet. <a href="classes_premium.php" class="text-primary fw-bold">Browse classes</a> to book your first class.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Social Features Section -->
    <div class="row g-4 mt-3">
        <div class="col-12">
            <!-- White card containing social media links -->
            <!-- style="box-shadow: 0 4px 16px rgba(0,0,0,0.08)" adds subtle shadow for depth -->
            <div class="glass-panel">
                <!-- Card header -->
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-people-fill text-primary" style="font-size: 2.5rem;"></i>
                    <h5 class="ms-3 mb-0 fw-bold text-deep-dark">Community & Social</h5>
                </div>
                <!-- Grid of social media buttons -->
                <div class="row g-3">
                    <!-- Facebook link -->
                    <!-- style="background: #f8f9fa" gives light gray background, "transition: all 0.2s" smooths hover effects -->
                    <div class="col-md-3 col-6">
                        <a href="https://facebook.com" target="_blank" class="btn btn-light w-100 d-flex align-items-center gap-2">
                            <i class="bi bi-facebook text-facebook" style="font-size: 1.5rem;"></i>
                            <span class="fw-bold text-deep-dark">Facebook</span>
                        </a>
                    </div>
                    <!-- Instagram link -->
                    <div class="col-md-3 col-6">
                        <a href="https://instagram.com" target="_blank" class="btn btn-light w-100 d-flex align-items-center gap-2">
                            <i class="bi bi-instagram text-instagram" style="font-size: 1.5rem;"></i>
                            <span class="fw-bold text-deep-dark">Instagram</span>
                        </a>
                    </div>
                    <!-- YouTube link -->
                    <div class="col-md-3 col-6">
                        <a href="https://youtube.com" target="_blank" class="btn btn-light w-100 d-flex align-items-center gap-2">
                            <i class="bi bi-youtube text-youtube" style="font-size: 1.5rem;"></i>
                            <span class="fw-bold text-deep-dark">YouTube</span>
                        </a>
                    </div>
                    <!-- News section placeholder -->
                    <div class="col-md-3 col-6">
                        <a href="#" class="btn btn-light w-100 d-flex align-items-center gap-2">
                            <i class="bi bi-newspaper text-primary" style="font-size: 1.5rem;"></i>
                            <span class="fw-bold text-deep-dark">News</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
