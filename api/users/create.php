<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole('admin')) {
    errorResponse('Unauthorized', 401);
}

$username = sanitizeInput($_POST['username'] ?? '');
$fullName = sanitizeInput($_POST['full_name'] ?? '');
$password = $_POST['password'] ?? '';
$role = sanitizeInput($_POST['role'] ?? '');

if (empty($username) || empty($fullName) || empty($password) || empty($role)) {
    errorResponse(__('required_fields'));
}

try {
    if (fetchOne("SELECT id FROM users WHERE username = ?", [$username])) {
        errorResponse('Username already exists');
    }
    
    $data = [
        'username' => $username,
        'password' => hashPassword($password),
        'full_name' => $fullName,
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'role' => $role,
        'is_active' => 1
    ];
    
    $userId = insertRecord('users', $data);
    
    logActivity('create', 'users', $userId, "Created user: $username");
    
    successResponse(__('save_success'), ['id' => $userId]);
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
