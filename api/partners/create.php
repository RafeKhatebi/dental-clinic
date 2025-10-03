<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole('admin')) {
    errorResponse('Unauthorized', 401);
}

$partnerName = sanitizeInput($_POST['partner_name'] ?? '');
$partnerPhone = sanitizeInput($_POST['partner_phone'] ?? '');
$sharePercentage = floatval($_POST['share_percentage'] ?? 0);
$periodStart = sanitizeInput($_POST['period_start'] ?? '');

if (empty($partnerName) || empty($partnerPhone) || empty($sharePercentage) || empty($periodStart)) {
    errorResponse(__('required_fields'));
}

try {
    $docCode = generateCode('PT', 6);
    
    $data = [
        'document_type' => 'partner_share',
        'document_code' => $docCode,
        'title' => $partnerName,
        'partner_name' => $partnerName,
        'partner_phone' => $partnerPhone,
        'partner_email' => sanitizeInput($_POST['partner_email'] ?? ''),
        'share_percentage' => $sharePercentage,
        'period_start' => $periodStart,
        'period_end' => !empty($_POST['period_end']) ? sanitizeInput($_POST['period_end']) : null,
        'status' => 'active',
        'created_by' => $_SESSION['user_id']
    ];
    
    $partnerId = insertRecord('documents', $data);
    
    logActivity('create', 'documents', $partnerId, "Created partner: $partnerName");
    
    successResponse(__('save_success'), ['id' => $partnerId]);
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
