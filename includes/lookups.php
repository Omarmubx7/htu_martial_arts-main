<?php
/**
 * includes/lookups.php
 * Shared lookup helpers for plans and user plan info.
 */

function getMartialArtsList()
{
    return ['Jiu-jitsu', 'Judo', 'Karate', 'Muay Thai'];
}

function getMembershipPlanById($planId)
{
    global $conn;

    $planId = intval($planId);
    if ($planId <= 0) {
        return null;
    }

    $stmt = $conn->prepare('SELECT id, type, price, description FROM memberships WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $planId);
    $stmt->execute();
    $plan = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    return $plan;
}

function getAllMembershipPlans()
{
    global $conn;

    $stmt = $conn->prepare('SELECT id, type, price, description FROM memberships');
    if (!$stmt) {
        return [];
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $plans = [];
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }

    $stmt->close();
    return $plans;
}

function getUserPlanInfo($userId)
{
    global $conn;

    $userId = intval($userId);
    if ($userId <= 0) {
        return [
            'membership_type' => '',
            'chosen_martial_art' => '',
            'chosen_martial_art_2' => '',
        ];
    }

    $stmt = $conn->prepare('SELECT u.chosen_martial_art, u.chosen_martial_art_2, m.type AS membership_type FROM users u LEFT JOIN memberships m ON u.membership_id = m.id WHERE u.id = ? LIMIT 1');
    if (!$stmt) {
        return [
            'membership_type' => '',
            'chosen_martial_art' => '',
            'chosen_martial_art_2' => '',
        ];
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();

    return [
        'membership_type' => $info['membership_type'] ?? '',
        'chosen_martial_art' => $info['chosen_martial_art'] ?? '',
        'chosen_martial_art_2' => $info['chosen_martial_art_2'] ?? '',
    ];
}
