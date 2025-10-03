<?php
/**
 * Optimized Configuration for New Database Schema
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/Teeth/teeth');

// Include optimized database configuration
require_once __DIR__ . '/database_3307.php';

// Timezone
date_default_timezone_set('Asia/Tehran');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Language settings
define('DEFAULT_LANG', 'fa');
$current_lang = $_SESSION['lang'] ?? DEFAULT_LANG;

/**
 * Load Language File
 */
function loadLanguage($lang = null) {
    global $current_lang;
    $lang = $lang ?? $current_lang;
    
    $langFile = BASE_PATH . "/lang/$lang.php";
    if (file_exists($langFile)) {
        return require $langFile;
    }
    
    return require BASE_PATH . "/lang/fa.php";
}

/**
 * Get Translation
 */
function __($key, $default = '') {
    static $translations = null;
    
    if ($translations === null) {
        $translations = loadLanguage();
    }
    
    return $translations[$key] ?? $default;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Check user role
 */
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (is_array($role)) {
        return in_array($_SESSION['role'], $role);
    }
    
    return $_SESSION['role'] === $role;
}

/**
 * Redirect
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Format Currency
 */
function formatCurrency($amount) {
    return number_format($amount, 0, '.', ',');
}

/**
 * Format Date
 */
function formatDate($date, $format = 'Y-m-d') {
    if (empty($date)) {
        return '';
    }
    
    return date($format, strtotime($date));
}

/**
 * Generate Unique Code
 */
function generateCode($prefix = '', $length = 6) {
    $number = str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

/**
 * Sanitize Input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash Password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify Password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Log Activity - Updated for optimized schema
 */
function logActivity($action, $tableName = null, $recordId = null, $description = null) {
    if (!isLoggedIn()) {
        return;
    }
    
    try {
        $data = [
            'record_type' => 'activity_log',
            'user_id' => $_SESSION['user_id'],
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        insertRecord('system', $data);
    } catch (Exception $e) {
        // Ignore logging errors
    }
}

/**
 * Get Setting Value - Updated for optimized schema
 */
function getSetting($key, $default = '') {
    try {
        $setting = fetchOne("SELECT setting_value FROM system WHERE record_type = 'setting' AND setting_key = ?", [$key]);
        return $setting ? $setting['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Update Setting - Updated for optimized schema
 */
function updateSetting($key, $value) {
    try {
        $exists = fetchOne("SELECT id FROM system WHERE record_type = 'setting' AND setting_key = ?", [$key]);
        
        if ($exists) {
            return updateRecord('system', 
                ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$exists['id']]
            );
        } else {
            return insertRecord('system', [
                'record_type' => 'setting',
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * JSON Response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Success Response
 */
function successResponse($message, $data = null) {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Error Response
 */
function errorResponse($message, $statusCode = 400) {
    jsonResponse([
        'success' => false,
        'message' => $message
    ], $statusCode);
}
?>