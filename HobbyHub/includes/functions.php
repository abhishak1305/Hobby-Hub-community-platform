<?php
// Input sanitization
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Flash message display
function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $message = $_SESSION['flash']['message'];
        $type = $_SESSION['flash']['type'];
        unset($_SESSION['flash']);
        
        $bgColor = $type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 
                   ($type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 
                   'bg-blue-100 border-blue-400 text-blue-700');
        
        return "<div class='border px-4 py-3 rounded relative mb-4 {$bgColor}' role='alert'>
                    <span class='block sm:inline'>{$message}</span>
                </div>";
    }
    return '';
}

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Format time
function formatTime($time) {
    return date('g:i A', strtotime($time));
}

// Get user by ID
function getUserById($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Error fetching user ID {$userId}: " . $e->getMessage());
        return false;
    }
}

// Check if user is group admin
function isGroupAdmin($pdo, $userId, $groupId) {
    try {
        $stmt = $pdo->prepare("SELECT role FROM group_members WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$userId, $groupId]);
        $result = $stmt->fetch();
        return $result && $result['role'] === 'admin';
    } catch(PDOException $e) {
        error_log("Error checking group admin status for user {$userId}, group {$groupId}: " . $e->getMessage());
        return false;
    }
}