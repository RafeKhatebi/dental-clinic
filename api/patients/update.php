<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$patientId = intval($_POST['id'] ?? 0);

if (empty($patientId)) {
    errorResponse('Patient ID is required');
}

// Validate required fields
$firstName = sanitizeInput($_POST['first_name'] ?? '');
$lastName = sanitizeInput($_POST['last_name'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');

if (empty($firstName) || empty($lastName) || empty($phone)) {
    errorResponse(__('required_fields'));
}

try {
    // Check if patient exists
    $patient = fetchOne("SELECT * FROM patients WHERE id = ?", [$patientId]);
    
    if (!$patient) {
        errorResponse('Patient not found');
    }
    
    // Prepare data
    $data = [
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
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Update patient
    updateRecord('patients', $data, 'id = :id', ['id' => $patientId]);
    
    // Log activity
    logActivity('update', 'patients', $patientId, "Updated patient: $firstName $lastName");
    
    successResponse(__('update_success'));
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
