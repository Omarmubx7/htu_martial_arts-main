<?php
/**
 * includes/membership_rules.php
 * Business logic for membership-based access + booking recording
 * Fully aligned with database IDs from attached screenshot
 */

function normalizeMembershipType(?string $raw): string {
    $raw = strtolower(trim((string)$raw));

    // Match exact strings from your database image
    if (str_contains($raw, 'junior')) return 'junior';
    if (str_contains($raw, 'elite')) return 'elite';
    if (str_contains($raw, 'advanced')) return 'advanced';
    if (str_contains($raw, 'intermediate')) return 'intermediate';
    if (str_contains($raw, 'basic')) return 'basic';
    if (str_contains($raw, 'self-defence')) return 'self-defence'; // ID 7
    if (str_contains($raw, 'private')) return 'private'; // ID 6
    if (str_contains($raw, 'fitness')) return 'fitness'; // ID 8 & 9

    return 'unknown';
}

function normalizeText(?string $value): string {
    return strtolower(trim((string)$value));
}

/**
 * Check if user can book a class.
 */
function canUserBookClass($user_id, $class_martial_art, $is_kids_class = false) {
    global $conn;

    $user_id = intval($user_id);
    if ($user_id <= 0) {
        return ['can_book' => false, 'reason' => 'Invalid user.'];
    }

    // Get user membership info
    $stmt = $conn->prepare("
        SELECT
            COALESCE(u.membership_type_id, u.membership_id) AS membership_fk,
            u.chosen_martial_art,
            COALESCE(u.sessions_used_this_week, 0) AS sessions_used_this_week,
            m.type AS membership_type,
            m.sessions_per_week
        FROM users u
        LEFT JOIN memberships m
            ON m.id = COALESCE(u.membership_type_id, u.membership_id)
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !$user['membership_type']) {
        return ['can_book' => false, 'reason' => 'No active membership. Please select a plan.'];
    }

    $membership_type = normalizeMembershipType($user['membership_type']);
    $sessions_used   = intval($user['sessions_used_this_week']);
    
    $class_art  = normalizeText($class_martial_art); // e.g., "judo", "self-defence"
    $chosen_art = normalizeText($user['chosen_martial_art']);

    // --- LOGIC SWITCHER BASED ON PLAN ---

    switch ($membership_type) {
        // --- TIER 1: Standard Plans (Basic, Intermediate, Advanced) ---
        case 'basic':
        case 'intermediate':
        case 'advanced':
            // Rule A: Must have chosen a martial art
            if ($chosen_art === '') {
                return ['can_book' => false, 'reason' => 'Please select your preferred martial art in your profile.'];
            }
            
            // Rule B: Can ONLY book their chosen art
            if ($class_art !== $chosen_art) {
                return [
                    'can_book' => false,
                    'reason'   => 'Your plan is restricted to ' . $user['chosen_martial_art'] . ' classes only.'
                ];
            }

            // Rule C: Weekly Session Limits
            // We use the database value first (2, 3, 5), fallback to defaults if NULL
            $limit = $user['sessions_per_week'];
            if ($limit === null) {
                $limit = match ($membership_type) {
                    'basic' => 2,
                    'intermediate' => 3,
                    'advanced' => 5,
                    default => 0
                };
            } else {
                $limit = intval($limit);
            }

            if ($limit > 0 && $sessions_used >= $limit) {
                return [
                    'can_book' => false,
                    'reason'   => "Weekly limit reached ({$limit} sessions)."
                ];
            }
            break;

        // --- TIER 2: Beginners' Self-Defence (ID 7) ---
        case 'self-defence':
            // Rule A: Can ONLY book "Self-Defence" classes
            // We check if the class martial art contains "defence"
            if (!str_contains($class_art, 'defence')) {
                 return [
                    'can_book' => false, 
                    'reason'   => 'This membership is for the Self-Defence course only.'
                ];
            }

            // Rule B: Limit is hardcoded to 2 per week (as per your prompt)
            if ($sessions_used >= 2) {
                return [
                    'can_book' => false, 
                    'reason'   => "Course limit reached (2 sessions/week)."
                ];
            }

            // Rule C: 6-week access window
            if (!isSelfDefenceWindowOpen($user_id)) {
                return ['can_book' => false, 'reason' => 'Self-Defence course access has expired (6-week limit).'];
            }
            break;

        // --- TIER 3: Elite (ID 4) - Unlimited Adult ---
        case 'elite':
            if ($is_kids_class) {
                return ['can_book' => false, 'reason' => 'Elite membership is for adult classes only.'];
            }
            break;

        // --- TIER 4: Junior (ID 5) - Unlimited Kids ---
        case 'junior':
            if (!$is_kids_class) {
                return ['can_book' => false, 'reason' => 'Junior membership is for kids classes only.'];
            }
            break;
            
        // --- TIER 5: Private Tuition (ID 6) ---
        case 'private':
             if (!str_contains($class_art, 'private')) {
                 return ['can_book' => false, 'reason' => 'This account is for Private Tuition bookings only.'];
             }
             break;

        // --- TIER 6: Fitness Room (ID 8 & 9) ---
        case 'fitness':
             return ['can_book' => false, 'reason' => 'Fitness memberships cannot book martial arts classes.'];

        default:
            return ['can_book' => false, 'reason' => 'Membership type not recognized. Contact support.'];
    }

    return ['can_book' => true, 'reason' => ''];
}

/**
 * Record booking and update session counters (transactional).
 */
function recordBooking($user_id, $class_id) {
    global $conn;

    $user_id  = intval($user_id);
    $class_id = intval($class_id);

    if ($user_id <= 0 || $class_id <= 0) return false;

    $conn->begin_transaction();

    try {
        // Prevent duplicate confirmed booking
        $stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND class_id = ? AND status = 'confirmed' LIMIT 1");
        $stmt->bind_param("ii", $user_id, $class_id);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $conn->rollback();
            return false;
        }

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, class_id, booking_date) VALUES (?, ?, CURDATE())");
        $stmt->bind_param("ii", $user_id, $class_id);
        $stmt->execute();

        // Update user session count
        $stmt = $conn->prepare("UPDATE users SET sessions_used_this_week = COALESCE(sessions_used_this_week, 0) + 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $conn->commit();
        return true;

    } catch (Throwable $e) {
        $conn->rollback();
        return false;
    }
}

function resetWeeklySessions() {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET sessions_used_this_week = 0");
    $stmt->execute();
    $stmt->close();
    return true;
}

function isSelfDefenceWindowOpen(int $user_id): bool {
    global $conn;
    $stmt = $conn->prepare("SELECT created_at FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $created = $stmt->get_result()->fetch_assoc()['created_at'] ?? null;
    if (!$created) return false;
    $start = new DateTime($created);
    $end   = (clone $start)->modify('+6 weeks');
    return new DateTime() <= $end;
}
?>
