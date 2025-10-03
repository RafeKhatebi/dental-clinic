<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Invalid request method', 405);
}

if (!isLoggedIn() || !hasRole('admin')) {
    errorResponse('Unauthorized', 401);
}

$backupId = intval($_GET['id'] ?? 0);

if (!$backupId) {
    errorResponse('Invalid backup ID');
}

try {
    $backup = fetchOne("SELECT * FROM documents WHERE id = ? AND document_type = 'backup'", [$backupId]);
    
    if (!$backup) {
        errorResponse('Backup not found');
    }
    
    // Delete file
    if (file_exists($backup['file_path'])) {
        unlink($backup['file_path']);
    }
    
    // Delete record
    deleteRecord('documents', 'id = ?', [$backupId]);
    
    logActivity('delete', 'documents', $backupId, 'Deleted backup: ' . $backup['title']);
    
    successResponse('Backup deleted successfully');
    
} catch (Exception $e) {
    errorResponse('Error: ' . $e->getMessage());
}
