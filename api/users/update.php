<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole('admin')) {
    errorResponse('Unauthorized', 401);
}

$userId = intval($_POST['id'] ?? 0);
$username = sanitizeInput($_POST['username'] ?? '');
$fullName = sanitizeInput($_POST['full_name'] ?? '');
$role = sanitizeInput($_POST['role'] ?? '');

if (empty($userId) || empty($username) || empty($fullName) || empty($role)) {
    errorResponse(__('required_fields'));
}

try {
    $existing = fetchOne("SELECT id FROM users WHERE username = ? AND id != ?", [$username, $userId]);
    if ($existing) {
        errorResponse('Username already exists');
    }
    
    $data = [
        'username' => $username,
        'full_name' => $fullName,
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'role' => $role
    ];
    
    if (!empty($_POST['password'])) {
        $data['password'] = hashPassword($_POST['password']);
    }
    
    updateRecord('users', $data, 'id = :id', ['id' => $userId]);
    
    logActivity('update', 'users', $userId, "Updated user: $username");
    
    successResponse(__('update_success'));
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
