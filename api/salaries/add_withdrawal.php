<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);
$staffId = $data['staff_id'] ?? 0;
$amount = $data['amount'] ?? 0;
$paymentDate = $data['payment_date'] ?? date('Y-m-d');
$monthYear = $data['month_year'] ?? date('Y-m');
$notes = $data['notes'] ?? '';

if (!$staffId || $amount <= 0) {
    errorResponse('Invalid data');
}

try {
    // Get first patient or create dummy
    $firstPatient = fetchOne("SELECT id FROM patients LIMIT 1");
    if (!$firstPatient) {
        $firstPatient = ['id' => insertRecord('patients', [
            'patient_code' => 'SYS-001',
            'first_name' => 'System',
            'last_name' => 'Account',
            'phone' => '0000000000'
        ])];
    }
    
    $paymentId = insertRecord('payments', [
        'patient_id' => $firstPatient['id'],
        'staff_id' => $staffId,
        'payment_type' => 'withdrawal',
        'amount' => $amount,
        'payment_method' => 'cash',
        'payment_date' => $paymentDate,
        'month_year' => $monthYear,
        'paid_amount' => $amount,
        'paid_date' => $paymentDate,
        'status' => 'paid',
        'notes' => $notes,
        'created_by' => $_SESSION['user_id']
    ]);

    logActivity('add_withdrawal', 'payments', $paymentId, "Staff withdrawal: $amount");
    successResponse('Withdrawal added successfully', ['id' => $paymentId]);

} catch (Exception $e) {
    errorResponse($e->getMessage());
}
