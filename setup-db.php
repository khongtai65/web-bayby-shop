<?php
require_once 'config.php';

// Check if migration is needed
$check_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='username'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Migration needed
    echo "🔄 Đang cập nhật cơ sở dữ liệu...\n\n";
    
    try {
        // Add columns
        $conn->query("ALTER TABLE `users` ADD COLUMN `username` VARCHAR(255) UNIQUE NOT NULL AFTER `id`");
        echo "✅ Thêm cột username\n";
        
        $conn->query("ALTER TABLE `users` ADD COLUMN `full_name` VARCHAR(255) AFTER `username`");
        echo "✅ Thêm cột full_name\n";
        
        // Create index
        $conn->query("CREATE UNIQUE INDEX idx_username ON users(username)");
        echo "✅ Tạo index cho username\n";
        
        // Update existing data if any
        $existing_sql = "SELECT COUNT(*) as cnt FROM users";
        $res = $conn->query($existing_sql);
        $row = $res->fetch_assoc();
        
        if ($row['cnt'] > 0) {
            $conn->query("UPDATE users SET username = CONCAT('user_', id) WHERE username IS NULL");
            $conn->query("UPDATE users SET full_name = name WHERE full_name IS NULL");
            echo "✅ Cập nhật dữ liệu hiện có\n";
        }
        
        echo "\n✅ Cập nhật cơ sở dữ liệu thành công!\n";
        echo "<br><a href='tai-khoan.php'>👉 Quay lại trang tài khoản</a>\n";
        
    } catch (Exception $e) {
        echo "❌ Lỗi: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ Cơ sở dữ liệu đã được cập nhật rồi!\n";
    echo "<br><a href='tai-khoan.php'>👉 Quay lại trang tài khoản</a>\n";
}
?>
