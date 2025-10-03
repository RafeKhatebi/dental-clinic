<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole(['admin', 'dentist'])) {
    errorResponse('Unauthorized', 401);
}

$serviceName = sanitizeInput($_POST['service_name'] ?? '');
$basePrice = floatval($_POST['base_price'] ?? 0);

if (empty($serviceName) || empty($basePrice)) {
    errorResponse(__('required_fields'));
}

try {
    $data = [
        'service_name' => $serviceName,
        'service_name_en' => sanitizeInput($_POST['service_name_en'] ?? ''),
        'category' => sanitizeInput($_POST['category'] ?? ''),
        'description' => sanitizeInput($_POST['description'] ?? ''),
        'base_price' => $basePrice,
        'status' => 'template',
        'is_active' => 1,
        'created_by' => $_SESSION['user_id']
    ];
    
    $serviceId = insertRecord('services', $data);
    
    logActivity('create', 'services', $serviceId, "Created service template: $serviceName");
    
    successResponse(__('save_success'), ['id' => $serviceId]);
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
