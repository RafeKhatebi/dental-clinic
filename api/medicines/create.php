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
$medicineName = sanitizeInput($_POST['medicine_name'] ?? '');
$unit = sanitizeInput($_POST['unit'] ?? '');
$purchasePrice = floatval($_POST['purchase_price'] ?? 0);
$salePrice = floatval($_POST['sale_price'] ?? 0);

if (empty($medicineName) || empty($unit)) {
    errorResponse(__('required_fields'));
}

try {
    // Generate medicine code
    $medicineCode = generateCode('M', 6);

    // Check if code already exists
    while (fetchOne("SELECT id FROM medicines WHERE medicine_code = ?", [$medicineCode])) {
        $medicineCode = generateCode('M', 6);
    }

    // Prepare data
    $data = [
        'medicine_code' => $medicineCode,
        'medicine_name' => $medicineName,
        'medicine_name_en' => sanitizeInput($_POST['medicine_name_en'] ?? ''),
        'category' => sanitizeInput($_POST['category'] ?? ''),
        'manufacturer' => sanitizeInput($_POST['manufacturer'] ?? ''),
        'unit' => $unit,
        'purchase_price' => $purchasePrice,
        'sale_price' => $salePrice,
        'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
        'min_stock_level' => intval($_POST['min_stock_level'] ?? 10),
        'expiry_date' => !empty($_POST['expiry_date']) ? sanitizeInput($_POST['expiry_date']) : null,
        'description' => sanitizeInput($_POST['description'] ?? ''),
        'is_active' => 1
    ];

    // Insert medicine
    $medicineId = insertRecord('medicines', $data);

    // Log activity
    logActivity('create', 'medicines', $medicineId, "Created medicine: $medicineName");

    successResponse(__('save_success'), ['id' => $medicineId, 'medicine_code' => $medicineCode]);

} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
