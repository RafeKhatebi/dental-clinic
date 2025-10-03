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
$amount = floatval($_POST['amount'] ?? 0);
$paymentMethod = sanitizeInput($_POST['payment_method'] ?? '');
$paymentDate = sanitizeInput($_POST['payment_date'] ?? '');

if (empty($patientId) || empty($amount) || empty($paymentMethod) || empty($paymentDate)) {
    errorResponse(__('required_fields'));
}

try {
    beginTransaction();
    
    // If installment payment, create multiple payment records
    if ($paymentMethod === 'installment') {
        $installmentCount = intval($_POST['installment_count'] ?? 3);
        $firstDueDate = sanitizeInput($_POST['first_due_date'] ?? date('Y-m-d', strtotime('+1 month')));
        $installmentAmount = $amount / $installmentCount;
        $serviceId = !empty($_POST['patient_service_id']) ? intval($_POST['patient_service_id']) : null;
        $notes = sanitizeInput($_POST['notes'] ?? '');
        
        for ($i = 1; $i <= $installmentCount; $i++) {
            $dueDate = date('Y-m-d', strtotime($firstDueDate . " +" . ($i - 1) . " month"));
            
            $paymentData = [
                'patient_id' => $patientId,
                'service_id' => $serviceId,
                'payment_type' => 'service',
                'amount' => $installmentAmount,
                'payment_method' => 'installment',
                'payment_date' => $paymentDate,
                'installment_number' => $i,
                'total_installments' => $installmentCount,
                'due_date' => $dueDate,
                'status' => 'pending',
                'notes' => $notes,
                'created_by' => $_SESSION['user_id']
            ];
            
            insertRecord('payments', $paymentData);
        }
    } else {
        // Single payment
        $paymentData = [
            'patient_id' => $patientId,
            'service_id' => !empty($_POST['patient_service_id']) ? intval($_POST['patient_service_id']) : null,
            'payment_type' => 'service',
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_date' => $paymentDate,
            'status' => 'paid',
            'paid_amount' => $amount,
            'paid_date' => $paymentDate,
            'notes' => sanitizeInput($_POST['notes'] ?? ''),
            'created_by' => $_SESSION['user_id']
        ];
        
        insertRecord('payments', $paymentData);
    }
    
    commitTransaction();
    
    // Log activity
    logActivity('create', 'payments', null, "Created payment for patient ID: $patientId");
    
    successResponse(__('save_success'));
    
} catch (Exception $e) {
    rollbackTransaction();
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
