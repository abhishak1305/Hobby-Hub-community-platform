<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to join groups', 'error');
}

// Check if group_id is provided
if (!isset($_POST['group_id'])) {
    redirect('groups.php', 'Invalid request', 'error');
}

$group_id = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
if ($group_id === false) {
    redirect('groups.php', 'Invalid group ID', 'error');
}

try {
    // Check if group exists
    $stmt = $pdo->prepare("SELECT group_id FROM `groups` WHERE group_id = ?");
    $stmt->execute([$group_id]);
    if (!$stmt->fetch()) {
        redirect('groups.php', 'Group not found', 'error');
    }

    // Check if user is already a member
    $stmt = $pdo->prepare("SELECT member_id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        redirect('groups.php', 'You are already a member of this group', 'error');
    }

    // Add user to group
    $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')");
    $stmt->execute([$group_id, $_SESSION['user_id']]);

    redirect('group_detail.php?id=' . $group_id, 'Successfully joined the group!', 'success');
} catch(PDOException $e) {
    error_log("Error joining group: " . $e->getMessage());
    redirect('groups.php', 'An error occurred while joining the group', 'error');
}
?>
