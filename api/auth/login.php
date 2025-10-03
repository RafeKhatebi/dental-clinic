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
        errorResponse(__('login_failed'));
    }

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
