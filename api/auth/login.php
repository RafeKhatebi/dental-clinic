<?php
require_once dirname(__DIR__, 2) . '/config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    errorResponse(__('required_fields'));
}

// Check login attempts
$loginCheck = checkLoginAttempts($username);
if (!$loginCheck['allowed']) {
    errorResponse($loginCheck['message'], 429);
}

try {
    $user = fetchOne(
        "SELECT * FROM users WHERE username = ? AND is_active = 1",
        [$username]
    );

    if (!$user) {
        errorResponse(__('login_failed'));
    }

    // Verify password
    if (!verifyPassword($password, $user['password'])) {
        recordFailedLogin($username);
        errorResponse(__('login_failed'));
    }
    
    // Reset login attempts on success
    resetLoginAttempts($username);

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];

    // Log activity
    logActivity('login', 'users', $user['id'], 'User logged in');

    successResponse(__('login_success'), [
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ]
    ]);

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    errorResponse(__('error_occurred'));
}
