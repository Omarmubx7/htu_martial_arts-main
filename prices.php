<?php
require_once 'includes/init.php';

$pageTitle = "Prices";

$martialArts = getMartialArtsList();
$currentPlanNormalized = '';
$currentPrimaryArt = '';
$currentSecondaryArt = '';
$errorMessage = '';

if (isset($_SESSION['user_id'])) {
    $userInfo = getUserPlanInfo((int)$_SESSION['user_id']);
    $currentPlanNormalized = normalizeMembershipType($userInfo['membership_type'] ?? '');
    $currentPrimaryArt = $userInfo['chosen_martial_art'] ?? '';
    $currentSecondaryArt = $userInfo['chosen_martial_art_2'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $planId = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : null;
    if ($planId) {
        $planData = getMembershipPlanById((int)$planId) ?: [];

        $selectedPlanType = $planData['type'] ?? '';
        $selectedPlanNormalized = normalizeMembershipType($selectedPlanType);
        $requiresSecondArt = $selectedPlanNormalized === 'advanced' && in_array($currentPlanNormalized, ['basic', 'intermediate'], true);
        $primaryArtInput = trim($_POST['martial_art'] ?? '');
        $secondaryArtInput = trim($_POST['martial_art_secondary'] ?? '');

        if ($requiresSecondArt && $secondaryArtInput === '') {
            $errorMessage = 'Please select your second martial art before upgrading to the Advanced plan.';
        } else {
            $martialArtToSave = $primaryArtInput !== '' ? $primaryArtInput : $currentPrimaryArt;
            $martialArtSecondaryToSave = $secondaryArtInput !== '' ? $secondaryArtInput : $currentSecondaryArt;

            $updateStmt = $conn->prepare('UPDATE users SET membership_id = ?, chosen_martial_art = ?, chosen_martial_art_2 = ? WHERE id = ?');
            if ($updateStmt) {
                $updateStmt->bind_param('issi', $planId, $martialArtToSave, $martialArtSecondaryToSave, $_SESSION['user_id']);
                if ($updateStmt->execute()) {
                    addFlashToast('Membership plan updated successfully!', 'success');
                    redirectTo('dashboard.php');
                }
            }
            $errorMessage = 'Unable to update your membership. Please try again.';
        }
    }
}

if (!empty($errorMessage)) {
    addFlashToast($errorMessage, 'danger');
    $errorMessage = '';
}

include 'includes/header.php'; 
?>

<!-- Main container for pricing page -->
<!-- Standard padding: 80px top/bottom for consistency across all pages -->
<div class="container" style="padding: 80px 0;">
    <!-- Page header section -->
    <!-- Consistent h2 size and styling across all pages -->
    <div class="text-center mb-5">
        <h2 class="section-title" style="font-size: 2.5rem; color: #1a1a2e; margin-bottom: 1rem;">
            <i class="bi bi-star me-2" style="color: #DC143C;"></i>Membership Plans
        </h2>
        <!-- Subheading with standard color and sizing -->
        <p style="color: #666; font-size: 1.05rem; font-weight: 400;">Choose the plan that fits your training goals</p>
    </div>
    
    <!-- Responsive grid for membership cards -->
    <!-- Using Bootstrap grid system for mobile responsiveness -->
    <div class="row justify-content-center g-4">
        <?php
        $plans = getAllMembershipPlans();

        if (!empty($plans)) {
            foreach ($plans as $row) {
                $planNormalized = normalizeMembershipType($row['type']);
                $requiresSecondArtField = $planNormalized === 'advanced' && in_array($currentPlanNormalized, ['basic', 'intermediate'], true);
                // Create a membership card for each plan
                echo '<div class="col-md-4 col-lg-3">';
                // Card wrapper with consistent glass-panel styling
                // glass-panel: white background with subtle shadow
                echo '  <div class="card h-100 glass-panel" style="border: none; padding: 0; overflow: hidden; display: flex; flex-direction: column;">';
                
                // Card header with red gradient - consistent color scheme across site
                // Gradient: primary red to darker red for visual depth
                echo '    <div style="background: linear-gradient(135deg, #DC143C 0%, #a00000 100%); color: white; padding: 20px; text-align: center;">';
                // Plan name badge with semi-transparent white background
                echo '      <span style="display: inline-block; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; letter-spacing: 1px;">' . htmlspecialchars($row['type']) . '</span>';
                echo '    </div>';
                
                // Card body with consistent padding and flex layout
                echo '    <div style="padding: 30px; display: flex; flex-direction: column; flex-grow: 1;">';
                
                // Price section - big, bold, primary color
                echo '      <div class="my-3">';
                echo '        <h3 class="fw-bold" style="color: #DC143C; font-size: 2.5rem; margin: 0;">$' . number_format($row['price'], 0) . '</h3>';
                echo '        <small style="color: #999; font-size: 0.9rem;">per month</small>';
                echo '      </div>';
                
                // Plan description - stretches to fill available space
                echo '      <p style="color: #666; flex-grow: 1; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">' . htmlspecialchars($row['description']) . '</p>';
                
                // Button section - responsive form
                if (isset($_SESSION['user_id'])) {
                    // LOGGED IN: Show form to SELECT/UPDATE membership
                    // When submitted, POSTs to this page and updates user's membership
                    echo '      <form method="POST" style="width: 100%;">';
                    echo '        <input type="hidden" name="plan_id" value="' . intval($row['id']) . '">';
                    if ($requiresSecondArtField) {
                        echo '        <div class="mb-3">';
                        echo '          <label class="form-label mb-2 fw-600 text-deep-dark">Pick a second martial art</label>';
                        echo '          <select name="martial_art_secondary" class="form-select" required>';
                        echo '            <option value="">Select another martial art...</option>';
                        foreach ($martialArts as $art) {
                            $sanitized = htmlspecialchars($art, ENT_QUOTES, 'UTF-8');
                            echo '            <option value="' . $sanitized . '">' . $sanitized . '</option>';
                        }
                        echo '          </select>';
                        echo '          <small class="text-muted d-block mt-2">Advanced upgrades require two disciplines; please choose your additional art.</small>';
                        echo '        </div>';
                    }
                    echo '        <button type="submit" class="btn w-100 fw-bold" style="background: linear-gradient(135deg, #DC143C 0%, #a00000 100%); color: white; padding: 12px; border-radius: 8px; border: none; font-size: 0.9rem; transition: all 0.3s ease; box-shadow: 0 4px 16px rgba(220,20,60,0.3);">';
                    echo '          <i class="bi bi-check-circle me-2"></i>Select Plan';
                    echo '        </button>';
                    echo '      </form>';
                } else {
                    // NOT LOGGED IN: Link to signup with this plan pre-selected
                    echo '      <a href="signup.php?plan_id=' . intval($row['id']) . '" class="btn w-100 fw-bold" style="background: linear-gradient(135deg, #DC143C 0%, #a00000 100%); color: white; padding: 12px; border-radius: 8px; border: none; font-size: 0.9rem; transition: all 0.3s ease; box-shadow: 0 4px 16px rgba(220,20,60,0.3); text-decoration: none; display: block; text-align: center;">';
                    echo '        <i class="bi bi-check-circle me-2"></i>Choose Plan';
                    echo '      </a>';
                }
                
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>

<!-- Consistent hover effect styling for cards -->
<style>
    .card.glass-panel:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(220,20,60,0.2) !important;
    }
</style>
<?php include 'includes/footer.php'; ?>
