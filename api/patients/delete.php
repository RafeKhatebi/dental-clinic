<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$patientId = intval($_GET['id'] ?? 0);

if (empty($patientId)) {
    errorResponse('Patient ID is required');
}

try {
    // Check if patient exists
    $patient = fetchOne("SELECT * FROM patients WHERE id = ?", [$patientId]);
    
    if (!$patient) {
        errorResponse('Patient not found');
    }
    
    // Check if patient has services
    $hasServices = fetchOne("SELECT COUNT(*) as count FROM services WHERE patient_id = ?", [$patientId])['count'];
    
    if ($hasServices > 0) {
        errorResponse('Cannot delete patient with existing services');
    }
    
    // Delete patient
    deleteRecord('patients', 'id = :id', ['id' => $patientId]);
    
    // Log activity
    logActivity('delete', 'patients', $patientId, "Deleted patient: {$patient['first_name']} {$patient['last_name']}");
    
    successResponse(__('delete_success'));
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
