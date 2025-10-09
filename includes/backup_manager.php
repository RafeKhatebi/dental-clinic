<?php
/**
 * Backup Manager - مدیریت خودکار بکاپها
 * نگهداری آخرین 5 بکاپ و حذف خودکار بکاپهای قدیمی
 */

function cleanOldBackups($maxBackups = 5) {
    global $db;
    
    try {
        // دریافت لیست بکاپها مرتب شده بر اساس تاریخ
        $backups = fetchAll("
            SELECT id, file_path, created_at 
            FROM documents 
            WHERE document_type = 'backup' 
            ORDER BY created_at DESC
        ");
        
        if (count($backups) <= $maxBackups) {
            return ['deleted' => 0, 'kept' => count($backups)];
        }
        
        $deleted = 0;
        // حذف بکاپهای اضافی
        for ($i = $maxBackups; $i < count($backups); $i++) {
            $backup = $backups[$i];
            
            // حذف فایل فیزیکی
            if (file_exists($backup['file_path'])) {
                unlink($backup['file_path']);
            }
            
            // حذف رکورد از دیتابیس
            execute("DELETE FROM documents WHERE id = ?", [$backup['id']]);
            $deleted++;
        }
        
        return ['deleted' => $deleted, 'kept' => $maxBackups];
        
    } catch (Exception $e) {
        error_log("Backup cleanup error: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

function getBackupStats() {
    $backups = fetchAll("
        SELECT COUNT(*) as total, 
               SUM(file_size) as total_size,
               MAX(created_at) as last_backup
        FROM documents 
        WHERE document_type = 'backup'
    ");
    
    return $backups[0] ?? ['total' => 0, 'total_size' => 0, 'last_backup' => null];
}
