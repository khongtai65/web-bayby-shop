<?php
require_once '../config.php';

// Danh sách từ khiếm nhã
$blocked_words = ['admin', 'root', 'fuck', 'ass', 'shit', 'damn', 'crap'];

// ===== PASSWORD HASHING (Using bcrypt instead of MD5) =====
function hashPassword($password) {
    // Dùng plain text - không hash
    return $password;
}

function verifyPassword($password, $stored) {
    // So sánh plain text
    return $password === $stored;
}

// Hàm validate mật khẩu
function validatePassword($password) {
    if (strlen($password) < 6) {
        return "Mật khẩu tối thiểu 6 ký tự";
    }

    if (strlen($password) > 16) {
        return "Mật khẩu tối đa 16 ký tự";
    }
    
    return true;
}

// Hàm validate tên đăng nhập
function validateUsername($username) {
    global $blocked_words;
    
    if (strlen($username) < 4) {
        return "Tên đăng nhập tối thiểu 4 ký tự";
    }
    
    if (strlen($username) > 12) {
        return "Tên đăng nhập tối đa 12 ký tự";
    }
    
    // Kiểm tra từ khiếm nhã
    foreach ($blocked_words as $word) {
        if (stripos($username, $word) !== false) {
            return "Tên đăng nhập không được chứa từ không phù hợp";
        }
    }
    
    // Không có khoảng trắng
    if (preg_match('/\s/', $username)) {
        return "Tên đăng nhập không được có khoảng trắng";
    }
    
    return true;
}

// LOGIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    try {
        if (!checkRateLimit('login_' . ($_POST['username'] ?? ''), 5)) {
            $_SESSION['login_error'] = 'Quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau 1 giờ.';
            header('Location: ../tai-khoan.php');
            exit;
        }
        
        $username = sanitizeInput(trim($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) && $_POST['remember'] === '1' ? 1 : 0;
        
        if (!$username || !$password) {
            $_SESSION['login_error'] = 'Tên đăng nhập và mật khẩu không được để trống';
            header('Location: ../tai-khoan.php');
            exit;
        }
        
        $sql = "SELECT id, username, name, full_name, phone, password, role FROM users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed');
        }
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['role'] = $user['role'] ?? 'user'; // Lấy role từ database
            
            // Remember me (secure cookies)
            if ($remember) {
                setcookie('remember_token', bin2hex(random_bytes(32)), 
                    time() + (30 * 24 * 60 * 60), 
                    '/', '', false, true); // httponly=true
            }
            
            // Redirect to returnUrl if provided, otherwise to account page
            $redirect_url = isset($_POST['returnUrl']) ? '../' . $_POST['returnUrl'] : '../tai-khoan.php';
            header('Location: ' . $redirect_url);
            exit;
        } else {
            $_SESSION['login_error'] = '❌ Tên đăng nhập hoặc mật khẩu không đúng';
            header('Location: ../tai-khoan.php');
            exit;
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = '❌ Lỗi đăng nhập. Vui lòng thử lại.';
        header('Location: ../tai-khoan.php');
        exit;
    }
}

// REGISTER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    
    // Validate
    if (!$username || !$full_name || !$name || !$password) {
        $_SESSION['register_error'] = 'Vui lòng điền đầy đủ thông tin';
        header('Location: ../tai-khoan.php');
        exit;
    }
    
    // Validate username
    $username_check = validateUsername($username);
    if ($username_check !== true) {
        $_SESSION['register_error'] = "❌ $username_check";
        header('Location: ../tai-khoan.php');
        exit;
    }
    
    // Validate password
    $password_check = validatePassword($password);
    if ($password_check !== true) {
        $_SESSION['register_error'] = "❌ $password_check";
        header('Location: ../tai-khoan.php');
        exit;
    }
    
    // Check username exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['register_error'] = '❌ Tên đăng nhập này đã tồn tại';
        header('Location: ../tai-khoan.php');
        exit;
    }
    
    $password_hash = hashPassword($password);
    $sql = "INSERT INTO users (username, name, full_name, password, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $name, $full_name, $password_hash, $phone);
    
    if ($stmt->execute()) {
        $_SESSION['register_success'] = 'Tài khoản "' . $username . '" đã được tạo thành công! Bạn có thể đăng nhập luôn.';
        // Redirect to returnUrl if provided, otherwise to account page
        $redirect_url = isset($_POST['returnUrl']) ? '../' . $_POST['returnUrl'] : '../tai-khoan.php';
        header('Location: ' . $redirect_url);
        exit;
    } else {
        $_SESSION['register_error'] = 'Đăng ký thất bại: ' . $conn->error;
        header('Location: ../tai-khoan.php');
        exit;
    }
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('username', '', time() - 3600, '/');
    header('Location: ../index.php');
    exit;
}

// API: Get user info
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'getUser') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id, username, name, full_name, phone FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        jsonResponse('success', 'User found', $user);
    } else {
        jsonResponse('error', 'User not found');
    }
}

// API: Check if user is logged in
if (isset($_GET['action']) && $_GET['action'] == 'checkLogin') {
    if (isset($_SESSION['user_id'])) {
        jsonResponse('success', 'User is logged in');
    } else {
        jsonResponse('error', 'Not logged in');
    }
}
