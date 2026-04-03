<?php
// ===== SECURITY HEADERS =====
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\'; connect-src \'self\'; frame-ancestors \'none\'');

// ===== START SESSION (before any output) =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== DATABASE CONFIGURATION =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'baby_shop');

// Create connection with error suppression
@$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection - don't expose error to user
if ($conn->connect_error) {
    // Log error securely (not displayed)
    error_log("Database Connection Error: " . $conn->connect_error);
    // Return JSON error for API calls
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed. Please try again later.',
        'data' => null
    ]);
    exit;
}

// Set charset to utf8
$conn->set_charset("utf8");

// Set timezone to Vietnam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// ===== HELPER FUNCTIONS =====

// Secure JSON response
function jsonResponse($status, $message, $data = null, $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Input validation - prevent SQL injection and XSS
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
}

// Validate integer
function validateInt($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

// CSRF token generation
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token validation
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

// Rate limiting check
function checkRateLimit($identifier, $limit = 100, $window = 3600) {
    $key = 'rate_limit_' . md5($identifier);
    $current = isset($_SESSION[$key]) ? $_SESSION[$key] : [];
    $now = time();
    
    // Remove old entries
    $current = array_filter($current, function($time) use ($now, $window) {
        return ($now - $time) < $window;
    });
    
    if (count($current) >= $limit) {
        return false;
    }
    
    $current[] = $now;
    $_SESSION[$key] = $current;
    return true;
}

// Close connection on script end
register_shutdown_function(function() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
});
?>

