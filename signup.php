<?php
require_once 'includes/init.php';

$pageTitle = "Sign Up";

// REQUIRE plan selection - redirect to prices if no plan chosen
// This makes sure users can't just skip the prices page and signup without selecting a plan
$planId = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : null;
if (!$planId) {
    redirectTo('prices.php');
}

// Fetch the selected membership plan from database
// Using a prepared statement to avoid SQL injection - safer than just putting the ID directly in the query
$selectedPlan = getMembershipPlanById((int)$planId);
if (!$selectedPlan) {
    redirectTo('prices.php');
}

$error = '';
$username = '';
$email = '';
$martialArt = '';
$martialArtSecondary = '';

// Handle form submission when user clicks Create Account button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data the user submitted from the form - trim() removes extra spaces
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $planId = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : null;
    $martialArt = trim($_POST['martial_art'] ?? '');
    $martialArtSecondary = trim($_POST['martial_art_secondary'] ?? '');

    // Re-fetch the plan safely (don't trust the hidden input).
    $freshPlan = $planId ? getMembershipPlanById((int)$planId) : null;
    $membershipType = $freshPlan['type'] ?? null;

    // Validate martial art selection for certain membership tiers
    // Basic, Intermediate, and Advanced plans require the user to pick which martial art they want
    // But Junior plan doesn't need this since kids have access to all classes
    if (in_array($membershipType, ['Basic', 'Intermediate', 'Advanced']) && $martialArt === '') {
        $error = 'Please select a martial art.';
    } elseif ($username === '' || $email === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } else {
        // Before creating the account, check if this email is already registered in the database
        // Can't have duplicate emails - each person gets one account
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult && $checkResult->num_rows > 0) {
            // Email already exists, show error instead of creating duplicate account
            $error = 'Email already registered.';
        } else {
            // Email is unique, safe to proceed with account creation
            // Hash the password using PASSWORD_DEFAULT which is bcrypt - never store plain passwords!
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // INSERT statement to add new user to database
            // Using prepared statement with ? placeholders to prevent SQL injection
            $insertStmt = $conn->prepare("INSERT INTO users (username, email, password, role, membership_id, chosen_martial_art, chosen_martial_art_2) VALUES (?, ?, ?, 'member', ?, ?, ?)");
            // Bind the values - 's' for string, 'i' for integer. "sssiss" = string, string, string, integer, string, string
            $insertStmt->bind_param("sssiss", $username, $email, $hashedPassword, $planId, $martialArt, $martialArtSecondary);

            // Execute the INSERT query - if it works, the new user is now in the database
            if ($insertStmt->execute()) {
                // Get the ID of the newly created user (database auto-generates this)
                $newUserId = $insertStmt->insert_id ?: $conn->insert_id;
                // Store user info in SESSION so they stay logged in after signup
                // SESSION persists across page visits until they logout
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['role'] = 'member';
                $_SESSION['username'] = $username;

                // Send them to their dashboard now that they're signed up and logged in
                redirectTo('dashboard.php');
            } else {
                $error = 'Error creating account. Please try again.';
            }
        }
    }
}

if (!empty($error)) {
    addFlashToast($error, 'danger');
    $error = '';
}

include 'includes/header.php';
?>

