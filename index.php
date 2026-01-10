<?php
require_once 'includes/init.php';
$pageTitle = "Home";
include 'includes/header.php';
?>

<!-- ============================================== -->
<!-- HERO SECTION - Main landing page banner -->
<!-- ============================================== -->
<!-- style="background: linear-gradient" creates the blue gradient background -->
<!-- style="min-height: 90vh" makes section take up most of viewport -->
<!-- style="display: flex; align-items: center" vertically centers the content -->
<section class="hero-sports text-white">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-7 mx-auto hero-content text-white">
                <!-- Small badge row - eye-catching labels -->
                <!-- style="padding: 10px 16px; font-size: 0.9rem" makes them small and compact -->
                <div class="d-inline-flex gap-2 mb-3 flex-wrap justify-content-center">
                    <span class="badge badge-primary"><i class="bi bi-shield-check me-1"></i>Black-belt coaches</span>
                    <span class="badge badge-primary"><i class="bi bi-people me-1"></i>Community first</span>
                </div>
                
                <!-- Main heading -->
                <!-- style="line-height: 1.2" tightens line spacing for dramatic effect -->
                <!-- style="letter-spacing: -0.5px" makes letters closer for bolder look -->
                <h1 class="fw-bold mb-4">Train Hard. Move Smart. Fight Confident.</h1>
                
                <!-- Subheading paragraph -->
                <!-- style="color: #e0e0e0" light gray color for supporting text -->
                <!-- style="max-width: 600px" prevents text from getting too wide on large screens -->
                <p class="lead mb-5 mx-auto">Master Jiu-jitsu, Karate, and Muay Thai with structured programs, expert guidance, and flexible schedules.</p>
                
                <!-- Call-to-action buttons -->
                <!-- Two main buttons: "See Memberships" (red) and "View Timetable" (outlined) -->
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <!-- Primary button with red background and shadow for emphasis -->
                    <!-- style="box-shadow: 0 8px 24px rgba(220,20,60,0.4)" adds red glow underneath -->
                    <!-- style="transition: all 0.3s ease" smooths hover animations -->
                    <a href="#plans" class="btn btn-primary fw-bold"><i class="bi bi-fire me-2"></i>See Memberships</a>
                    <!-- Secondary button with white outline -->
                    <!-- style="border-width: 2px" thicker border for visibility -->
                    <a href="classes_premium.php" class="btn btn-outline fw-bold"><i class="bi bi-calendar-week me-2"></i>View Timetable</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- FEATURES SECTION - Why choose us -->
<!-- ============================================== -->
<!-- style="padding: 80px 0" adds vertical spacing, "background: #f8f9fa" light gray background -->
<section class="features-section page-section bg-light">
    <div class="container">
        <!-- Section heading -->
        <!-- style="font-size: 2.5rem; color: #1a1a2e" makes it big and dark -->
        <h2 class="text-center mb-5 fw-bold text-deep-dark">Why Choose HTU Martial Arts?</h2>
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card feature-card text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-person-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-deep-dark">Expert Instructors</h5>
                    <p class="text-muted">Learn from certified and experienced instructors with years of martial arts expertise.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card feature-card text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-calendar-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-deep-dark">Flexible Schedule</h5>
                    <p class="text-muted">Classes available throughout the week at times that fit your lifestyle.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card feature-card text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-deep-dark">Modern Facilities</h5>
                    <p class="text-muted">Train in our state-of-the-art facility equipped with all necessary equipment.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.feature-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 12px 32px rgba(220,20,60,0.15) !important;
}
</style>

<!-- ============================================== -->
<!-- FACILITIES SECTION -->
<!-- ============================================== -->
<section class="facilities-section page-section">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-deep-dark">World-Class Facilities</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-moisture text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-deep-dark">Sauna & Recovery</h5>
                    <p class="text-muted">Full-size sauna facilities for post-training recovery and muscle relaxation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-heart-pulse text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-deep-dark">Strength & Conditioning Gym</h5>
                    <p class="text-muted">Fully-equipped weight room with cardio machines, free weights, and functional training gear.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-grid-3x3 text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-deep-dark">Premium Training Mats</h5>
                    <p class="text-muted">Professional-grade mats for BJJ, Judo, and grappling with shock absorption and hygiene standards.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- OUR INSTRUCTORS SECTION -->
