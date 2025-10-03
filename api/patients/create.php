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
$firstName = sanitizeInput($_POST['first_name'] ?? '');
$lastName = sanitizeInput($_POST['last_name'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');

if (empty($firstName) || empty($lastName) || empty($phone)) {
    errorResponse(__('required_fields'));
}

try {
    // Generate patient code
    $patientCode = generateCode('P', 6);

    // Check if code already exists
    while (fetchOne("SELECT id FROM patients WHERE patient_code = ?", [$patientCode])) {
        $patientCode = generateCode('P', 6);
    }

    // Prepare data
    $data = [
        'patient_code' => $patientCode,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'age' => !empty($_POST['age']) ? intval($_POST['age']) : null,
        'gender' => sanitizeInput($_POST['gender'] ?? ''),
        'phone' => $phone,
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'address' => sanitizeInput($_POST['address'] ?? ''),
        'medical_history' => sanitizeInput($_POST['medical_history'] ?? ''),
        'allergies' => sanitizeInput($_POST['allergies'] ?? ''),
        'notes' => sanitizeInput($_POST['notes'] ?? ''),
        'created_by' => $_SESSION['user_id']
    ];

    // Insert patient
    $patientId = insertRecord('patients', $data);

    // Log activity
    logActivity('create', 'patients', $patientId, "Created patient: $firstName $lastName");

    successResponse(__('save_success'), ['id' => $patientId, 'patient_code' => $patientCode]);

} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
