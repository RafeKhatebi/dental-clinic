<?php
/**
 * Main Configuration File
 * Dental Clinic Management System
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/Teeth/teeth');

// Include database configuration
require_once __DIR__ . '/database.php';

// Include security helpers
require_once BASE_PATH . '/includes/security.php';

// Timezone
date_default_timezone_set('Asia/Tehran');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Language settings
define('DEFAULT_LANG', 'fa');

// Handle language switching
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fa', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$current_lang = $_SESSION['lang'] ?? DEFAULT_LANG;

/**
 * Load Language File
 */
function loadLanguage($lang = null)
{
    global $current_lang;
    $lang = $lang ?? $current_lang;

    $langFile = BASE_PATH . "/lang/$lang.php";
    $translations = [];

    if (file_exists($langFile)) {
        $translations = require $langFile;
    } else {
        $translations = require BASE_PATH . "/lang/fa.php";
    }

    return $translations;
}

/**
 * Get Translation
 */
function __($key, $default = '')
{
    static $translations = null;

    if ($translations === null) {
        $translations = loadLanguage();
    }

    return $translations[$key] ?? $default;
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user
 */
function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'role' => $_SESSION['role'] ?? ''
    ];
}

/**
 * Check user role
 */
function hasRole($role)
{
    if (!isLoggedIn()) {
        return false;
    }

    if (is_array($role)) {
        return in_array($_SESSION['role'], $role);
    }

    return $_SESSION['role'] === $role;
}

/**
 * Check permission for module
 */
function hasPermission($module)
{
    if (!isLoggedIn()) {
        return false;
    }

    // Admin always has full access
    if ($_SESSION['role'] === 'admin') {
        return true;
    }

    // Load permissions from database
    static $permissions = null;
    if ($permissions === null) {
        $permsData = fetchOne("SELECT data FROM system WHERE record_type = 'permission' AND setting_key = ?", [$_SESSION['role']]);
        $permissions = $permsData ? json_decode($permsData['data'], true) : [];
    }

    return in_array($module, $permissions);
}

/**
 * Redirect
 */
function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Format Currency
 */
function formatCurrency($amount)
{
    return number_format($amount, 0, '.', ',');
}

/**
 * Format Date
 */
function formatDate($date, $format = 'Y-m-d')
{
    if (empty($date)) {
        return '';
    }

    return date($format, strtotime($date));
}

/**
 * Generate Unique Code
 */
function generateCode($prefix = '', $length = 6)
{
    $number = str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

/**
 * Sanitize Input
 */
function sanitizeInput($data)
{
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }

    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Email
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash Password
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify Password
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Log Activity
 */
function logActivity($action, $tableName = null, $recordId = null, $description = null)
{
    if (!isLoggedIn()) {
        return;
    }

    try {
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO system (record_type, user_id, action, table_name, record_id, description, ip_address, user_agent) VALUES ('activity_log', ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $tableName,
            $recordId,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignore logging errors
    }
}

/**
 * Get Setting Value
 */
function getSetting($key, $default = '')
{
    try {
        $setting = fetchOne("SELECT setting_value FROM system WHERE record_type = 'setting' AND setting_key = ?", [$key]);
        return $setting ? $setting['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Update Setting
 */
function updateSetting($key, $value)
{
    try {
        $db = getDBConnection();
        $exists = fetchOne("SELECT id FROM system WHERE record_type = 'setting' AND setting_key = ?", [$key]);

        if ($exists) {
            $stmt = $db->prepare("UPDATE system SET setting_value = ?, updated_at = ? WHERE id = ?");
            return $stmt->execute([$value, date('Y-m-d H:i:s'), $exists['id']]);
        } else {
            $stmt = $db->prepare("INSERT INTO system (record_type, setting_key, setting_value) VALUES ('setting', ?, ?)");
            return $stmt->execute([$key, $value]);
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get Pagination Data
 */
function getPagination($totalRecords, $perPage = 20)
{
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $totalPages = ceil($totalRecords / $perPage);
    $offset = ($page - 1) * $perPage;

    return [
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords,
        'offset' => $offset,
        'hasNext' => $page < $totalPages,
        'hasPrev' => $page > 1
    ];
}

/**
 * Render Pagination
 */
function renderPagination($pagination)
{
    if ($pagination['totalPages'] <= 1)
        return '';

    global $current_lang;
    $page = $pagination['page'];
    $totalPages = $pagination['totalPages'];

    // Preserve existing query parameters
    $queryParams = $_GET;
    unset($queryParams['page']);
    $queryString = http_build_query($queryParams);
    $baseUrl = '?' . ($queryString ? $queryString . '&' : '');

    $html = '<div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t">';
    $html .= '<div class="text-sm text-gray-700">';
    $html .= ($current_lang === 'fa' ? 'صفحه ' : 'Page ') . $page . ($current_lang === 'fa' ? ' از ' : ' of ') . $totalPages;
    $html .= ' (' . number_format($pagination['totalRecords']) . ($current_lang === 'fa' ? ' رکورد)' : ' records)');
    $html .= '</div>';
    $html .= '<div class="flex gap-2">';

    // Previous
    if ($pagination['hasPrev']) {
        $html .= '<a href="' . $baseUrl . 'page=' . ($page - 1) . '" class="px-3 py-1 border rounded hover:bg-gray-100">' . ($current_lang === 'fa' ? 'قبلی' : 'Previous') . '</a>';
    }

    // Pages
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    if ($start > 1) {
        $html .= '<a href="' . $baseUrl . 'page=1" class="px-3 py-1 border rounded hover:bg-gray-100">1</a>';
        if ($start > 2)
            $html .= '<span class="px-3 py-1">...</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100';
        $html .= '<a href="' . $baseUrl . 'page=' . $i . '" class="px-3 py-1 border rounded ' . $active . '">' . $i . '</a>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1)
            $html .= '<span class="px-3 py-1">...</span>';
        $html .= '<a href="' . $baseUrl . 'page=' . $totalPages . '" class="px-3 py-1 border rounded hover:bg-gray-100">' . $totalPages . '</a>';
    }

    // Next
    if ($pagination['hasNext']) {
        $html .= '<a href="' . $baseUrl . 'page=' . ($page + 1) . '" class="px-3 py-1 border rounded hover:bg-gray-100">' . ($current_lang === 'fa' ? 'بعدی' : 'Next') . '</a>';
    }

    $html .= '</div></div>';
    return $html;
}

/**
 * JSON Response
 */
function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Success Response
 */
function successResponse($message, $data = null)
{
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Error Response
 */
function errorResponse($message, $statusCode = 400)
{
    jsonResponse([
        'success' => false,
        'message' => $message
    ], $statusCode);
}

// Initialize database on first load
initializeDatabase();

// Check IP blocking
if (isIPBlocked()) {
    http_response_code(403);
    die('Access Denied');
}