<!-- ============================================== -->
<!-- kinda over-styled but it looks cool -->
<section class="instructors-section page-section bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-deep-dark">Meet Our Instructors</h2>
        <div class="row g-4">
            <?php
            $stmt = $conn->prepare("SELECT id, name, specialty, bio FROM instructors");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 col-lg-4">';
                    $instructorName = trim($row["name"]);
                    $imageExtensions = ['png', 'jpg', 'jpeg', 'webp'];
                    $imgSrc = '';
                    
                    // Check for image file with exact instructor name (with spaces)
                    foreach ($imageExtensions as $ext) {
                        $filePath = "images/" . $instructorName . "." . $ext;
                        if (file_exists($filePath)) {
                            $imgSrc = $filePath;
                            break;
                        }
                    }
                    
                    // Fallback: try lowercase with hyphens
                    if (empty($imgSrc)) {
                        $nameWithHyphens = strtolower(str_replace(' ', '-', $instructorName));
                        foreach ($imageExtensions as $ext) {
                            $filePath = "images/" . $nameWithHyphens . "." . $ext;
                            if (file_exists($filePath)) {
                                $imgSrc = $filePath;
                                break;
                            }
                        }
                    }
                    
                    // Fallback to database image_url if set
                    if (empty($imgSrc) && !empty($row["image_url"])) {
                        $imgSrc = htmlspecialchars($row["image_url"]);
                    }
                    
                    echo '  <div class="card h-100" style="overflow: hidden;">';
                    echo '    <div style="height: auto; aspect-ratio: 3/4; overflow: hidden;">';
                    if (!empty($imgSrc)) {
                        echo '      <img src="' . $imgSrc . '" loading="lazy" alt="' . htmlspecialchars($row["name"]) . '" style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.3s;">';
                    } else {
                        echo '      <div style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;"><p class="text-muted">No image</p></div>';
                    }
                    echo '    </div>';
                    echo '    <div class="card-body p-4">';
                    echo '      <h5 class="card-title fw-bold text-deep-dark">' . htmlspecialchars($row["name"]) . '</h5>';
                    echo '      <p class="mb-3 text-primary fw-600">' . htmlspecialchars($row["specialty"]) . '</p>';
                    echo '      <p class="card-text text-muted">' . htmlspecialchars($row["bio"]) . '</p>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-muted">No instructors available yet.</p></div>';
            }
            $stmt->close();
            ?>
        </div>
    </div>
</section>



<!-- ============================================== -->
<!-- MEMBERSHIPS SECTION (ID="plans") -->
<!-- ============================================== -->
<section id="plans" class="memberships-section page-section">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-deep-dark">Choose Your Plan</h2>
        <div class="row g-4 justify-content-center">
            <?php
            $stmt_plans = $conn->prepare("SELECT id, type, price, description FROM memberships");
            $stmt_plans->execute();
            $result = $stmt_plans->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 col-lg-3">';
                    echo '  <div class="card h-100 d-flex flex-column" style="overflow: hidden;">';
                    echo '    <div class="card-header text-center">';
                    echo '      <h5 class="mb-0 fw-bold plan-name">' . htmlspecialchars($row['type']) . '</h5>';
                    echo '    </div>';
                    echo '    <div class="card-body d-flex flex-column p-4">';
                    echo '      <div class="mb-4">';
                    echo '        <span class="display-5 fw-bold text-primary">$' . number_format($row['price'], 0) . '</span>';
                    echo '        <span class="text-muted">/month</span>';
                    echo '      </div>';
                    echo '      <p class="text-muted" style="flex-grow: 1;">' . htmlspecialchars($row['description']) . '</p>';
                    echo '      <a href="signup.php?plan_id=' . intval($row['id']) . '" class="btn btn-primary mt-4 fw-bold">Choose Plan</a>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-muted">No membership plans available yet.</p></div>';
            }
            $stmt_plans->close();
            ?>
        </div>
    </div>
</section>



<!-- ============================================== -->
<!-- CTA SECTION -->
<!-- ============================================== -->
<section class="cta-section bg-primary page-section text-center text-white">
    <div class="container">
        <h2 class="mb-4 fw-bold">Ready to Start Your Martial Arts Journey?</h2>
        <p class="lead mb-5">Join hundreds of students already training with us.</p>
        <a href="signup.php" class="btn btn-light fw-bold">Sign Up Now</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
