<?php
/**
 * Auto-Migration & Setup Script
 * Truy cập: http://localhost/baby-shop/auto-setup.php
 */
header('Content-Type: text/html; charset=UTF-8');
require_once 'config.php';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Auto Setup & Migration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        h2 {
            color: #333;
            margin: 20px 0 15px;
            font-size: 1.3em;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        table tr:hover {
            background: #f5f5f5;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 1em;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Auto Setup & Migration</h1>
            <p>Kiểm tra và cập nhật cơ sở dữ liệu tự động</p>
        </div>
        
        <div class="content">
            <?php
            $errors = [];
            $warnings = [];
            $success_count = 0;
            
            try {
                // === 1. Check & Add GENDER column ===
                echo '<div class="section">';
                echo '<h2>✓ 1. Kiểm tra cột GENDER</h2>';
                
                $check = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='gender'";
                $result = $conn->query($check);
                
                if ($result->num_rows == 0) {
                    echo '<div class="warning">⚠️ Cột `gender` chưa tồn tại, đang thêm...</div>';
                    if ($conn->query("ALTER TABLE `products` ADD COLUMN `gender` VARCHAR(50) DEFAULT 'unisex'")) {
                        echo '<div class="success">✅ Cột `gender` đã được thêm thành công!</div>';
                        $success_count++;
                    } else {
                        $errors[] = 'Thêm cột gender thất bại: ' . $conn->error;
                    }
                } else {
                    echo '<div class="success">✅ Cột `gender` đã tồn tại</div>';
                    $success_count++;
                }
                echo '</div>';
                
                // === 2. Check & Add Gender INDEX ===
                echo '<div class="section">';
                echo '<h2>✓ 2. Kiểm tra INDEX gender</h2>';
                
                $check_idx = "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND INDEX_NAME='idx_gender'";
                $result_idx = $conn->query($check_idx);
                
                if ($result_idx->num_rows == 0) {
                    echo '<div class="warning">⚠️ INDEX `idx_gender` chưa tồn tại, đang thêm...</div>';
                    if ($conn->query("ALTER TABLE `products` ADD INDEX `idx_gender` (`gender`)")) {
                        echo '<div class="success">✅ INDEX `idx_gender` đã được thêm!</div>';
                        $success_count++;
                    } else {
                        $warnings[] = 'Thêm index gender không thành công (có thể đã tồn tại)';
                    }
                } else {
                    echo '<div class="success">✅ INDEX `idx_gender` đã tồn tại</div>';
                    $success_count++;
                }
                echo '</div>';
                
                // === 3. Check Products Table Structure ===
                echo '<div class="section">';
                echo '<h2>✓ 3. Cấu trúc bảng Products</h2>';
                
                $columns = $conn->query("SHOW COLUMNS FROM products");
                echo '<table>';
                echo '<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
                while ($col = $columns->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td><code>' . $col['Field'] . '</code></td>';
                    echo '<td>' . $col['Type'] . '</td>';
                    echo '<td>' . $col['Null'] . '</td>';
                    echo '<td>' . $col['Key'] . '</td>';
                    echo '<td>' . ($col['Default'] ?? 'N/A') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';
                
                // === 4. Count Products ===
                echo '<div class="section">';
                echo '<h2>✓ 4. Thống kê Sản phẩm</h2>';
                
                $count_total = $conn->query("SELECT COUNT(*) as cnt FROM products")->fetch_assoc()['cnt'];
                $count_by_gender = $conn->query("SELECT gender, COUNT(*) as cnt FROM products GROUP BY gender")->fetch_all(MYSQLI_ASSOC);
                $count_by_category = $conn->query("SELECT category, COUNT(*) as cnt FROM products GROUP BY category")->fetch_all(MYSQLI_ASSOC);
                
                echo "<p><strong>📦 Tổng sản phẩm:</strong> <span style='font-size: 1.5em; color: #667eea;'>$count_total</span></p>";
                
                if (count($count_by_gender) > 0) {
                    echo '<h3>👶 Theo Giới Tính:</h3>';
                    echo '<table>';
                    echo '<tr><th>Gender</th><th>Quantity</th></tr>';
                    foreach ($count_by_gender as $row) {
                        $icons = [
                            'be-trai' => '👦',
                            'be-gai' => '👧',
                            'unisex' => '👶'
                        ];
                        $icon = $icons[$row['gender']] ?? '📦';
                        echo '<tr><td>' . $icon . ' ' . ucfirst($row['gender']) . '</td><td><strong>' . $row['cnt'] . '</strong></td></tr>';
                    }
                    echo '</table>';
                }
                
                if (count($count_by_category) > 0) {
                    echo '<h3>📂 Theo Danh Mục:</h3>';
                    echo '<table>';
                    echo '<tr><th>Category</th><th>Quantity</th></tr>';
                    foreach ($count_by_category as $row) {
                        echo '<tr><td>' . ($row['category'] ?? 'N/A') . '</td><td><strong>' . $row['cnt'] . '</strong></td></tr>';
                    }
                    echo '</table>';
                }
                
                if ($count_total > 0) {
                    echo '<div class="success">✅ Database có ' . $count_total . ' sản phẩm</div>';
                } else {
                    echo '<div class="warning">⚠️ Database trống, cần thêm sản phẩm</div>';
                }
                echo '</div>';
                
                // === 5. Latest Products ===
                echo '<div class="section">';
                echo '<h2>✓ 5. Sản phẩm mới nhất</h2>';
                
                $latest = $conn->query("SELECT id, name, category, gender, price, discount_percent, stock, created_at FROM products ORDER BY created_at DESC LIMIT 5");
                
                if ($latest->num_rows > 0) {
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Name</th><th>Category</th><th>Gender</th><th>Price</th><th>Discount</th><th>Stock</th><th>Created</th></tr>';
                    while ($prod = $latest->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $prod['id'] . '</td>';
                        echo '<td>' . substr($prod['name'], 0, 25) . '...</td>';
                        echo '<td>' . $prod['category'] . '</td>';
                        echo '<td>' . ($prod['gender'] ?? 'N/A') . '</td>';
                        echo '<td>₫' . number_format($prod['price']) . '</td>';
                        echo '<td>' . $prod['discount_percent'] . '%</td>';
                        echo '<td>' . $prod['stock'] . '</td>';
                        echo '<td>' . date('d/m/Y H:i', strtotime($prod['created_at'])) . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p style="color: #999;">Chưa có sản phẩm nào</p>';
                }
                echo '</div>';
                
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            
            // === Display Errors ===
            if (!empty($errors)) {
                echo '<div class="section">';
                echo '<h2>❌ Lỗi</h2>';
                foreach ($errors as $error) {
                    echo '<div class="error">' . htmlspecialchars($error) . '</div>';
                }
                echo '</div>';
            }
            
            // === Display Warnings ===
            if (!empty($warnings)) {
                echo '<div class="section">';
                echo '<h2>⚠️ Cảnh báo</h2>';
                foreach ($warnings as $warning) {
                    echo '<div class="warning">' . htmlspecialchars($warning) . '</div>';
                }
                echo '</div>';
            }
            
            // === Summary ===
            echo '<div class="section" style="background: #d1ecf1; border-left-color: #17a2b8;">';
            echo '<h2>📊 Tóm tắt</h2>';
            echo '<p>✅ Hoàn thành: <strong>' . $success_count . '/3</strong> bước</p>';
            echo '<p style="margin-top: 15px; color: #666; font-size: 0.95em;">Nếu database vẫn trống, hãy:</p>';
            echo '<ol style="margin-left: 20px; color: #666;">';
            echo '<li>Vào <strong>Quản Lý Sản Phẩm</strong></li>';
            echo '<li>Thêm sản phẩm mới (chọn Giới tính: Bé Trai/Bé Gái/Unisex)</li>';
            echo '<li>Upload ảnh PNG/JPG</li>';
            echo '<li>Submit form</li>';
            echo '<li>Quay lại trang này để kiểm tra</li>';
            echo '</ol>';
            echo '</div>';
            ?>
        </div>
        
        <div class="footer">
            <div class="btn-group" style="justify-content: center;">
                <button class="btn-primary" onclick="window.location.href='quan-ly-san-pham.php'">⚙️ Quản Lý Sản Phẩm</button>
                <button class="btn-primary" onclick="window.location.href='cua-hang.php'">🛒 Cửa Hàng</button>
                <button class="btn-primary" onclick="window.location.href='test-api.html'">🧪 Test API</button>
                <button class="btn-secondary" onclick="location.reload()">🔄 Làm mới</button>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
