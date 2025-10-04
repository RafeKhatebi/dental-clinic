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
$supplierName = sanitizeInput($_POST['supplier_name'] ?? '');
$supplierPhone = sanitizeInput($_POST['supplier_phone'] ?? '');
$supplierEmail = sanitizeInput($_POST['supplier_email'] ?? '');
$supplierAddress = sanitizeInput($_POST['supplier_address'] ?? '');

if (empty($supplierName) || empty($supplierPhone)) {
    errorResponse(__('required_fields'));
}

try {
    // Create a placeholder medicine record for the supplier
    $data = [
        'medicine_code' => 'SUP-' . generateCode('', 6),
        'medicine_name' => 'Supplier: ' . $supplierName,
        'unit' => 'N/A',
        'purchase_price' => 0,
        'sale_price' => 0,
        'stock_quantity' => 0,
        'supplier_name' => $supplierName,
        'supplier_phone' => $supplierPhone,
        'supplier_email' => $supplierEmail,
        'supplier_address' => $supplierAddress,
        'is_active' => 0,
        'created_by' => $_SESSION['user_id']
    ];
    
    $supplierId = insertRecord('medicines', $data);
    
    logActivity('create', 'suppliers', $supplierId, "Created supplier: $supplierName");
    
    successResponse(__('save_success'), ['id' => $supplierId]);
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
