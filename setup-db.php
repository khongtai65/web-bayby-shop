<?php
require_once 'config.php';

echo "=== DATABASE MIGRATION ===\n\n";

// Kiểm tra nếu cần cập nhật
$needs_migration = false;

// Kiểm tra cột username
$check_username = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='username'";
$result = $conn->query($check_username);
if ($result->num_rows == 0) {
    $needs_migration = true;
}

// Kiểm tra cột gender trong products
$check_gender = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='products' AND COLUMN_NAME='gender'";
$result = $conn->query($check_gender);
if ($result->num_rows == 0) {
    $needs_migration = true;
}

if ($needs_migration) {
    echo "🔄 Đang cập nhật cơ sở dữ liệu...\n\n";
    
    try {
        // === ADD USERNAME COLUMNS ===
        $check = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='username'";
        if ($conn->query($check)->num_rows == 0) {
            echo "1️⃣  Thêm cột username...\n";
            $conn->query("ALTER TABLE `users` ADD COLUMN `username` VARCHAR(255) UNIQUE NOT NULL");
            echo "   ✅ Thêm cột username\n";
            
            $conn->query("CREATE UNIQUE INDEX IF NOT EXISTS idx_username ON users(username)");
            echo "   ✅ Tạo index cho username\n";
        }
        
        // === ADD FULL_NAME COLUMN ===
        $check = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='full_name'";
        if ($conn->query($check)->num_rows == 0) {
            echo "2️⃣  Thêm cột full_name...\n";
            $conn->query("ALTER TABLE `users` ADD COLUMN `full_name` VARCHAR(255)");
            echo "   ✅ Thêm cột full_name\n";
        }
        
        // === ADD GENDER COLUMN ===
        $check = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='products' AND COLUMN_NAME='gender'";
        if ($conn->query($check)->num_rows == 0) {
            echo "3️⃣  Thêm cột gender cho products...\n";
            $conn->query("ALTER TABLE `products` ADD COLUMN `gender` VARCHAR(50) DEFAULT 'unisex'");
            echo "   ✅ Thêm cột gender\n";
            
            $check_idx = "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME='products' AND INDEX_NAME='idx_gender'";
            if ($conn->query($check_idx)->num_rows == 0) {
                $conn->query("ALTER TABLE `products` ADD INDEX `idx_gender` (`gender`)");
                echo "   ✅ Tạo index cho gender\n";
            }
        }
        
        // === UPDATE EXISTING DATA ===
        echo "4️⃣  Cập nhật dữ liệu hiện có...\n";
        $existing_sql = "SELECT COUNT(*) as cnt FROM users";
        $res = $conn->query($existing_sql);
        $row = $res->fetch_assoc();
        
        if ($row['cnt'] > 0) {
            $check_null = "SELECT COUNT(*) as cnt FROM users WHERE username IS NULL";
            $res_null = $conn->query($check_null);
            $row_null = $res_null->fetch_assoc();
            
            if ($row_null['cnt'] > 0) {
                $conn->query("UPDATE users SET username = CONCAT('user_', id) WHERE username IS NULL");
                echo "   ✅ Cập nhật username cho user hiện có\n";
            }
            
            $check_fullname = "SELECT COUNT(*) as cnt FROM users WHERE full_name IS NULL";
            $res_fullname = $conn->query($check_fullname);
            $row_fullname = $res_fullname->fetch_assoc();
            
            if ($row_fullname['cnt'] > 0) {
                $conn->query("UPDATE users SET full_name = name WHERE full_name IS NULL");
                echo "   ✅ Cập nhật full_name cho user hiện có\n";
            }
        }
        
        echo "\n✅ Cập nhật cơ sở dữ liệu thành công!\n";
        echo "\n📊 Thông tin sản phẩm:\n";
        echo str_repeat("-", 60) . "\n";
        
        $sql_info = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN gender='be-trai' THEN 1 ELSE 0 END) as be_trai,
            SUM(CASE WHEN gender='be-gai' THEN 1 ELSE 0 END) as be_gai,
            SUM(CASE WHEN gender='unisex' THEN 1 ELSE 0 END) as unisex
        FROM products";
        
        $res_info = $conn->query($sql_info);
        $info = $res_info->fetch_assoc();
        
        printf("   Tổng sản phẩm: %d\n", $info['total'] ?? 0);
        printf("   👦 Bé Trai: %d\n", $info['be_trai'] ?? 0);
        printf("   👧 Bé Gái: %d\n", $info['be_gai'] ?? 0);
        printf("   👶 Unisex: %d\n", $info['unisex'] ?? 0);
        
        echo "\n<meta charset='UTF-8'>\n";
        echo "<br><a href='tai-khoan.php'>👉 Quay lại trang tài khoản</a>\n";
        
    } catch (Exception $e) {
        echo "❌ Lỗi: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ Cơ sở dữ liệu đã được cập nhật rồi!\n";
    echo "\n<meta charset='UTF-8'>\n";
    echo "<br><a href='tai-khoan.php'>👉 Quay lại trang tài khoản</a>\n";
}

$conn->close();
?>
