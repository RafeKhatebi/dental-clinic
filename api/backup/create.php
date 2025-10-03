<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole('admin')) {
    errorResponse('Unauthorized', 401);
}

try {
    // Create backups directory if not exists
    $backupDir = BASE_PATH . '/backups';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true);
    }

    // Generate backup filename
    $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.db';
    $backupPath = $backupDir . '/' . $backupName;

    // Copy database file
    $dbPath = DB_PATH;

    if (!file_exists($dbPath)) {
        errorResponse('Database file not found');
    }

    if (!copy($dbPath, $backupPath)) {
        errorResponse('Failed to create backup');
    }

    // Get file size
    $fileSize = filesize($backupPath);

    // Generate document code
    $docCode = generateCode('BK', 6);

    // Save backup record
    $backupData = [
        'document_type' => 'backup',
        'document_code' => $docCode,
        'title' => $backupName,
        'file_path' => $backupPath,
        'file_size' => $fileSize,
        'created_by' => $_SESSION['user_id']
    ];

    $backupId = insertRecord('documents', $backupData);

    // Log activity
    logActivity('create', 'documents', $backupId, "Created database backup: $backupName");

    successResponse(__('backup_success'), ['id' => $backupId, 'name' => $backupName]);

} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
