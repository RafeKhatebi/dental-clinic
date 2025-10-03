<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !hasRole('admin')) {
    die('Unauthorized');
}

$backupId = intval($_GET['id'] ?? 0);

if (!$backupId) {
    die('Invalid backup ID');
}

$backup = fetchOne("SELECT * FROM documents WHERE id = ? AND document_type = 'backup'", [$backupId]);

if (!$backup || !file_exists($backup['file_path'])) {
    die('Backup file not found');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $backup['title'] . '"');
header('Content-Length: ' . filesize($backup['file_path']));
readfile($backup['file_path']);
exit;
