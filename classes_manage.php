<?php
/**
 * classes_manage.php
 * Admin-only page for managing (Create, Read, Update, Delete) martial art classes
 * Admins can create class schedules, edit times, and delete classes
 */

session_start();
include 'includes/db.php';

// Security check: only admins can manage classes
// If user is not logged in or not an admin, redirect them to homepage
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Get the action from URL or POST data to determine what operation to perform
// Examples: action=create, action=delete, action=edit, action=update
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// Switch statement to handle different CRUD operations
switch ($action) {
    // =========================================================
    // CREATE CLASS - Process form submission to add new class
    // =========================================================
    case 'create':
        // Only process if form was submitted via POST method
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data and trim whitespace
            $className = trim($_POST['class_name'] ?? '');
            $day = trim($_POST['day'] ?? '');
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';
            $martialArt = trim($_POST['martial_art'] ?? '');
            $isKidsClass = isset($_POST['is_kids_class']) ? 1 : 0;

            // Only proceed if all required fields have values
            if ($className && $day && $startTime && $endTime && $martialArt) {
                // Prepare INSERT query with placeholder values
                // Using prepared statement prevents SQL injection
                $stmt = $conn->prepare('INSERT INTO classes (class_name, day_of_week, start_time, end_time, martial_art, is_kids_class) VALUES (?, ?, ?, ?, ?, ?)');
                // Bind variables: s=string, i=integer
                $stmt->bind_param('sssssi', $className, $day, $startTime, $endTime, $martialArt, $isKidsClass);
                // Execute the INSERT query
                $stmt->execute();
            }
        }
        // After creating, go back to admin dashboard
        header('Location: admin.php');
        exit();

    // =========================================================
    // DELETE CLASS - Remove class from database
    // =========================================================
    case 'delete':
        // Get class ID from URL parameter and convert to integer (safe from injection)
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Only delete if ID is valid (greater than 0)
        if ($id > 0) {
            // Prepare DELETE query with placeholder for ID
            // Using prepared statement is safe from SQL injection
            $stmt = $conn->prepare('DELETE FROM classes WHERE id = ?');
            // Bind the ID variable: i=integer
            $stmt->bind_param('i', $id);
            // Execute the DELETE query
            $stmt->execute();
        }
        // After deleting, go back to admin dashboard
        header('Location: admin.php');
        exit();

    // =========================================================
    // EDIT CLASS - Show form to edit existing class schedule
    // =========================================================
    case 'edit':
        // Get class ID from URL and convert to integer
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Validate ID is positive
        if ($id <= 0) {
            header('Location: admin.php');
            exit();
        }
        
        // Fetch class record from database
        // Using prepared statement to safely query with ID
        $stmt = $conn->prepare('SELECT * FROM classes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $class = $result->fetch_assoc();
        
        // If class not found, redirect to admin page
        if (!$class) {
            header('Location: admin.php');
            exit();
        }
        
        // Set page title for header
        $pageTitle = 'Edit Class';
        include 'includes/header.php';
        ?>
        <div class="container page-section narrow-container">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="admin.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-2"></i>Back to Dashboard</a>
                <h2 class="mb-0">Edit Class</h2>
            </div>
            <!-- Form to update class info -->
            <form method="POST" class="glass-panel">
                <!-- Hidden fields to specify action and ID for processing -->
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo intval($class['id']); ?>">
                
                <!-- Class name input field - required -->
                <div class="mb-3">
                    <label class="form-label">Class Name</label>
                    <input type="text" name="class_name" class="form-control" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                </div>
                
                <!-- Day of week dropdown - required -->
                <!-- Shows all 7 days with the current day pre-selected -->
                <div class="mb-3">
                    <label class="form-label">Day of Week</label>
                    <select name="day" class="form-select" required>
                        <?php
                        // Array of all weekdays
                        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        // Loop through each day and check if it matches the stored day
                        foreach ($days as $dayOption) {
                            // If this day matches the class's day, mark it as selected
                            $selected = ($dayOption === $class['day_of_week']) ? 'selected' : '';
                            echo '<option ' . $selected . '>' . $dayOption . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Start and end times in a row -->
                <div class="row g-3">
                    <!-- Start time input - required, uses HTML5 time picker -->
                    <div class="col-md-6">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($class['start_time']); ?>" required>
                    </div>
                    
                    <!-- End time input - required, uses HTML5 time picker -->
                    <div class="col-md-6">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($class['end_time']); ?>" required>
                    </div>
                </div>

                <!-- Martial art selection -->
                <div class="mb-3">
                    <label class="form-label">Martial Art</label>
                    <input type="text" name="martial_art" class="form-control" value="<?php echo htmlspecialchars($class['martial_art'] ?? ''); ?>" placeholder="e.g., Karate, Judo, Muay Thai" required>
                </div>

                <!-- Kids class checkbox -->
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_kids_class" class="form-check-input" id="is_kids_class" <?php echo (isset($class['is_kids_class']) && $class['is_kids_class']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_kids_class">Kids Class</label>
                </div>
                
                <!-- Action buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="admin.php" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
        <?php
        include 'includes/footer.php';
        exit();

    // =========================================================
    // UPDATE CLASS - Process form submission to update existing class
    // =========================================================
    case 'update':
        // Only process if form was submitted via POST method
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data from POST variables
            $id = intval($_POST['id'] ?? 0);
            $className = trim($_POST['class_name'] ?? '');
            $day = trim($_POST['day'] ?? '');
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';
            $martialArt = trim($_POST['martial_art'] ?? '');
            $isKidsClass = isset($_POST['is_kids_class']) ? 1 : 0;

            // Only update if ID is valid and all fields are provided
            if ($id > 0 && $className && $day && $startTime && $endTime && $martialArt) {
                // Prepare UPDATE query with placeholders for new values
                // Using prepared statement prevents SQL injection
                $stmt = $conn->prepare('UPDATE classes SET class_name = ?, day_of_week = ?, start_time = ?, end_time = ?, martial_art = ?, is_kids_class = ? WHERE id = ?');
                // Bind variables: s=string, i=integer
                $stmt->bind_param('sssssii', $className, $day, $startTime, $endTime, $martialArt, $isKidsClass, $id);
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
