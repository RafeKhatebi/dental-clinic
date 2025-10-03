<?php
/**
 * Security Configuration and Functions
 * Dental Clinic Management System - Enhanced Security
 */

// Prevent direct access
if (!defined('SYSTEM_INIT')) {
    http_response_code(403);
    exit('Direct access forbidden');
}

/**
 * Security Manager Class
 */
class SecurityManager {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = SecureDB::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Validate and sanitize input
     */
    public function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return $this->sanitizeInput($item, $type);
            }, $input);
        }
        
        // Remove null bytes and control characters
        $input = str_replace("\0", '', $input);
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'html':
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
            default:
                return trim($input);
        }
    }
    
    /**
     * Validate input against specific rules
     */
    public function validateInput($input, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $input[$field] ?? null;
            
            // Required check
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = "Field {$field} is required";
                continue;
            }
            
            // Skip validation if field is empty and not required
            if (empty($value) && (!isset($rule['required']) || !$rule['required'])) {
                continue;
            }
            
            // Type validation
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "Invalid email format";
                        }
                        break;
                    case 'phone':
                        if (!preg_match('/^[\d\-\+\(\)\s]+$/', $value)) {
                            $errors[$field] = "Invalid phone format";
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field] = "Field must be numeric";
                        }
                        break;
                    case 'date':
                        if (!$this->validateDate($value)) {
                            $errors[$field] = "Invalid date format";
                        }
                        break;
                }
            }
            
            // Length validation
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = "Field must be at least {$rule['min_length']} characters";
            }
            
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = "Field must not exceed {$rule['max_length']} characters";
            }
            
            // Pattern validation
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                $errors[$field] = "Field format is invalid";
            }
        }
        
        return $errors;
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$token] = time() + CSRF_TOKEN_LIFETIME;
        
        // Clean expired tokens
        $this->cleanExpiredTokens();
        
        return $token;
    }
    
    /**
     * Validate CSRF token
     */
    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_tokens'][$token])) {
            return false;
        }
        
        if ($_SESSION['csrf_tokens'][$token] < time()) {
            unset($_SESSION['csrf_tokens'][$token]);
            return false;
        }
        
        // Remove used token
        unset($_SESSION['csrf_tokens'][$token]);
        return true;
    }
    
    /**
     * Clean expired CSRF tokens
     */
    private function cleanExpiredTokens() {
        if (!isset($_SESSION['csrf_tokens'])) {
            return;
        }
        
        $currentTime = time();
        foreach ($_SESSION['csrf_tokens'] as $token => $expiry) {
            if ($expiry < $currentTime) {
                unset($_SESSION['csrf_tokens'][$token]);
            }
        }
    }
    
    /**
     * Check login attempts and lockout
     */
    public function checkLoginAttempts($username) {
        $user = $this->db->fetchOne(
            "SELECT failed_login_attempts, locked_until FROM users WHERE username = ?",
            [$username]
        );
        
        if (!$user) {
            return ['allowed' => false, 'message' => 'User not found'];
        }
        
        // Check if user is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remainingTime = strtotime($user['locked_until']) - time();
            return [
                'allowed' => false,
                'message' => "Account locked. Try again in " . ceil($remainingTime / 60) . " minutes"
            ];
        }
        
        // Check if max attempts reached
        if ($user['failed_login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
            $this->db->update('users', 
                ['locked_until' => $lockUntil], 
                'username = ?', 
                [$username]
            );
            
            return [
                'allowed' => false,
                'message' => 'Too many failed attempts. Account locked for ' . (LOCKOUT_DURATION / 60) . ' minutes'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Record failed login attempt
     */
    public function recordFailedLogin($username) {
        $this->db->execute(
            "UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE username = ?",
            [$username]
        );
        
        $this->logActivity(null, 'failed_login', 'users', null, "Failed login attempt for username: $username");
    }
    
    /**
     * Reset login attempts on successful login
     */
    public function resetLoginAttempts($username) {
        $this->db->update('users', 
            [
                'failed_login_attempts' => 0,
                'locked_until' => null,
                'last_login' => date('Y-m-d H:i:s')
            ], 
            'username = ?', 
            [$username]
        );
    }
    
    /**
     * Log user activity
     */
    public function logActivity($userId, $action, $tableName = null, $recordId = null, $description = null) {
        try {
            $this->db->insert('activity_logs', [
                'user_id' => $userId,
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'description' => $description,
                'ip_address' => $this->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
    
    /**
     * Get client IP address
     */
    public function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Validate date format
     */
    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $action, $resource = null) {
        $user = $this->db->fetchOne("SELECT role FROM users WHERE id = ? AND is_active = 1", [$userId]);
        
        if (!$user) {
            return false;
        }
        
        $permissions = $this->getRolePermissions($user['role']);
        
        if (in_array('*', $permissions)) {
            return true; // Admin has all permissions
        }
        
        $permission = $resource ? "{$action}_{$resource}" : $action;
        return in_array($permission, $permissions);
    }
    
    /**
     * Get role permissions
     */
    private function getRolePermissions($role) {
        $permissions = [
            'admin' => ['*'], // All permissions
            'dentist' => [
                'view_patients', 'create_patients', 'update_patients',
                'view_services', 'create_services', 'provide_services',
                'view_prescriptions', 'create_prescriptions',
                'view_reports'
            ],
            'secretary' => [
                'view_patients', 'create_patients', 'update_patients',
                'view_services', 'provide_services',
                'view_payments', 'create_payments',
                'view_medicines', 'sell_medicines'
            ],
            'accountant' => [
                'view_patients', 'view_services', 'view_payments',
                'view_medicines', 'view_reports', 'manage_finances'
            ]
        ];
        
        return $permissions[$role] ?? [];
    }
    
    /**
     * Encrypt sensitive data
     */
    public function encryptData($data, $key = null) {
        if ($key === null) {
            $key = $this->getEncryptionKey();
        }
        
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt sensitive data
     */
    public function decryptData($encryptedData, $key = null) {
        if ($key === null) {
            $key = $this->getEncryptionKey();
        }
        
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    /**
     * Get encryption key
     */
    private function getEncryptionKey() {
        // In production, this should be stored securely (environment variable, key management service, etc.)
        return hash('sha256', 'dental_clinic_encryption_key_2024', true);
    }
    
    /**
     * Generate secure random password
     */
    public function generateSecurePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
    
    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return $errors;
    }
}

/**
 * Authentication Manager Class
 */
class AuthManager {
    private static $instance = null;
    private $security;
    private $db;
    
    private function __construct() {
        $this->security = SecurityManager::getInstance();
        $this->db = SecureDB::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        // Check login attempts
        $attemptCheck = $this->security->checkLoginAttempts($username);
        if (!$attemptCheck['allowed']) {
            return ['success' => false, 'message' => $attemptCheck['message']];
        }
        
        // Get user
        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE username = ? AND is_active = 1",
            [$username]
        );
        
        if (!$user || !password_verify($password, $user['password'])) {
            $this->security->recordFailedLogin($username);
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Reset login attempts
        $this->security->resetLoginAttempts($username);
        
        // Create session
        $this->createSession($user);
        
        // Log successful login
        $this->security->logActivity($user['id'], 'login', 'users', $user['id'], 'User logged in successfully');
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Create user session
     */
    private function createSession($user) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->security->logActivity($_SESSION['user_id'], 'logout', 'users', $_SESSION['user_id'], 'User logged out');
        }
        
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Require authentication
     */
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Require specific permission
     */
    public function requirePermission($action, $resource = null) {
        $this->requireAuth();
        
        if (!$this->security->hasPermission($_SESSION['user_id'], $action, $resource)) {
            http_response_code(403);
            die('Access denied');
        }
    }
}

// Helper functions
function sanitize($input, $type = 'string') {
    return SecurityManager::getInstance()->sanitizeInput($input, $type);
}

function validate($input, $rules) {
    return SecurityManager::getInstance()->validateInput($input, $rules);
}

function csrf_token() {
    return SecurityManager::getInstance()->generateCSRFToken();
}

function verify_csrf($token) {
    return SecurityManager::getInstance()->validateCSRFToken($token);
}

function log_activity($action, $table = null, $recordId = null, $description = null) {
    $userId = $_SESSION['user_id'] ?? null;
    SecurityManager::getInstance()->logActivity($userId, $action, $table, $recordId, $description);
}

function require_auth() {
    AuthManager::getInstance()->requireAuth();
}

function require_permission($action, $resource = null) {
    AuthManager::getInstance()->requirePermission($action, $resource);
}

function current_user() {
    return AuthManager::getInstance()->getCurrentUser();
}

function is_authenticated() {
    return AuthManager::getInstance()->isAuthenticated();
}