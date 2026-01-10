<?php
/**
 * admin_actions.php
 * Admin-only CRUD actions for instructors and classes.
 *
 * This file centralizes admin CRUD actions for instructors and classes.
 * It supports:
 * - resource=instructor|class
 * - action=create|delete|edit|update
 */

require_once 'includes/init.php';
requireAdmin();

$resource = $_GET['resource'] ?? ($_POST['resource'] ?? '');
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

function adminRedirectToDashboard() {
    header('Location: admin.php');
    exit();
}

function getIntParam($key) {
    if (isset($_POST[$key])) {
        return intval($_POST[$key]);
    }
    if (isset($_GET[$key])) {
        return intval($_GET[$key]);
    }
    return 0;
}

if (!in_array($resource, ['instructor', 'class'], true)) {
    adminRedirectToDashboard();
}

if (!in_array($action, ['create', 'delete', 'edit', 'update'], true)) {
    adminRedirectToDashboard();
}

// ------------------------------------------------------------
// INSTRUCTORS
// ------------------------------------------------------------
if ($resource === 'instructor') {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = trim($_POST['name'] ?? '');
                $specialty = trim($_POST['specialty'] ?? '');
                $bio = trim($_POST['bio'] ?? '');

                if ($name !== '') {
                    $stmt = $conn->prepare('INSERT INTO instructors (name, specialty, bio) VALUES (?, ?, ?)');
                    $stmt->bind_param('sss', $name, $specialty, $bio);
                    $stmt->execute();
                }
            }
            adminRedirectToDashboard();

        case 'delete':
            $id = getIntParam('id');
            if ($id > 0) {
                $stmt = $conn->prepare('DELETE FROM instructors WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
            }
            adminRedirectToDashboard();

        case 'edit':
            $id = getIntParam('id');
            if ($id <= 0) {
                adminRedirectToDashboard();
            }

            $stmt = $conn->prepare('SELECT * FROM instructors WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $instructor = $result->fetch_assoc();

            if (!$instructor) {
                adminRedirectToDashboard();
            }

            $pageTitle = 'Edit Instructor';
            include 'includes/header.php';
            ?>
            <div class="container mt-5" style="max-width: 720px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <a href="admin.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-2"></i>Back to Dashboard</a>
                    <h2 class="section-title mb-0"><i class="bi bi-person-lines-fill me-2"></i>Edit Instructor</h2>
                </div>

                <form method="POST" action="admin_actions.php" class="glass-panel p-4">
                    <input type="hidden" name="resource" value="instructor">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo intval($instructor['id']); ?>">

                    <div class="mb-4">
                        <label class="form-label mb-2"><i class="bi bi-person me-2"></i>Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($instructor['name']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label mb-2"><i class="bi bi-award me-2"></i>Specialty</label>
                        <input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($instructor['specialty']); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label mb-2"><i class="bi bi-card-text me-2"></i>Bio</label>
                        <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($instructor['bio']); ?></textarea>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Save Changes</button>
                        <a href="admin.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php
            include 'includes/footer.php';
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = getIntParam('id');
                $name = trim($_POST['name'] ?? '');
                $specialty = trim($_POST['specialty'] ?? '');
                $bio = trim($_POST['bio'] ?? '');

                if ($id > 0 && $name !== '') {
                    $stmt = $conn->prepare('UPDATE instructors SET name = ?, specialty = ?, bio = ? WHERE id = ?');
                    $stmt->bind_param('sssi', $name, $specialty, $bio, $id);
                    $stmt->execute();
                }
            }
            adminRedirectToDashboard();
    }
}

// ------------------------------------------------------------
// CLASSES
// ------------------------------------------------------------
if ($resource === 'class') {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $className = trim($_POST['class_name'] ?? '');
                $day = trim($_POST['day'] ?? '');
                $startTime = $_POST['start_time'] ?? '';
                $endTime = $_POST['end_time'] ?? '';
                $martialArt = trim($_POST['martial_art'] ?? '');
                $isKidsClass = isset($_POST['is_kids_class']) ? 1 : 0;

                if ($className !== '' && $day !== '' && $startTime !== '' && $endTime !== '' && $martialArt !== '') {
                    $stmt = $conn->prepare('INSERT INTO classes (class_name, day_of_week, start_time, end_time, martial_art, is_kids_class) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('sssssi', $className, $day, $startTime, $endTime, $martialArt, $isKidsClass);
                    $stmt->execute();
                }
            }
            adminRedirectToDashboard();

        case 'delete':
            $id = getIntParam('id');
            if ($id > 0) {
                $stmt = $conn->prepare('DELETE FROM classes WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
            }
            adminRedirectToDashboard();

        case 'edit':
            $id = getIntParam('id');
            if ($id <= 0) {
                adminRedirectToDashboard();
            }

            $stmt = $conn->prepare('SELECT * FROM classes WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $class = $result->fetch_assoc();

            if (!$class) {
                adminRedirectToDashboard();
            }

            $pageTitle = 'Edit Class';
            include 'includes/header.php';
            ?>
            <div class="container page-section narrow-container">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <a href="admin.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-2"></i>Back to Dashboard</a>
                    <h2 class="mb-0">Edit Class</h2>
                </div>

                <form method="POST" action="admin_actions.php" class="glass-panel">
                    <input type="hidden" name="resource" value="class">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo intval($class['id']); ?>">

                    <div class="mb-3">
                        <label class="form-label">Class Name</label>
                        <input type="text" name="class_name" class="form-control" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Day of Week</label>
                        <select name="day" class="form-select" required>
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            foreach ($days as $dayOption) {
                                $selected = ($dayOption === $class['day_of_week']) ? 'selected' : '';
                                echo '<option ' . $selected . '>' . $dayOption . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($class['start_time']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($class['end_time']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Martial Art</label>
                        <input type="text" name="martial_art" class="form-control" value="<?php echo htmlspecialchars($class['martial_art'] ?? ''); ?>" placeholder="e.g., Karate, Judo, Muay Thai" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_kids_class" class="form-check-input" id="is_kids_class" <?php echo (!empty($class['is_kids_class'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_kids_class">Kids Class</label>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="admin.php" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
            <?php
            include 'includes/footer.php';
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = getIntParam('id');
                $className = trim($_POST['class_name'] ?? '');
                $day = trim($_POST['day'] ?? '');
                $startTime = $_POST['start_time'] ?? '';
                $endTime = $_POST['end_time'] ?? '';
                $martialArt = trim($_POST['martial_art'] ?? '');
                $isKidsClass = isset($_POST['is_kids_class']) ? 1 : 0;

                if ($id > 0 && $className !== '' && $day !== '' && $startTime !== '' && $endTime !== '' && $martialArt !== '') {
                    $stmt = $conn->prepare('UPDATE classes SET class_name = ?, day_of_week = ?, start_time = ?, end_time = ?, martial_art = ?, is_kids_class = ? WHERE id = ?');
                    $stmt->bind_param('sssssii', $className, $day, $startTime, $endTime, $martialArt, $isKidsClass, $id);
                    $stmt->execute();
                }
            }
            adminRedirectToDashboard();
    }
}

adminRedirectToDashboard();
