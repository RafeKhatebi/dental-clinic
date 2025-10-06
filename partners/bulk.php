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
            $stmt = $db->prepare("UPDATE documents SET status = 'active' WHERE id IN ($placeholders) AND document_type = 'partner_share'");
            $stmt->execute($ids);
            logActivity('bulk_activate', 'partners', null, 'Activated ' . count($ids) . ' partners');
            successResponse('شرکا فعال شدند');
            break;
            
        case 'deactivate':
            $stmt = $db->prepare("UPDATE documents SET status = 'inactive' WHERE id IN ($placeholders) AND document_type = 'partner_share'");
            $stmt->execute($ids);
            logActivity('bulk_deactivate', 'partners', null, 'Deactivated ' . count($ids) . ' partners');
            successResponse('شرکا غیرفعال شدند');
            break;
            
        default:
            errorResponse('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
