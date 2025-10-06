<?php
require_once '../config/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$ids = $input['ids'] ?? [];

if (empty($action) || empty($ids)) errorResponse('Invalid request');

try {
    $db = getDBConnection();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM patients WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        logActivity('bulk_delete', 'patients', null, 'Deleted ' . count($ids) . ' patients');
        successResponse('بیماران انتخابی حذف شدند');
    } else {
        errorResponse('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
