<?php
require_once '../../config/config.php';

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

$fullName = sanitizeInput($data['full_name'] ?? '');
$jobTitle = sanitizeInput($data['job_title'] ?? '');
$phone = sanitizeInput($data['phone'] ?? '');
$email = sanitizeInput($data['email'] ?? '');
$monthlySalary = floatval($data['monthly_salary'] ?? 0);
$hireDate = $data['hire_date'] ?? date('Y-m-d');

if (!$fullName || !$jobTitle || !$phone || !$monthlySalary) {
    errorResponse('All required fields must be filled');
}

// Generate unique username
$username = 'staff_' . time();
$password = 'staff123';

try {
    $userId = insertRecord('users', [
        'username' => $username,
        'password' => hashPassword($password),
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'role' => 'secretary',
        'job_title' => $jobTitle,
        'monthly_salary' => $monthlySalary,
        'salary_currency' => 'افغانی',
        'hire_date' => $hireDate,
        'is_staff' => 1,
        'is_active' => 1
    ]);

    logActivity('create_staff', 'users', $userId, "Created staff: $fullName");
    successResponse('Staff created successfully', ['id' => $userId]);

} catch (Exception $e) {
    errorResponse($e->getMessage());
}
