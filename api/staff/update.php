<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

$id = intval($data['id'] ?? 0);
$fullName = sanitizeInput($data['full_name'] ?? '');
$jobTitle = sanitizeInput($data['job_title'] ?? '');
$phone = sanitizeInput($data['phone'] ?? '');
$email = sanitizeInput($data['email'] ?? '');
$monthlySalary = floatval($data['monthly_salary'] ?? 0);
$hireDate = $data['hire_date'] ?? date('Y-m-d');
$isActive = intval($data['is_active'] ?? 1);

if (!$id || !$fullName || !$jobTitle || !$phone || !$monthlySalary) {
    errorResponse('All required fields must be filled');
}

try {
    updateRecord('users', [
        'full_name' => $fullName,
        'job_title' => $jobTitle,
        'phone' => $phone,
        'email' => $email,
        'monthly_salary' => $monthlySalary,
        'hire_date' => $hireDate,
        'is_active' => $isActive
    ], 'id = ?', [$id]);

    logActivity('update_staff', 'users', $id, "Updated staff: $fullName");
    successResponse('Staff updated successfully');

} catch (Exception $e) {
    errorResponse($e->getMessage());
}
