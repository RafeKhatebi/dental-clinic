<?php
/**
 * Error Handler Helper
 * Centralized error handling for API endpoints
 */

/**
 * Send JSON response
 */
function sendResponse($success, $message, $data = null, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');

    $response = [
        'success' => $success,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response);
    exit;
}

/**
 * Handle API errors with try-catch
 */
function handleApiError($e, $context = 'Operation')
{
    // Log error
    error_log("API Error in $context: " . $e->getMessage());

    // Send user-friendly error
    $message = 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.';

    // In development, show actual error
    if (defined('DEBUG') && DEBUG === true) {
        $message = $e->getMessage();
    }

    sendResponse(false, $message, null, 500);
}

/**
 * Validate required fields
 */
function validateRequired($fields, $data)
{
    $missing = [];

    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        sendResponse(false, 'فیلدهای الزامی خالی است: ' . implode(', ', $missing), null, 400);
    }

    return true;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data)
{
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }

    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>