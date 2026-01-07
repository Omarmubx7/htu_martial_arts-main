<?php
// SECURITY: Start session at the very top for secure session handling
session_start();
include 'includes/db.php';
include 'includes/membership_rules.php';  // Include functions that check if user can book each class
$pageTitle = "Class Timetable";
include 'includes/header.php';
?>


<!-- Hero Banner - full width section with background image -->
<section class="hero-sports">
    <div class="hero-content">
        <h1>Your Week.<br>Your Rules.</h1>
        <p>Train when you want. Choose what fits your life.</p>
        <!-- Smooth scroll button to jump to schedule section -->
        <button class="hero-cta" onclick="document.querySelector('.schedule-section').scrollIntoView({behavior: 'smooth'})">
            View Schedule
        </button>
    </div>
</section>


<!-- Schedule Section - main classes display -->
<section class="schedule-section">
    <div class="container">
        <h2 class="text-center" style="margin-bottom: 50px;">Weekly Timetable</h2>
        <?php if (!empty($_SESSION['booking_error'])): ?>
            <div class="alert alert-danger text-center mt-3">
                <?php echo htmlspecialchars($_SESSION['booking_error']); ?>
            </div>
            <?php unset($_SESSION['booking_error']); ?>
        <?php endif; ?>
        
        <!-- Filter Buttons - let users filter by martial art or kids classes -->
        <!-- These buttons have JavaScript event listeners to filter the schedule dynamically -->
        <!-- TODO: maybe fetch these from DB later, manual for now -->
        <div class="schedule-filters">
            <button class="filter-btn active" data-filter="all">All Classes</button>
            <button class="filter-btn" data-filter="jiu-jitsu">Jiu-jitsu</button>
            <button class="filter-btn" data-filter="karate">Karate</button>
            <button class="filter-btn" data-filter="judo">Judo</button>
            <button class="filter-btn" data-filter="muay-thai">Muay Thai</button>
            <button class="filter-btn" data-filter="kids">Kids Classes</button>
        </div>
        
        <!-- Class Grid - displays all classes fetched from database -->
        <div class="schedule-grid">
            <?php
            // SECURITY: Use prepared statement even without parameters for consistency against injection
            $sql = "SELECT id, class_name, day_of_week, start_time, end_time, martial_art, age_group, is_kids_class 
                FROM classes 
                ORDER BY FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), start_time";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0):
            while($class = $result->fetch_assoc()):
                // Check access if user is logged in
                $access_check = ['can_book' => false, 'reason' => 'Login to book'];
                
                if (isset($_SESSION['user_id'])) {
                    // SECURITY: Passing validated fields to access checker
                    $access_check = canUserBookClass(
                        $_SESSION['user_id'], 
                        $class['martial_art'],
                        ($class['is_kids_class'] == 1),
                        $class['class_name']
                    );
                }
                
                $locked_class = $access_check['can_book'] ? '' : 'locked';
                
                // Filter logic for the frontend buttons
                $class_filter = strtolower(str_replace(' ', '-', $class['martial_art']));
                
                // If it's a kids class, ensure it shows up under "Kids" filter
                if ($class['is_kids_class'] == 1) {
                    $class_filter = 'kids'; 
                }
            ?>
                <!-- FIX 4: Data attribute uses database flag -->
                <div class="class-slot <?php echo htmlspecialchars($locked_class, ENT_QUOTES, 'UTF-8'); ?>" 
                     data-art="<?php echo htmlspecialchars($class_filter, ENT_QUOTES, 'UTF-8'); ?>"
                     data-kids="<?php echo ($class['is_kids_class'] == 1) ? 'kids' : 'adult'; ?>"
                     style="background: rgba(255, 255, 255, 0.95); color: #1a1a2e; border: 1px solid rgba(220, 20, 60, 0.2);">
                    
                    <div class="class-day" style="color: var(--color-primary);"><?php echo htmlspecialchars($class['day_of_week'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <h4 style="color: #1a1a2e;"><?php echo htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="class-time" style="color: #666;">
                        <?php echo htmlspecialchars(date('g:i A', strtotime($class['start_time'])) . ' - ' . date('g:i A', strtotime($class['end_time'])), ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($access_check['can_book']): ?>
                            <button class="book-btn" onclick="validateBooking(<?php echo (int)$class['id']; ?>, '<?php echo htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8'); ?>', event)">
                                📅 Book Now
                            </button>
                        <?php else: ?>
                            <button class="book-btn" disabled title="<?php echo htmlspecialchars($access_check['reason'], ENT_QUOTES, 'UTF-8'); ?>">
                                🔒 <?php echo strlen($access_check['reason']) > 25 ? 'Restricted' : htmlspecialchars($access_check['reason'], ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" class="book-btn">Login to Book</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; else: ?>
                <p class="text-center text-muted" style="grid-column: 1 / -1;">No classes scheduled yet. Please check back soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>


<!-- CTA Banner -->
<section class="cta-banner">
    <h2>Ready to Level Up?</h2>
    <p>Upgrade to Elite for unlimited access to all classes.</p>
    <a href="prices.php" class="btn">View Plans</a>
</section>


<?php include 'includes/footer.php'; ?>
