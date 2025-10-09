<?php
/**
 * Security Helper
 * CSRF Protection & Rate Limiting
 */

// ==================== CSRF Protection ====================

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Get CSRF token meta tag
 */
function csrfMeta() {
    $token = generateCSRFToken();
    return '<meta name="csrf-token" content="' . $token . '">';
}

/**
 * Check CSRF token in request
 */
function checkCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
        $_SERVER['REQUEST_METHOD'] === 'PUT' || 
        $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (!validateCSRFToken($token)) {
            http_response_code(403);
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            } else {
                die('Invalid CSRF token');
            }
            exit;
        }
    }
}

// ==================== Rate Limiting ====================

/**
 * Check rate limit
 */
function checkRateLimit($action = 'general', $maxAttempts = 10, $timeWindow = 60) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_{$action}_{$ip}";
    
    // Get current attempts
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];
    
    // Reset if time window passed
    if (time() - $attempts['time'] > $timeWindow) {
        $attempts = ['count' => 0, 'time' => time()];
    }
    
    // Increment attempts
    $attempts['count']++;
    $_SESSION[$key] = $attempts;
    
    // Check if exceeded
    if ($attempts['count'] > $maxAttempts) {
        $remainingTime = $timeWindow - (time() - $attempts['time']);
        
        http_response_code(429);
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => "تعداد درخواست‌ها بیش از حد مجاز. لطفاً $remainingTime ثانیه صبر کنید."
            ]);
        } else {
            die("تعداد درخواست‌ها بیش از حد مجاز. لطفاً $remainingTime ثانیه صبر کنید.");
        }
        exit;
    }
    
    return true;
}

/**
 * Reset rate limit
 */
function resetRateLimit($action = 'general') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_{$action}_{$ip}";
    unset($_SESSION[$key]);
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// ==================== Login Rate Limiting ====================

/**
 * Check login attempts
 */
function checkLoginAttempts($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "login_attempts_{$ip}_{$username}";
    
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];
    
    // Reset after 15 minutes
    if (time() - $attempts['time'] > 900) {
        $attempts = ['count' => 0, 'time' => time()];
    }
    
    // Check if blocked
    if ($attempts['count'] >= 5) {
        $remainingTime = ceil((900 - (time() - $attempts['time'])) / 60);
        return [
            'allowed' => false,
            'message' => "تعداد تلاش‌های ناموفق بیش از حد. لطفاً $remainingTime دقیقه صبر کنید."
        ];
    }
    
    return ['allowed' => true];
}

/**
 * Record failed login
 */
function recordFailedLogin($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "login_attempts_{$ip}_{$username}";
    
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];
    $attempts['count']++;
    $attempts['time'] = time();
    $_SESSION[$key] = $attempts;
}

/**
 * Reset login attempts
 */
function resetLoginAttempts($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "login_attempts_{$ip}_{$username}";
    unset($_SESSION[$key]);
}

// ==================== IP Whitelist/Blacklist ====================

/**
 * Check if IP is blocked
 */
function isIPBlocked($ip = null) {
    $ip = $ip ?? $_SERVER['REMOTE_ADDR'];
    
    // Check blacklist (can be stored in database)
    $blacklist = [
        // Add blocked IPs here
    ];
    
    return in_array($ip, $blacklist);
}

/**
 * Block current IP
 */
function blockCurrentIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    // Store in database or file
    error_log("Blocked IP: $ip");
}
?>
