<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole(['admin', 'dentist'])) {
    errorResponse('Unauthorized', 401);
}

$serviceId = intval($_POST['id'] ?? 0);
$serviceName = sanitizeInput($_POST['service_name'] ?? '');
$basePrice = floatval($_POST['base_price'] ?? 0);

if (empty($serviceId) || empty($serviceName) || empty($basePrice)) {
    errorResponse(__('required_fields'));
}

try {
    $data = [
        'service_name' => $serviceName,
        'service_name_en' => sanitizeInput($_POST['service_name_en'] ?? ''),
        'category' => sanitizeInput($_POST['category'] ?? ''),
        'description' => sanitizeInput($_POST['description'] ?? ''),
        'base_price' => $basePrice
    ];
    
    updateRecord('services', $data, 'id = :id', ['id' => $serviceId]);
    
    logActivity('update', 'services', $serviceId, "Updated service: $serviceName");
    
    successResponse(__('update_success'));
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
