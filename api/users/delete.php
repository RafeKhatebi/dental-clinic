<?php
require_once '../../config/config.php';

if (!hasRole('admin')) {
    errorResponse('Unauthorized', 401);
}

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    errorResponse('Invalid user ID');
}

// Prevent deleting self
if ($id == $_SESSION['user_id']) {
    errorResponse('Cannot delete your own account');
}

try {
    deleteRecord('users', 'id = ?', [$id]);
    logActivity('delete_user', 'users', $id, "Deleted user ID: $id");
    successResponse('User deleted successfully');
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
