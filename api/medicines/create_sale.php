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
$saleDate = sanitizeInput($_POST['sale_date'] ?? '');
$paymentMethod = sanitizeInput($_POST['payment_method'] ?? '');
$items = $_POST['items'] ?? [];

if (empty($saleDate) || empty($paymentMethod) || empty($items)) {
    errorResponse(__('required_fields'));
}

try {
    beginTransaction();

    // Generate sale code
    $saleCode = generateCode('S', 6);
    while (fetchOne("SELECT id FROM medicines WHERE sale_code = ?", [$saleCode])) {
        $saleCode = generateCode('S', 6);
    }

    // Calculate totals
    $totalAmount = 0;
    foreach ($items as $item) {
        $quantity = intval($item['quantity'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $totalAmount += $quantity * $unitPrice;
    }

    $discount = floatval($_POST['discount'] ?? 0);
    $finalAmount = $totalAmount - $discount;

    // Process each medicine sale
    $saleIds = [];
    foreach ($items as $item) {
        $medicineId = intval($item['medicine_id'] ?? 0);
        $quantity = intval($item['quantity'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);

        if (empty($medicineId) || empty($quantity)) {
            continue;
        }

        // Check stock
        $medicine = fetchOne("SELECT stock_quantity FROM medicines WHERE id = ?", [$medicineId]);
        if (!$medicine || $medicine['stock_quantity'] < $quantity) {
            throw new Exception('Insufficient stock for medicine ID: ' . $medicineId);
        }

        // Update medicine with sale data
        updateRecord('medicines', [
            'sale_code' => $saleCode,
            'sale_patient_id' => !empty($_POST['patient_id']) ? intval($_POST['patient_id']) : null,
            'sale_customer_name' => sanitizeInput($_POST['customer_name'] ?? ''),
            'sale_date' => $saleDate,
            'sale_quantity' => $quantity,
            'sale_unit_price' => $unitPrice,
            'sale_total_price' => $finalAmount,
            'sale_payment_method' => $paymentMethod,
            'stock_quantity' => $medicine['stock_quantity'] - $quantity
        ], 'id = :id', ['id' => $medicineId]);

        $saleIds[] = $medicineId;
    }

    // Create payment record
    $paymentData = [
        'patient_id' => !empty($_POST['patient_id']) ? intval($_POST['patient_id']) : null,
        'medicine_id' => $saleIds[0] ?? null,
        'payment_type' => 'medicine',
        'amount' => $finalAmount,
        'payment_method' => $paymentMethod,
        'payment_date' => $saleDate,
        'notes' => 'Medicine sale: ' . $saleCode,
        'created_by' => $_SESSION['user_id']
    ];
    insertRecord('payments', $paymentData);

    commitTransaction();

    // Log activity
    logActivity('create', 'medicines', $saleIds[0] ?? null, "Created medicine sale: $saleCode");

    successResponse(__('save_success'), ['id' => $saleId, 'sale_code' => $saleCode]);

} catch (Exception $e) {
    rollbackTransaction();
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
