<?php
session_start();
require_once 'config.php';

$message = '';
$error = '';

// Kiểm tra nếu đã có admin rồi
$checkAdmin = "SELECT COUNT(*) as count FROM users WHERE role='admin'";
$result = $conn->query($checkAdmin);
$adminCount = $result->fetch_assoc()['count'];

// Xử lý form tạo admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_admin') {
    try {
        $username = sanitizeInput(trim($_POST['admin_username'] ?? ''));
        $email = sanitizeInput(trim($_POST['admin_email'] ?? ''));
        $name = sanitizeInput(trim($_POST['admin_name'] ?? ''));
        $full_name = sanitizeInput(trim($_POST['admin_full_name'] ?? ''));
        $phone = sanitizeInput(trim($_POST['admin_phone'] ?? ''));
        $password = $_POST['admin_password'] ?? '';
        
        // Validate
        if (!$username || strlen($username) < 6) {
            throw new Exception('Tên đăng nhập phải tối thiểu 6 ký tự');
        }
        
        if (!$password || strlen($password) < 6) {
            throw new Exception('Mật khẩu phải tối thiểu 6 ký tự');
        }
        
        if (!$name) {
            throw new Exception('Tên gọi không được để trống');
        }
        
        if (!$full_name) {
            throw new Exception('Tên đầy đủ không được để trống');
        }
        
        // Check if username exists
        $checkUsername = "SELECT id FROM users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($checkUsername);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('Tên đăng nhập đã tồn tại!');
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Insert admin user
        $sql = "INSERT INTO users (username, email, name, full_name, phone, password, role) 
                VALUES (?, ?, ?, ?, ?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $username, $email, $name, $full_name, $phone, $passwordHash);
        if ($stmt->execute()) {
            $message = '✅ Tài khoản admin "' . $username . '" đã được tạo thành công!';
            $_POST = []; // Clear form
        } else {
            throw new Exception('Lỗi khi tạo admin: ' . $conn->error);
        }
    } catch (Exception $e) {
        $error = '❌ ' . $e->getMessage();
    }
}

// Xử lý xoá admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_admin') {
    try {
        $admin_id = intval($_POST['admin_id'] ?? 0);
        
        if ($admin_id <= 0) {
            throw new Exception('ID admin không hợp lệ');
        }
        
        $sql = "DELETE FROM users WHERE id = ? AND role = 'admin'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $message = '✅ Đã xoá admin thành công';
        } else {
            throw new Exception('Không thể xoá admin này');
        }
    } catch (Exception $e) {
        $error = '❌ ' . $e->getMessage();
    }
}

// Lấy danh sách admin
$admins = [];
$sql = "SELECT id, username, email, full_name, phone FROM users WHERE role='admin' ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    $admins = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Admin - Shop Mẹ và Bé</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <link rel="stylesheet" href="css-kieu-dang/mobile.css" media="(max-width: 768px)">
    <style>
        body {
            background-color: #f0f0f0;
        }

        .admin-management-container {
            max-width: 1000px;
            margin: 100px auto 20px;
            padding: 20px;
        }

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .admin-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }

        .message {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .form-section h2 {
            color: #333;
            margin-top: 0;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        /* Tbl Admin */
        .admins-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .admins-table thead {
            background: #34495e;
            color: white;
        }

        .admins-table th,
        .admins-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        .admins-table tbody tr:hover {
            background: #f8f9fa;
        }

        .admins-table .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .admins-table .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .admins-table .delete-btn:hover {
            background: #c0392b;
        }

        @media (max-width: 768px) {
            .admin-management-container {
                margin-top: 80px;
                padding: 10px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .admin-header {
                padding: 20px;
            }

            .form-section {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-management-container">
        <div class="admin-header">
            <h1>⚙️ Quản Lý Tài Khoản Admin</h1>
            <p>Tạo và quản lý tài khoản quản trị viên</p>
            <a href="trang-chu.php" style="color: white; text-decoration: none; display: inline-block; margin-top: 15px; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;">← Quay lại trang chủ</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Form Tạo Admin -->
        <div class="form-section">
            <h2>➕ Tạo Tài Khoản Admin Mới</h2>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="admin_username">Tên đăng nhập *</label>
                        <input type="text" id="admin_username" name="admin_username" required 
                               placeholder="admin123" minlength="6" value="<?php echo htmlspecialchars($_POST['admin_username'] ?? ''); ?>">
                        <small style="color: #666;">Tối thiểu 6 ký tự</small>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Mật khẩu *</label>
                        <input type="password" id="admin_password" name="admin_password" required 
                               placeholder="••••••" minlength="6" value="<?php echo htmlspecialchars($_POST['admin_password'] ?? ''); ?>">
                        <small style="color: #666;">Tối thiểu 6 ký tự</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="admin_name">Tên gọi *</label>
                        <input type="text" id="admin_name" name="admin_name" required 
                               placeholder="Admin" value="<?php echo htmlspecialchars($_POST['admin_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="admin_full_name">Tên đầy đủ *</label>
                        <input type="text" id="admin_full_name" name="admin_full_name" required 
                               placeholder="Người Quản Lý" value="<?php echo htmlspecialchars($_POST['admin_full_name'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="admin_email">Email</label>
                        <input type="email" id="admin_email" name="admin_email" 
                               placeholder="admin@shopme.vn" value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="admin_phone">Số điện thoại</label>
                        <input type="tel" id="admin_phone" name="admin_phone" 
                               placeholder="0866021711" value="<?php echo htmlspecialchars($_POST['admin_phone'] ?? ''); ?>">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <input type="hidden" name="action" value="create_admin">
                    <button type="submit" class="btn btn-primary">✅ Tạo Admin</button>
                </div>
            </form>
        </div>

        <!-- Danh Sách Admin -->
        <div class="form-section">
            <h2>👥 Danh Sách Admin (<?php echo count($admins); ?>)</h2>

            <?php if (count($admins) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="admins-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên Đăng Nhập</th>
                                <th>Tên Đầy Đủ</th>
                                <th>Email</th>
                                <th>Số ĐT</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?php echo $admin['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($admin['username']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xoá admin này?');">
                                            <input type="hidden" name="action" value="delete_admin">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <button type="submit" class="action-btn delete-btn">🗑️ Xoá</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #666; text-align: center; padding: 20px;">📭 Chưa có admin nào</p>
            <?php endif; ?>
        </div>

        <!-- Hướng Dẫn -->
        <div class="form-section" style="background: #f0f7ff; border-left: 4px solid #667eea;">
            <h2>📖 Hướng Dẫn</h2>
            <ul style="color: #333; line-height: 1.8;">
                <li>✅ Nhập tên đăng nhập, mật khẩu và thông tin admin</li>
                <li>✅ Click nút "Tạo Admin"</li>
                <li>✅ Admin sẽ xuất hiện trong danh sách</li>
                <li>✅ Sử dụng tài khoản đó để đăng nhập vào trang tai-khoan.php</li>
                <li>✅ Sau khi đăng nhập, admin sẽ thấy nút "Dashboard Quản Lý"</li>
            </ul>
        </div>
    </div>

    <script src="js-kiem-soat/header-scroll.js"></script>
</body>
</html>
