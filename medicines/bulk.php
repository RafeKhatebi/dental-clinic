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
            $stmt = $db->prepare("UPDATE medicines SET is_active = 1 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            logActivity('bulk_activate', 'medicines', null, 'Activated ' . count($ids) . ' medicines');
            successResponse('داروهای انتخابی فعال شدند');
            break;
            
        case 'deactivate':
            $stmt = $db->prepare("UPDATE medicines SET is_active = 0 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            logActivity('bulk_deactivate', 'medicines', null, 'Deactivated ' . count($ids) . ' medicines');
            successResponse('داروهای انتخابی غیرفعال شدند');
            break;
            
        case 'delete':
            $stmt = $db->prepare("DELETE FROM medicines WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            logActivity('bulk_delete', 'medicines', null, 'Deleted ' . count($ids) . ' medicines');
            successResponse('داروهای انتخابی حذف شدند');
            break;
            
        default:
            errorResponse('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
