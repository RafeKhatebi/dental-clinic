<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    errorResponse('Unauthorized', 401);
}

$query = sanitizeInput($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $patients = fetchAll("
        SELECT id, patient_code, first_name, last_name, phone
        FROM patients
        WHERE patient_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?
        ORDER BY created_at DESC
        LIMIT 10
    ", ["%$query%", "%$query%", "%$query%", "%$query%"]);
    
    echo json_encode($patients);
} catch (Exception $e) {
    echo json_encode([]);
}
