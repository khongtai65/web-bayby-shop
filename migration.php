<?php
/**
 * Database Migration Script
 * Chạy các migrations cần thiết để cập nhật database schema
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "=== DATABASE MIGRATION ===\n";

try {
    // Kiểm tra và thêm cột gender vào products
    echo "\n1. Checking for 'gender' column in products table...\n";
    
    $sql_check = "SHOW COLUMNS FROM products LIKE 'gender'";
    $result = $conn->query($sql_check);
    
    if ($result && $result->num_rows === 0) {
        echo "   ✓ Column 'gender' not found. Adding...\n";
        $sql_add = "ALTER TABLE `products` ADD COLUMN `gender` VARCHAR(50) DEFAULT 'unisex'";
        if ($conn->query($sql_add)) {
            echo "   ✓ Column 'gender' added successfully!\n";
        } else {
            echo "   ✗ Failed to add 'gender' column: " . $conn->error . "\n";
        }
    } else if ($result && $result->num_rows > 0) {
        echo "   ✓ Column 'gender' already exists.\n";
    }
    
    // Kiểm tra và thêm index cho gender
    echo "\n2. Checking for 'idx_gender' index...\n";
    
    $sql_check_idx = "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME='products' AND INDEX_NAME='idx_gender'";
    $result_idx = $conn->query($sql_check_idx);
    
    if ($result_idx && $result_idx->num_rows === 0) {
        echo "   ✓ Index 'idx_gender' not found. Adding...\n";
        $sql_add_idx = "ALTER TABLE `products` ADD INDEX `idx_gender` (`gender`)";
        if ($conn->query($sql_add_idx)) {
            echo "   ✓ Index 'idx_gender' added successfully!\n";
        } else {
            echo "   ✗ Failed to add 'idx_gender' index: " . $conn->error . "\n";
        }
    } else if ($result_idx && $result_idx->num_rows > 0) {
        echo "   ✓ Index 'idx_gender' already exists.\n";
    }
    
    // Hiển thị thông tin products
    echo "\n3. Displaying products info:\n";
    echo "   " . str_repeat("=", 80) . "\n";
    
    $sql_display = "SELECT id, name, category, gender, price, discount_percent, stock FROM products ORDER BY id DESC LIMIT 10";
    $result_display = $conn->query($sql_display);
    
    if ($result_display && $result_display->num_rows > 0) {
        printf("   %-3s %-30s %-15s %-15s %-12s %-10s %-8s\n", 
            "ID", "Name", "Category", "Gender", "Price", "Discount", "Stock");
        echo "   " . str_repeat("-", 80) . "\n";
        
        while ($row = $result_display->fetch_assoc()) {
            printf("   %-3s %-30s %-15s %-15s %-12s %-10s %-8s\n",
                $row['id'],
                substr($row['name'], 0, 28),
                $row['category'],
                $row['gender'],
                number_format($row['price']),
                $row['discount_percent'] . '%',
                $row['stock']
            );
        }
        echo "   " . str_repeat("=", 80) . "\n";
    } else {
        echo "   ℹ️  No products found in database.\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
