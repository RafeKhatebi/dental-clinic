<?php
require_once '../config/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$ids = $input['ids'] ?? [];

if (empty($action) || empty($ids)) errorResponse('Invalid request');

try {
    $db = getDBConnection();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    switch ($action) {
        case 'activate':
            $stmt = $db->prepare("UPDATE users SET is_active = 1 WHERE id IN ($placeholders) AND is_staff = 1");
            $stmt->execute($ids);
            logActivity('bulk_activate', 'staff', null, 'Activated ' . count($ids) . ' staff');
            successResponse('کارمندان انتخابی فعال شدند');
            break;
            
        case 'deactivate':
            $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE id IN ($placeholders) AND is_staff = 1");
            $stmt->execute($ids);
            logActivity('bulk_deactivate', 'staff', null, 'Deactivated ' . count($ids) . ' staff');
            successResponse('کارمندان انتخابی غیرفعال شدند');
            break;
            
        default:
            errorResponse('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
