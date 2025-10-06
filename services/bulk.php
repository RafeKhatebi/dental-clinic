<?php
require_once '../config/config.php';

if (!hasRole(['admin', 'dentist'])) errorResponse('Unauthorized', 401);

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$ids = $input['ids'] ?? [];

if (empty($action) || empty($ids)) errorResponse('Invalid request');

try {
    $db = getDBConnection();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    switch ($action) {
        case 'activate':
            $stmt = $db->prepare("UPDATE services SET is_active = 1 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            logActivity('bulk_activate', 'services', null, 'Activated ' . count($ids) . ' services');
            successResponse('موارد انتخابی فعال شدند');
            break;
            
        case 'deactivate':
            $stmt = $db->prepare("UPDATE services SET is_active = 0 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            logActivity('bulk_deactivate', 'services', null, 'Deactivated ' . count($ids) . ' services');
            successResponse('موارد انتخابی غیرفعال شدند');
            break;
            
        case 'delete':
            $stmt = $db->prepare("DELETE FROM services WHERE id IN ($placeholders) AND status = 'template'");
            $stmt->execute($ids);
            logActivity('bulk_delete', 'services', null, 'Deleted ' . count($ids) . ' services');
            successResponse('موارد انتخابی حذف شدند');
            break;
            
        default:
            errorResponse('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
