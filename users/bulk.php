<?php
require_once '../config/config.php';

if (!hasRole('admin')) errorResponse('Unauthorized', 401);

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$ids = $input['ids'] ?? [];

if (empty($action) || empty($ids)) errorResponse('Invalid request');

try {
    $db = getDBConnection();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    switch ($action) {
        case 'activate':
            $stmt = $db->prepare("UPDATE users SET is_active = 1 WHERE id IN ($placeholders) AND id != ?");
            $stmt->execute(array_merge($ids, [$_SESSION['user_id']]));
            logActivity('bulk_activate', 'users', null, 'Activated ' . count($ids) . ' users');
            successResponse('کاربران انتخابی فعال شدند');
            break;
            
        case 'deactivate':
            $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE id IN ($placeholders) AND id != ?");
            $stmt->execute(array_merge($ids, [$_SESSION['user_id']]));
            logActivity('bulk_deactivate', 'users', null, 'Deactivated ' . count($ids) . ' users');
            successResponse('کاربران انتخابی غیرفعال شدند');
            break;
            
        case 'delete':
            $stmt = $db->prepare("DELETE FROM users WHERE id IN ($placeholders) AND id != ?");
            $stmt->execute(array_merge($ids, [$_SESSION['user_id']]));
            logActivity('bulk_delete', 'users', null, 'Deleted ' . count($ids) . ' users');
            successResponse('کاربران انتخابی حذف شدند');
            break;
            
        default:
            errorResponse('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
