<?php
session_start();
include 'includes/db.php';

// Security gate: require logged-in admin before any output
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$pageTitle = "Admin Dashboard";
include 'includes/header.php';
?>

<div class="container page-section">
    <!-- Back to Homepage link with styled button -->
    <!-- style="background: #f8f9fa" light gray background for contrast -->
    <div class="mb-3">
        <a href="index.php" class="btn btn-light btn-sm no-underline"><i class="bi bi-arrow-left me-2"></i>Back to Home</a>
    </div>
    
    <!-- Page title and description -->
    <div class="text-center mb-5">
        <h2 class="section-title mb-2"><i class="bi bi-gear me-3"></i>Admin Dashboard</h2>
        <p class="text-muted">Manage instructors, classes, and membership plans</p>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 1: MANAGE INSTRUCTORS              -->
    <!-- ========================================== -->
    <!-- style="border-bottom: 2px solid" creates a separator line under the heading -->
    <h3 class="mt-5 mb-2 text-deep-dark"><i class="bi bi-people me-2"></i>Manage Instructors</h3>
    <div class="divider divider-primary mb-3"></div>

    <!-- Form to Add New Instructor -->
    <!-- This form POSTs to instructors_manage.php to INSERT a new instructor into database -->
    <!-- Using glass-panel class for semi-transparent background styling -->
    <div class="glass-panel p-4 mb-4">
        <h4>Add a New Instructor</h4>
        <!-- Form action points to instructors_manage.php with action=create parameter -->
        <!-- This triggers the CREATE logic in that file to INSERT the new instructor -->
        <form action="instructors_manage.php?action=create" method="POST">
            <div class="row">
                <!-- Instructor name field -->
                <div class="col-md-4 mb-3">
                    <label>Instructor Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <!-- Martial art specialty field -->
                <div class="col-md-4 mb-3">
                    <label>Specialty</label>
                    <input type="text" name="specialty" class="form-control">
                </div>
                <!-- Short biography field -->
                <div class="col-md-4 mb-3">
                    <label>Biography</label>
                    <input type="text" name="bio" class="form-control" placeholder="Short bio...">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Instructor</button>
        </form>
    </div>

    <!-- List of Instructors -->
    <div class="glass-panel p-3">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Specialty</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT id, name, specialty FROM instructors");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['specialty'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo '<td>';
                    // EDIT BUTTON
                    echo '<a href="instructors_manage.php?action=edit&id=' . intval($row['id']) . '" class="btn btn-warning btn-sm me-2">Edit</a>';
                    // DELETE BUTTON
                    echo '<a href="instructors_manage.php?action=delete&id=' . intval($row['id']) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                    echo '</td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No instructors found.</td></tr>";
            }
            $stmt->close();
            ?>
        </tbody>
    </table>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 2: MANAGE CLASSES (TIMETABLE)      -->
    <!-- ========================================== -->
    <h3 class="mt-5 mb-2 text-deep-dark"><i class="bi bi-calendar-week me-2"></i>Manage Classes</h3>
    <div class="divider divider-primary mb-3"></div>

    <!-- Form to Add New Class -->
    <div class="glass-panel p-4 mb-4">
        <h4 class="mb-3"><i class="bi bi-plus-lg me-2"></i>Add a New Class</h4>
        <form action="classes_manage.php?action=create" method="POST">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Class Name</label>
                    <input type="text" name="class_name" class="form-control" placeholder="e.g. Muay Thai Basics" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Martial Art</label>
                    <input type="text" name="martial_art" class="form-control" placeholder="e.g. Muay Thai" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Day of Week</label>
                    <select name="day" class="form-select">
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                        <option>Saturday</option>
                        <option>Sunday</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-1 mb-3">
                    <label style="display: block; margin-bottom: 0.5rem;">End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_kids_class" class="form-check-input" id="is_kids_class">
                        <label class="form-check-label" for="is_kids_class">Kids Class</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Class</button>
        </form>
    </div>

    <!-- List of Classes -->
    <div class="glass-panel p-3">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th>Class Name</th>
                <th>Martial Art</th>
                <th>Day</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt_classes = $conn->prepare("SELECT id, class_name, martial_art, day_of_week, start_time, end_time FROM classes ORDER BY day_of_week, start_time");
            $stmt_classes->execute();
            $result_classes = $stmt_classes->get_result();

            if ($result_classes->num_rows > 0) {
                while($row = $result_classes->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['class_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['martial_art'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['day_of_week'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['start_time'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($row['end_time'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>";
                    echo "<a href='classes_manage.php?action=edit&id=" . intval($row['id']) . "' class='btn btn-warning btn-sm me-2'>Edit</a>";
                    echo "<a href='classes_manage.php?action=delete&id=" . intval($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\\\"Are you sure?\\\")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No classes scheduled yet.</td></tr>";
            }            $stmt_classes->close();            ?>
        </tbody>
    </table>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 3: MANAGE PRICES                   -->
    <!-- ========================================== -->
    <h3 class="mt-5 mb-2 text-deep-dark"><i class="bi bi-currency-dollar me-2"></i>Manage Membership Prices</h3>
    <div class="divider divider-primary mb-3"></div>

    <div class="glass-panel p-3">
    <table class="table table-bordered mb-0">
        <thead class="table-dark">
            <tr>
                <th>Membership Type</th>
                <th>Price</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt_prices = $conn->prepare("SELECT id, type, price, description FROM memberships");
            $stmt_prices->execute();
            $result_prices = $stmt_prices->get_result();

            if ($result_prices->num_rows > 0) {
                while($row = $result_prices->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') . "</strong></td>";
                    echo "<td>$" . number_format($row['price'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>";
                    echo '<a href="edit_price.php?id=' . intval($row['id']) . '" class="btn btn-warning btn-sm">Edit</a>';
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    </div>

</div> <!-- End Container -->

<?php include 'includes/footer.php'; ?>
