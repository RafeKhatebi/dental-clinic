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
    // List of settings to update
    $settingsToUpdate = [
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'clinic_email',
        'currency',
        'language',
        'low_stock_alert',
        'expiry_alert_days'
    ];
    
    foreach ($settingsToUpdate as $key) {
        if (isset($_POST[$key])) {
            $value = sanitizeInput($_POST[$key]);
            updateSetting($key, $value);
        }
    }
    
    // Log activity
    logActivity('update', 'settings', null, 'Updated system settings');
    
    successResponse(__('update_success'));
    
} catch (Exception $e) {
    errorResponse(__('error_occurred') . ': ' . $e->getMessage());
}
