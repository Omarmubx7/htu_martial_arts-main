<?php
/**
 * instructors_manage.php
 * Admin-only page for managing (Create, Read, Update, Delete) instructors
 * All operations require admin role - accessed from admin.php
 */

require_once 'includes/init.php';
requireAdmin();

// Get the action from URL or POST data to determine what operation to perform
// Examples: action=create, action=delete, action=edit, action=update
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// Switch statement to handle different CRUD operations
switch ($action) {
    // =========================================================
    // CREATE INSTRUCTOR - Process form submission to add new instructor
    // =========================================================
    case 'create':
        // Only process if form was submitted via POST method
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data and trim whitespace from edges
            $name = trim($_POST['name'] ?? '');
            $specialty = trim($_POST['specialty'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            // Only proceed if name is provided (it's required)
            if ($name) {
                // Prepare INSERT query with 3 placeholder values
                // Using prepared statement prevents SQL injection attacks
                $stmt = $conn->prepare('INSERT INTO instructors (name, specialty, bio) VALUES (?, ?, ?)');
                // Bind the variables: s=string for all three
                $stmt->bind_param('sss', $name, $specialty, $bio);
                // Execute the INSERT query
                $stmt->execute();
            }
        }
        // After creating, go back to admin dashboard
        header('Location: admin.php');
        exit();

    // =========================================================
    // DELETE INSTRUCTOR - Remove instructor from database
    // =========================================================
    case 'delete':
        // Get instructor ID from URL parameter and convert to integer (safe from injection)
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Only delete if ID is valid (greater than 0)
        if ($id > 0) {
            // Prepare DELETE query with placeholder for ID
            // Using prepared statement is safe from SQL injection
            $stmt = $conn->prepare('DELETE FROM instructors WHERE id = ?');
            // Bind the ID variable: i=integer
            $stmt->bind_param('i', $id);
            // Execute the DELETE query
            $stmt->execute();
        }
        // After deleting, go back to admin dashboard
        header('Location: admin.php');
        exit();

    // =========================================================
    // EDIT INSTRUCTOR - Show form to edit existing instructor details
    // =========================================================
    case 'edit':
        // Get instructor ID from URL and convert to integer
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Validate ID is positive
        if ($id <= 0) {
            header('Location: admin.php');
            exit();
        }
        
        // Fetch instructor record from database
        // Using prepared statement to safely query with ID
        $stmt = $conn->prepare('SELECT * FROM instructors WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $instructor = $result->fetch_assoc();
        
        // If instructor not found, redirect to admin page
        if (!$instructor) {
            header('Location: admin.php');
            exit();
        }
        
        // Set page title for header
        $pageTitle = 'Edit Instructor';
        include 'includes/header.php';
        ?>
        <div class="container mt-5" style="max-width: 720px;">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="admin.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-2"></i>Back to Dashboard</a>
                <h2 class="section-title mb-0"><i class="bi bi-person-lines-fill me-2"></i>Edit Instructor</h2>
            </div>
            <!-- Form to update instructor info -->
            <form method="POST" class="glass-panel p-4">
                <!-- Hidden fields to specify action and ID for processing -->
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo intval($instructor['id']); ?>">
                
                <!-- Name input field - required -->
                <div class="mb-4">
                    <label class="form-label mb-2"><i class="bi bi-person me-2"></i>Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($instructor['name']); ?>" required>
                </div>
                
                <!-- Specialty input field - optional -->
                <div class="mb-4">
                    <label class="form-label mb-2"><i class="bi bi-award me-2"></i>Specialty</label>
                    <input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($instructor['specialty']); ?>">
                </div>
                
                <!-- Bio textarea field - optional, allows multiple lines -->
                <div class="mb-4">
                    <label class="form-label mb-2"><i class="bi bi-card-text me-2"></i>Bio</label>
                    <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($instructor['bio']); ?></textarea>
                </div>
                
                <!-- Action buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-danger">Save Changes</button>
                    <a href="admin.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        <?php
        include 'includes/footer.php';
        exit();

    // =========================================================
    // UPDATE INSTRUCTOR - Process form submission to update existing instructor
    // =========================================================
    case 'update':
        // Only process if form was submitted via POST method
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data from POST variables
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $specialty = trim($_POST['specialty'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            // Only update if ID is valid and name is provided
            if ($id > 0 && $name) {
                // Prepare UPDATE query with placeholders for new values
                // Using prepared statement prevents SQL injection
                $stmt = $conn->prepare('UPDATE instructors SET name = ?, specialty = ?, bio = ? WHERE id = ?');
                // Bind variables: s=string for name/specialty/bio, i=integer for ID
                $stmt->bind_param('sssi', $name, $specialty, $bio, $id);
                // Execute the UPDATE query
                $stmt->execute();
            }
        }
        // After updating, go back to admin dashboard
        header('Location: admin.php');
        exit();

    default:
        // If action is not recognized, go back to admin page
        header('Location: admin.php');
        exit();
}
?>
