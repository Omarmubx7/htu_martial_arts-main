<?php
/**
 * includes/membership_rules.php
 * COMPLETE & FIXED business logic for all Assignment Brief plans.
 */

/**
 * HELPER: Removes spaces/hyphens for loose matching.
 * Example: "Muay-Thai" matches "Muay Thai" -> both become "muaythai"
 */
function cleanArtName($text) {
    return strtolower(preg_replace('/[^a-zA-Z]/', '', (string)$text));
}

function normalizeMembershipType($raw) {
    $raw = strtolower(trim((string)$raw));

    // Use strpos for broader PHP compatibility (instead of str_contains).
    if (strpos($raw, 'junior') !== false) return 'junior';
    if (strpos($raw, 'elite') !== false) return 'elite';
    if (strpos($raw, 'advanced') !== false) return 'advanced';
    if (strpos($raw, 'intermediate') !== false) return 'intermediate';
    if (strpos($raw, 'basic') !== false) return 'basic';
    if (strpos($raw, 'self-defence') !== false || strpos($raw, 'defense') !== false) return 'self-defence';
    if (strpos($raw, 'private') !== false) return 'private';
    if (strpos($raw, 'fitness') !== false) return 'fitness';
    return 'unknown';
}

/**
 * Main Logic Function
 */
function canUserBookClass($user_id, $class_martial_art, $is_kids_class = false, $class_name = '') {
    global $conn;

    $user_id = intval($user_id);
    if ($user_id <= 0) return ['can_book' => false, 'reason' => 'Invalid user.'];

    // 1. Fetch User Data (Includes 'chosen_martial_art_2' for Advanced)
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(u.membership_type_id, u.membership_id) AS membership_fk,
            u.chosen_martial_art,
            u.chosen_martial_art_2, 
            COALESCE(u.sessions_used_this_week, 0) AS sessions_used_this_week,
            m.type AS membership_type,
            m.sessions_per_week,
            u.created_at
        FROM users u
        LEFT JOIN memberships m ON m.id = COALESCE(u.membership_type_id, u.membership_id)
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !$user['membership_type']) {
        return ['can_book' => false, 'reason' => 'No active membership found.'];
    }

    // 2. Normalize Data
    $plan           = normalizeMembershipType($user['membership_type']);
    $sessions_used  = intval($user['sessions_used_this_week']);
    
    // Use cleanArtName for robust matching
    $class_art_clean = cleanArtName($class_martial_art);
    $user_art_1      = cleanArtName($user['chosen_martial_art']);
    $user_art_2      = cleanArtName($user['chosen_martial_art_2'] ?? '');
    $normalized_class_name = strtolower(trim((string)$class_name));
    $is_general_access_class = $normalized_class_name !== '' && (
        strpos($normalized_class_name, 'open mat') !== false ||
        strpos($normalized_class_name, 'personal training') !== false
    );

    // 3. Switch Logic per Plan
    switch ($plan) {

        // =========================================================
        // TIER 1: Basic (2 sessions) & Intermediate (3 sessions)
        // =========================================================
        case 'basic':
        case 'intermediate':
            // Rule A: Adults Only
            if ($is_kids_class) {
                return ['can_book' => false, 'reason' => 'This plan is for Adult classes only.'];
            }
            if (!$is_general_access_class) {
                // Rule B: Must match their ONE chosen art
                if ($user_art_1 === '') {
                    return ['can_book' => false, 'reason' => 'Please select your preferred martial art in your profile.'];
                }
                if ($class_art_clean !== $user_art_1) {
                    return ['can_book' => false, 'reason' => "Your plan is restricted to " . ucfirst($user['chosen_martial_art']) . " classes only."];
                }
            }

            // Rule C: Weekly Limits
            $limit = ($plan === 'basic') ? 2 : 3;
            if ($sessions_used >= $limit) {
                return ['can_book' => false, 'reason' => "Weekly limit reached ($limit sessions). Upgrade for more!"];
            }
            break;

        // =========================================================
        // TIER 2: Advanced (5 sessions, 2 Arts)
        // =========================================================
        case 'advanced':
            // Rule A: Adults Only
            if ($is_kids_class) {
                return ['can_book' => false, 'reason' => 'Advanced plan is for Adult classes only.'];
            }

            if (!$is_general_access_class) {
                // Rule B: Must match EITHER chosen art
                if ($user_art_1 === '' && $user_art_2 === '') {
                    return ['can_book' => false, 'reason' => 'Please select your 2 preferred martial arts in your profile.'];
                }
                
                $match1 = ($user_art_1 !== '' && $class_art_clean === $user_art_1);
                $match2 = ($user_art_2 !== '' && $class_art_clean === $user_art_2);

                if (!$match1 && !$match2) {
                    return ['can_book' => false, 'reason' => "You can only book classes for your 2 chosen arts."];
                }
            }

            // Rule C: Limit 5 sessions
            if ($sessions_used >= 5) {
                return ['can_book' => false, 'reason' => "Weekly limit reached (5 sessions)."];
            }
            break;

        // =========================================================
        // TIER 3: Elite (Unlimited)
        // =========================================================
        case 'elite':
            // Rule A: Adults Only
            if ($is_kids_class) {
                return ['can_book' => false, 'reason' => 'Elite membership is for Adult classes only.'];
            }
            
            // Rule B: No Private Tuition (Additional Cost)
              if (strpos($class_art_clean, 'private') !== false) {
                 return ['can_book' => false, 'reason' => 'Private tuition is not included in Elite membership.'];
            }
            
            // Rule C: Unlimited Sessions (No check needed)
            break;

        // =========================================================
        // TIER 4: Junior (Unlimited Kids)
        // =========================================================
        case 'junior':
            // Rule A: Kids Classes ONLY
            if (!$is_kids_class) {
                return ['can_book' => false, 'reason' => 'Junior membership is for Kids classes only.'];
            }
            // Rule B: Unlimited Sessions (No check needed)
            break;

        // =========================================================
        // TIER 5: Self-Defence Course
        // =========================================================
        case 'self-defence':
            // Rule A: Only Self-Defence Classes
            if (strpos($class_art_clean, 'defence') === false) {
                return ['can_book' => false, 'reason' => 'This account is for the Self-Defence course only.'];
            }

            // Rule B: 2 Sessions/Week Limit
            if ($sessions_used >= 2) {
                return ['can_book' => false, 'reason' => "Course limit reached (2 sessions/week)."];
            }

            // Rule C: 6-Week Expiration
            $start = new DateTime($user['created_at']);
            $expiry = (clone $start)->modify('+6 weeks');
            if (new DateTime() > $expiry) {
                return ['can_book' => false, 'reason' => 'Your 6-week Self-Defence course has expired.'];
            }
            break;

        // =========================================================
        // TIER 6: Private Tuition & Fitness
        // =========================================================
        case 'private':
            if (strpos($class_art_clean, 'private') === false) {
                return ['can_book' => false, 'reason' => 'This account is for Private Tuition bookings only.'];
            }
            break;

        case 'fitness':
            return ['can_book' => false, 'reason' => 'Fitness memberships cannot book martial arts classes.'];

        default:
            return ['can_book' => false, 'reason' => 'Membership type not recognized. Contact support.'];
    }

    return ['can_book' => true, 'reason' => ''];
}
?>
