<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);
$staffId = $data['staff_id'] ?? 0;
$monthYear = $data['month_year'] ?? '';
$amount = $data['amount'] ?? 0;

if (!$staffId || !$monthYear || $amount <= 0) {
    errorResponse('Invalid data');
}

try {
    $db = getDBConnection();
    $db->beginTransaction();

    // Check if already paid
    $existing = fetchOne("SELECT id FROM payments WHERE staff_id = ? AND payment_type = 'salary' AND month_year = ?", [$staffId, $monthYear]);
    if ($existing) {
        errorResponse('Salary already paid for this month');
    }

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

    // Insert salary payment
    $paymentId = insertRecord('payments', [
        'patient_id' => $firstPatient['id'],
        'staff_id' => $staffId,
        'payment_type' => 'salary',
        'amount' => $amount,
        'payment_method' => 'cash',
        'payment_date' => date('Y-m-d'),
        'month_year' => $monthYear,
        'paid_amount' => $amount,
        'paid_date' => date('Y-m-d'),
        'status' => 'paid',
        'created_by' => $_SESSION['user_id']
    ]);

    $db->commit();
    logActivity('pay_salary', 'payments', $paymentId, "Paid salary for month $monthYear");
    successResponse('Salary paid successfully', ['id' => $paymentId]);

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    errorResponse($e->getMessage());
}
