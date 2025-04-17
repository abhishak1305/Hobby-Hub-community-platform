<?php

/**
 * Sanitizes input data to prevent XSS and other attacks
 * @param mixed $input The input to sanitize
 * @return mixed Sanitized input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validates an email address
 * @param string $email The email to validate
 * @return bool True if valid, false otherwise
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generates a secure random token
 * @param int $length The length of the token in bytes (default: 32)
 * @return string The generated token
 * @throws Exception If random_bytes fails
 */
function generateToken($length = 32) {
    try {
        return bin2hex(random_bytes($length));
    } catch (Exception $e) {
        error_log("Error generating token: " . $e->getMessage());
        throw new Exception("Failed to generate secure token");
    }
}

/**
 * Displays flash messages stored in session
 * @return string HTML formatted flash message
 */
function displayFlashMessage() {
    if (!isset($_SESSION['flash'])) {
        return '';
    }

    $messages = is_array($_SESSION['flash']) ? $_SESSION['flash'] : [$_SESSION['flash']];
    unset($_SESSION['flash']);
    
    $output = '';
    foreach ($messages as $flash) {
        $message = htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8');
        $type = $flash['type'] ?? 'info';
        
        $bgColor = match ($type) {
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'success' => 'bg-green-100 border-green-400 text-green-700',
            default => 'bg-blue-100 border-blue-400 text-blue-700',
        };
        
        $output .= "<div class='border px-4 py-3 rounded relative mb-4 {$bgColor}' role='alert'>
                        <span class='block sm:inline'>{$message}</span>
                    </div>";
    }
    
    return $output;
}

/**
 * Formats a date string
 * @param string $date The date to format
 * @return string Formatted date or empty string on error
 */
function formatDate($date) {
    try {
        $dateTime = new DateTime($date);
        return $dateTime->format('F j, Y');
    } catch (Exception $e) {
        error_log("Error formatting date {$date}: " . $e->getMessage());
        return '';
    }
}

/**
 * Formats a time string
 * @param string $time The time to format
 * @return string Formatted time or empty string on error
 */
function formatTime($time) {
    try {
        $dateTime = new DateTime($time);
        return $dateTime->format('g:i A');
    } catch (Exception $e) {
        error_log("Error formatting time {$time}: " . $e->getMessage());
        return '';
    }
}

/**
 * Retrieves user data by ID
 * @param PDO $pdo Database connection
 * @param int $userId User ID to fetch
 * @return array|null User data or null on error/not found
 */
function getUserById($pdo, $userId) {
    if (!is_numeric($userId) || $userId <= 0) {
        error_log("Invalid user ID: {$userId}");
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    } catch (PDOException $e) {
        error_log("Error fetching user ID {$userId}: " . $e->getMessage());
        return null;
    }
}

/**
 * Checks if a user is a group admin
 * @param PDO $pdo Database connection
 * @param int $userId User ID to check
 * @param int $groupId Group ID to check
 * @return bool True if user is admin, false otherwise
 */
function isGroupAdmin($pdo, $userId, $groupId) {
    if (!is_numeric($userId) || !is_numeric($groupId) || $userId <= 0 || $groupId <= 0) {
        error_log("Invalid user ID {$userId} or group ID {$groupId}");
        return false;
    }

    try {
        $stmt = $pdo->prepare("SELECT role FROM group_members WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$userId, $groupId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['role']) && $result['role'] === 'admin';
    } catch (PDOException $e) {
        error_log("Error checking group admin status for user {$userId}, group {$groupId}: " . $e->getMessage());
        return false;
    }
}

?>