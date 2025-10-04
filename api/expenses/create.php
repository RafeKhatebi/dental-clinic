<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

$title = sanitizeInput($data['title'] ?? '');
$expenseCategory = sanitizeInput($data['expense_category'] ?? '');
$expenseType = $data['expense_type'] ?? 'fixed';
$amount = floatval($data['amount'] ?? 0);
$recurrence = $data['recurrence'] ?? 'monthly';
$nextDueDate = $data['next_due_date'] ?? date('Y-m-d');
$content = sanitizeInput($data['content'] ?? '');

if (!$title || !$expenseCategory || $amount <= 0) {
    errorResponse('All required fields must be filled');
}

try {
    $documentCode = 'EXP-' . date('Ymd') . '-' . rand(1000, 9999);

    $expenseId = insertRecord('documents', [
        'document_type' => 'expense',
        'document_code' => $documentCode,
        'title' => $title,
        'content' => $content,
        'expense_type' => $expenseType,
        'expense_category' => $expenseCategory,
        'amount' => $amount,
        'recurrence' => $recurrence,
        'next_due_date' => $nextDueDate,
        'status' => 'active',
        'created_by' => $_SESSION['user_id']
    ]);

    logActivity('create_expense', 'documents', $expenseId, "Created expense: $title");
    successResponse('Expense created successfully', ['id' => $expenseId]);

} catch (Exception $e) {
    errorResponse($e->getMessage());
}
