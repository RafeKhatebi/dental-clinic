<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

$id = intval($data['id'] ?? 0);
$title = sanitizeInput($data['title'] ?? '');
$expenseCategory = sanitizeInput($data['expense_category'] ?? '');
$expenseType = $data['expense_type'] ?? 'fixed';
$amount = floatval($data['amount'] ?? 0);
$recurrence = $data['recurrence'] ?? 'monthly';
$nextDueDate = $data['next_due_date'] ?? date('Y-m-d');
$status = $data['status'] ?? 'active';
$content = sanitizeInput($data['content'] ?? '');

if (!$id || !$title || !$expenseCategory || $amount <= 0) {
    errorResponse('All required fields must be filled');
}

try {
    updateRecord('documents', [
        'title' => $title,
        'content' => $content,
        'expense_type' => $expenseType,
        'expense_category' => $expenseCategory,
        'amount' => $amount,
        'recurrence' => $recurrence,
        'next_due_date' => $nextDueDate,
        'status' => $status
    ], 'id = ?', [$id]);

    logActivity('update_expense', 'documents', $id, "Updated expense: $title");
    successResponse('Expense updated successfully');

} catch (Exception $e) {
    errorResponse($e->getMessage());
}