<!-- Main signup form container - refactored to shared utility classes -->
<section class="page-section signup-section">
    <div class="container">
    <div class="narrow-container">
        <!-- Back Button - light gray background, inline styles for custom look -->
        <div class="mb-3">
            <a href="prices.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-2"></i>Back to Plans</a>
        </div>
        <!-- Header section with title and subtitle -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-deep-dark" style="margin-bottom: 0.5rem;">Join HTU Martial Arts</h2>
            <p class="text-muted">Start your martial arts journey today</p>
        </div>

        <!-- Show which plan the user selected - using inline styles for light purple background -->
        <!-- style="background: rgba(95,92,241,0.1)" is a very light purple with transparency -->
        <?php if ($selectedPlan): ?>
            <div class="glass-panel p-3 mb-4">
                <i class="bi bi-star-fill me-2 text-primary"></i>
                You are signing up for the <strong><?php echo htmlspecialchars($selectedPlan['type']); ?></strong> plan
                ($<?php echo number_format($selectedPlan['price'], 2); ?>/month)
            </div>
        <?php endif; ?>

        <!-- Signup form - POST method sends data securely to this same page for processing -->
        <form method="POST" class="glass-panel">
            <input type="hidden" name="plan_id" value="<?php echo $planId ? intval($planId) : ''; ?>">

            <!-- Full Name Field - using inline styles to customize borders and padding -->
            <!-- style="border: 1px solid #e0e0e0" gives a light gray border, "padding: 12px" adds space inside -->
            <div class="mb-4">
                <label class="form-label mb-2 fw-600 text-deep-dark"><i class="bi bi-person me-2 text-primary"></i>Full Name</label>
                <input type="text" name="username" class="form-control" placeholder="John Doe" required value="<?php echo htmlspecialchars($username); ?>">
            </div>

            <!-- Email Field - styling matches the name field for consistency -->
            <div class="mb-4">
                <label class="form-label mb-2 fw-600 text-deep-dark"><i class="bi bi-envelope me-2 text-primary"></i>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?php echo htmlspecialchars($email); ?>">
            </div>

            <!-- Password Field - inline styles same as other inputs for consistent look -->
            <div class="mb-4">
                <label class="form-label mb-2 fw-600 text-deep-dark"><i class="bi bi-lock me-2 text-primary"></i>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <?php 
            // Show martial art selection for tiers that require it
            if ($selectedPlan && in_array($selectedPlan['type'], ['Basic', 'Intermediate', 'Advanced'])): 
            ?>
            <div class="mb-4">
                <label class="form-label mb-2 fw-600 text-deep-dark"><i class="bi bi-star me-2 text-primary"></i>Choose Your Martial Art</label>
                <select name="martial_art" class="form-select" required>
                    <option value="">Select a martial art...</option>
                    <option value="Jiu-jitsu" <?php echo ($martialArt === 'Jiu-jitsu') ? 'selected' : ''; ?>>Jiu-jitsu</option>
                    <option value="Judo" <?php echo ($martialArt === 'Judo') ? 'selected' : ''; ?>>Judo</option>
                    <option value="Karate" <?php echo ($martialArt === 'Karate') ? 'selected' : ''; ?>>Karate</option>
                    <option value="Muay Thai" <?php echo ($martialArt === 'Muay Thai') ? 'selected' : ''; ?>>Muay Thai</option>
                </select>
                <small class="text-muted d-block mt-2">
                    You will have access to classes in your selected martial art.
                    <?php 
                    if ($selectedPlan['type'] === 'Basic') echo 'Max 2 sessions per week.';
                    elseif ($selectedPlan['type'] === 'Intermediate') echo 'Max 3 sessions per week.';
                    elseif ($selectedPlan['type'] === 'Advanced') echo 'You can select a second martial art below. Max 5 sessions per week.';
                    ?>
                </small>
            </div>
            <?php if ($selectedPlan && $selectedPlan['type'] === 'Advanced'): ?>
            <div class="mb-4">
                <label class="form-label mb-2 fw-600 text-deep-dark"><i class="bi bi-star me-2 text-primary"></i>Optional Second Martial Art</label>
                <select name="martial_art_secondary" class="form-select">
                    <option value="">Choose another martial art (optional)</option>
                    <option value="Jiu-jitsu" <?php echo ($martialArtSecondary === 'Jiu-jitsu') ? 'selected' : ''; ?>>Jiu-jitsu</option>
                    <option value="Judo" <?php echo ($martialArtSecondary === 'Judo') ? 'selected' : ''; ?>>Judo</option>
                    <option value="Karate" <?php echo ($martialArtSecondary === 'Karate') ? 'selected' : ''; ?>>Karate</option>
                    <option value="Muay Thai" <?php echo ($martialArtSecondary === 'Muay Thai') ? 'selected' : ''; ?>>Muay Thai</option>
                </select>
                <small class="text-muted d-block mt-2">Chosen second arts help you book dual-specialty sessions faster and satisfy the Advanced plan allowance.</small>
            </div>
            <?php endif; ?>
            <?php elseif ($selectedPlan && $selectedPlan['type'] === 'Junior'): ?>
            <div class="glass-panel p-3 mb-4">
                <i class="bi bi-info-circle me-2 text-primary"></i>Your plan includes access to all Kids classes!
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100 fw-bold mb-3"><i class="bi bi-check-circle me-2"></i>Create Account</button>

            <!-- Link to login page for existing members -->
            <p class="text-center mb-0"><span class="text-muted">Already a member?</span> <a href="login.php" class="text-primary no-underline fw-600">Login here</a></p>
        </form>
    </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
