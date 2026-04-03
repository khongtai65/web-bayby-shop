<?php
session_start();
require_once 'config.php';

// Bảo vệ trang setup - chỉ chạy 1 lần
if (isset($_POST['setup']) && $_POST['setup'] === 'confirm') {
    try {
        // Bước 1: Thêm role column nếu chưa có
        $checkColumn = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='role'";
        $result = $conn->query($checkColumn);
        
        if ($result->num_rows === 0) {
            // Thêm role column
            $sql = "ALTER TABLE `users` ADD COLUMN `role` VARCHAR(50) DEFAULT 'user' AFTER `password`";
            if ($conn->query($sql)) {
                echo "✅ Đã thêm cột role<br>";
            } else {
                echo "⚠️ Cột role có thể đã tồn tại<br>";
            }
        } else {
            echo "✅ Cột role đã tồn tại<br>";
        }
        
        // Bước 2: Tạo tài khoản admin
        // Password: admin123 (hashed with bcrypt cost 12)
        $adminPassword = '$2y$12$Qa.qQsgTJy5y6P//9WS52OZtqUbqAu/1bPMqnZvZ89vKXZLcMBOUm';
        
        $checkAdmin = "SELECT id FROM users WHERE username='admin' LIMIT 1";
        $result = $conn->query($checkAdmin);
        
        if ($result->num_rows === 0) {
            $sql = "INSERT INTO `users` (username, email, name, full_name, phone, password, role) 
                    VALUES ('admin', 'admin@shopme.vn', 'Admin', 'Người Quản Lý', '0866021711', '$adminPassword', 'admin')";
            
            if ($conn->query($sql)) {
                echo "✅ Đã tạo tài khoản admin<br>";
                echo "<strong style='color: green;'>👤 Admin đã được tạo:</strong><br>";
                echo "📧 Tên đăng nhập: <strong>admin</strong><br>";
                echo "🔐 Mật khẩu: <strong>admin123</strong><br>";
                echo "⚠️ <strong style='color: red;'>VUI LÒNG ĐỔI MẬT KHẨU NGAY KHI ĐĂNG NHẬP LẦN ĐẦU</strong><br><br>";
            } else {
                echo "❌ Lỗi tạo admin: " . $conn->error . "<br>";
            }
        } else {
            echo "✅ Tài khoản admin đã tồn tại<br>";
        }
        
        echo "<hr>";
        echo "<h2>✅ Setup hoàn tất!</h2>";
        echo "<p>Bạn có thể đăng nhập bằng tài khoản admin và truy cập quản lý sản phẩm.</p>";
        echo "<a href='tai-khoan.php' style='background: #E74C3C; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block;'>→ Đến trang đăng nhập</a>";
        
    } catch (Exception $e) {
        echo "❌ Lỗi: " . $e->getMessage();
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin - Shop Mẹ và Bé</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f0f0;
            padding: 20px;
            margin: 0;
        }

        .setup-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #E74C3C;
            text-align: center;
            margin-bottom: 20px;
        }

        .step {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #E74C3C;
            border-radius: 4px;
        }

        .step h3 {
            color: #333;
            margin-top: 0;
        }

        .step p {
            color: #666;
            margin: 10px 0;
        }

        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .btn-setup {
            background: #E74C3C;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-setup:hover {
            background: #C0392B;
        }

        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>⚙️ Thiết lập Tài Khoản Admin</h1>

        <div class="step">
            <h3>📋 Bước 1: Chuẩn bị</h3>
            <p>Hệ thống sẽ tạo:</p>
            <ul>
                <li>✅ Cột <code>role</code> trong bảng users (nếu chưa có)</li>
                <li>✅ Tài khoản admin với tên: <code>admin</code></li>
                <li>✅ Mật khẩu mặc định: <code>admin123</code></li>
            </ul>
        </div>

        <div class="warning">
            <strong>⚠️ CẢNH BÁO BẢOMẬT:</strong><br>
            Sau khi setup, vui lòng <strong>ĐỔI MẬT KHẨU NGAY LẬP TỨC</strong> trong cài đặt tài khoản để đảm bảo an toàn.
        </div>

        <div class="step">
            <h3>🚀 Bước 2: Thực thi Setup</h3>
            <p>Nhấn nút bên dưới để tạo tài khoản admin:</p>
            <form method="POST">
                <input type="hidden" name="setup" value="confirm">
                <button type="submit" class="btn-setup">✅ Thực thi Setup</button>
            </form>
        </div>

        <div class="step">
            <h3>✨ Sau khi hoàn tất</h3>
            <p>1. Đăng nhập bằng tài khoản admin</p>
            <p>2. Truy cập: <strong>/quan-ly-san-pham.php</strong></p>
            <p>3. Quản lý sản phẩm, tồn kho, thông tin...</p>
        </div>
    </div>
</body>
</html>
