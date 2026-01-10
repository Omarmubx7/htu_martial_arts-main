<?php
/**
 * edit_price.php
 * Admin-only page for editing membership plan pricing and descriptions
 * Allows admins to update the price and details of existing membership tiers
 */

require_once 'includes/init.php';
requireAdmin();
include 'includes/header.php';

// ====================================================================
// STEP 1: Fetch current membership price data from database
// ====================================================================
if (isset($_GET['id'])) {
    // Get membership ID from URL parameter and sanitize it (convert to integer)
    $id = intval($_GET['id']); 
    
    // Prepare SELECT query to fetch the membership record
    // Using prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM memberships WHERE id = ?");
    // Bind the ID variable: i=integer
    $stmt->bind_param("i", $id);
    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    // Fetch the membership data as an associative array
    $price = $result->fetch_assoc();
    
    // If membership not found in database, redirect to admin page
    if (!$price) {
        header("Location: admin.php");
        exit();
    }
} else {
    // If no ID was provided in URL, redirect to admin page
    header("Location: admin.php");
    exit();
}

// ====================================================================
// STEP 2: Process form submission when admin updates price
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the membership ID from hidden form field
    $id = $_POST['id'];
    // Get the new price value from form input
    $amount = $_POST['price'];
    // Get the description from form textarea
    $desc = $_POST['description'];

    // Prepare UPDATE query to modify the membership record
    // Using prepared statement prevents SQL injection
    $stmt = $conn->prepare("UPDATE memberships SET price=?, description=? WHERE id=?");
    // Bind the variables: 
    // d=decimal (for price), s=string (for description), i=integer (for ID)
    $stmt->bind_param("dsi", $amount, $desc, $id);

    // Execute the UPDATE query
    if ($stmt->execute()) {
        // If update successful, show success message and redirect to admin
        echo "<script>alert('Price Updated!'); window.location.href='admin.php';</script>";
    }
}
?>

<div class="container page-section narrow-container">
    <!-- Back Button - allows admin to return to admin dashboard -->
    <div class="mb-3">
        <a href="admin.php" class="btn btn-light btn-sm no-underline"><i class="bi bi-arrow-left me-2"></i>Back to Dashboard</a>
    </div>
    
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h2 class="section-title mb-2"><i class="bi bi-pencil-square me-2"></i>Edit Membership</h2>
        <!-- Display which membership plan is being edited -->
        <p class="text-muted"><?php echo htmlspecialchars($price['type']); ?> Plan</p>
    </div>
    
    <!-- Form to update price and description -->
    <form method="POST" class="glass-panel p-4">
        <!-- Hidden field to pass membership ID to POST handler -->
        <input type="hidden" name="id" value="<?php echo intval($price['id']); ?>">
        
        <!-- Price input field - decimal number with 2 decimal places -->
        <div class="mb-4">
            <label class="form-label mb-2"><i class="bi bi-currency-dollar me-2"></i>Price ($)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($price['price']); ?>" required>
        </div>
        
        <!-- Description textarea - allows admin to update plan description -->
        <div class="mb-4">
            <label class="form-label mb-2"><i class="bi bi-card-text me-2"></i>Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($price['description']); ?></textarea>
        </div>
        
        <!-- Action buttons -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Update Price</button>
            <a href="admin.php" class="btn btn-light"><i class="bi bi-x-circle me-2"></i>Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
