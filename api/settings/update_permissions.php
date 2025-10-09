<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if (!hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'متد نامعتبر']);
    exit;
}

try {
    $permissions = $_POST['permissions'] ?? [];
    
    // Always ensure admin has full access
    $allPerms = ['dashboard', 'patients', 'services', 'medicines', 'suppliers', 'financial', 'reports', 'users', 'settings', 'backup'];
    $permissions['admin'] = $allPerms;
    
    // Delete old permissions
    execute("DELETE FROM system WHERE record_type = 'permission'");
    
    // Insert new permissions
    foreach ($permissions as $role => $perms) {
        $data = json_encode($perms);
        execute("INSERT INTO system (record_type, setting_key, data, created_at) VALUES ('permission', ?, ?, NOW())", [$role, $data]);
    }
    
    logActivity('update', 'system', null, 'بهروزرسانی دسترسیها');
    
    echo json_encode(['success' => true, 'message' => 'دسترسیها با موفقیت بهروزرسانی شد']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطا: ' . $e->getMessage()]);
}
