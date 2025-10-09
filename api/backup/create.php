<?php
require_once '../../config/config.php';
require_once '../../includes/backup_manager.php';

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
    $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $backupPath = $backupDir . '/' . $backupName;

    // Get database connection
    $db = getDBConnection();
    
    // Get all tables
    $tables = [];
    $result = $db->query('SHOW TABLES');
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    // Start backup content
    $backup = "-- MySQL Backup\n";
    $backup .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
    $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Loop through tables
    foreach ($tables as $table) {
        // Drop table
        $backup .= "DROP TABLE IF EXISTS `$table`;\n";
        
        // Create table
        $createTable = $db->query("SHOW CREATE TABLE `$table`")->fetch();
        $backup .= $createTable['Create Table'] . ";\n\n";
        
        // Insert data
        $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            foreach ($rows as $row) {
                $values = array_map(function($v) use ($db) {
                    return $v === null ? 'NULL' : $db->quote($v);
                }, array_values($row));
                $backup .= "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n";
            }
            $backup .= "\n";
        }
    }

    $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Save to file
    if (!file_put_contents($backupPath, $backup)) {
        errorResponse('Failed to write backup file');
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
    
    // پاکسازی بکاپهای قدیمی (نگهداری آخرین 5 بکاپ)
    $cleanup = cleanOldBackups(5);

    successResponse(__('backup_success'), [
        'id' => $backupId, 
        'name' => $backupName,
        'cleanup' => $cleanup
    ]);

} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
