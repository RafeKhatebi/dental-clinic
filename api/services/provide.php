<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

// Validate required fields
$patientId = intval($_POST['patient_id'] ?? 0);
$serviceId = intval($_POST['service_id'] ?? 0);
$dentistId = intval($_POST['dentist_id'] ?? 0);
$serviceDate = sanitizeInput($_POST['service_date'] ?? '');
$quantity = intval($_POST['quantity'] ?? 1);
$unitPrice = floatval($_POST['unit_price'] ?? 0);
$discount = floatval($_POST['discount'] ?? 0);

if (empty($patientId) || empty($serviceId) || empty($dentistId) || empty($serviceDate)) {
    errorResponse(__('required_fields'));
}

try {
    // Calculate prices
    $totalPrice = $quantity * $unitPrice;
    $finalPrice = $totalPrice - $discount;
    
    // Prepare data
    $data = [
        'patient_id' => $patientId,
        'service_id' => $serviceId,
        'dentist_id' => $dentistId,
        'service_date' => $serviceDate,
        'tooth_number' => sanitizeInput($_POST['tooth_number'] ?? ''),
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'total_price' => $totalPrice,
        'discount' => $discount,
        'final_price' => $finalPrice,
        'notes' => sanitizeInput($_POST['notes'] ?? ''),
        'status' => 'completed',
        'created_by' => $_SESSION['user_id']
    ];
    
    // Get service template info
    $service = fetchOne("SELECT service_name, service_name_en, category FROM services WHERE id = ? AND status = 'template'", [$serviceId]);
    
    // Add service info to data
    $data['service_name'] = $service['service_name'] ?? '';
    $data['service_name_en'] = $service['service_name_en'] ?? '';
    $data['category'] = $service['category'] ?? '';
    $data['base_price'] = $unitPrice;
    $data['status'] = 'completed';
    unset($data['service_id']);
    
    // Insert service
    $serviceRecordId = insertRecord('services', $data);
    
    // Log activity
    logActivity('create', 'services', $serviceRecordId, "Provided service to patient ID: $patientId");
    
    successResponse(__('save_success'), ['id' => $serviceRecordId]);
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
