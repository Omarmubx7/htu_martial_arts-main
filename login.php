<?php 
/**
 * login.php
 * User login page - authenticates users against the database
 * Uses prepared statements for security and password_verify() for password checking
 */

session_start();
include 'includes/db.php';
$pageTitle = "Login";

// ===================================================================
// HANDLE LOGIN LOGIC - Process form submission
// ===================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from the login form (safely)
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Query the database to find user with this email
    // Using prepared statement to prevent SQL injection - very important for security!
    // The ? is a placeholder that gets safely replaced with the actual email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    // 's' means the parameter is a string
    $stmt->bind_param("s", $email);
    // Execute the prepared query with the email value
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if exactly one user was found with this email
    if ($result->num_rows == 1) {
        // Get the user data from database as an associative array
        $user = $result->fetch_assoc();
        
        // Verify the password they entered matches the hashed password in database
        // password_verify() safely compares plain text password with bcrypt hash
        // Never compare plain text passwords directly - always use password_verify()!
        if (password_verify($password, $user['password'])) {
            // Password is correct! Store user info in SESSION variables
            // This keeps them logged in as they navigate the site
            $_SESSION['user_id'] = $user['id'];
            // Check is_admin flag; set role to 'admin' if true, else default to 'member'
            $role = (isset($user['is_admin']) && $user['is_admin'] == 1) ? 'admin' : 'member';
            $_SESSION['role'] = $role; // Either 'admin' or 'member'
            $_SESSION['username'] = $user['username'];
            
            // Redirect to different pages based on user role
            // Admins go to admin panel, regular members go to their dashboard
            if ($role === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            // Password doesn't match - show generic error
            // Security best practice: don't say which field was wrong (prevents email enumeration)
            $error = "Invalid email or password.";
        }
    } else {
        // Email not found in database - show generic error for security
        // Again, don't reveal if email exists or not
        $error = "Invalid email or password.";
    }
}
include 'includes/header.php';
?>

<!-- Login form container with centered layout -->
<!-- Centered both vertically and horizontally with flex display -->
<div class="container" style="padding: 80px 0; min-height: 70vh; display: flex; align-items: center;">
    <!-- Inner wrapper - centers form horizontally -->
    <div style="width: 100%; max-width: 420px; margin: 0 auto;">
        <!-- Show error message if login failed -->
        <!-- Uses semi-transparent red background for error states -->
        <?php if(isset($error)): ?>
            <div style="background: rgba(220,20,60,0.1); border: 1px solid rgba(220,20,60,0.3); color: #DC143C; padding: 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <!-- Show success message if one exists (e.g., from signup redirect) -->
        <!-- Uses semi-transparent green background for success states -->
        <?php if(isset($_SESSION['success'])): ?>
            <div style="background: rgba(52,227,127,0.1); border: 1px solid rgba(52,227,127,0.3); color: #34e37f; padding: 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <!-- Remove success message so it doesn't show again on next page load -->
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <!-- Login form with glass-panel styling (white background with shadow) -->
        <!-- Consistent padding and border-radius across all form pages -->
        <form method="POST" class="glass-panel" style="padding: 40px;">
            <!-- Email input field -->
            <!-- Form labels are dark with red icon for visual hierarchy -->
            <div class="mb-4">
                <label class="form-label mb-2 fw-600" style="color: #1a1a2e; font-size: 0.95rem;">
                    <i class="bi bi-envelope me-2" style="color: #DC143C;"></i>Email Address
                </label>
                <!-- Input with consistent styling: light border, padding, and border-radius -->
                <input type="email" name="email" class="form-control" placeholder="your@email.com" required style="border: 1px solid #e0e0e0; padding: 12px 14px; border-radius: 8px; font-size: 0.95rem;">
            </div>
            
            <!-- Password input field -->
            <!-- type="password" hides characters for privacy -->
            <div class="mb-4">
                <label class="form-label mb-2 fw-600" style="color: #1a1a2e; font-size: 0.95rem;">
                    <i class="bi bi-lock me-2" style="color: #DC143C;"></i>Password
                </label>
                <!-- Input with consistent styling matching email field -->
                <input type="password" name="password" class="form-control" placeholder="••••••••" required style="border: 1px solid #e0e0e0; padding: 12px 14px; border-radius: 8px; font-size: 0.95rem;">
            </div>
            
            <!-- Submit button with primary gradient and hover effects -->
            <!-- Uses inline style but follows CSS variables from theme -->
            <button type="submit" class="btn w-100 fw-bold" style="background: linear-gradient(135deg, #DC143C 0%, #a00000 100%); color: white; padding: 14px; border-radius: 8px; border: none; font-size: 0.95rem; transition: all 0.3s ease; box-shadow: 0 4px 16px rgba(220,20,60,0.3);">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
            
            <!-- Link to pricing for new users -->
            <!-- Signup flow: prices.php > signup.php > login on return -->
            <div class="text-center mt-4">
                <span style="color: #666; font-size: 0.9rem;">Don't have an account?</span> 
                <a href="prices.php" style="color: #DC143C; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Choose a Plan</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
