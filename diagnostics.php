<?php
/**
 * Database Diagnostics & Migration
 */
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration & Diagnostics</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #E74C3C;
            padding-bottom: 10px;
        }
        h2 {
            color: #E74C3C;
            margin-top: 20px;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #34495e;
            color: white;
        }
        table tr:hover {
            background: #f5f5f5;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #E74C3C;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-back:hover {
            background: #C0392B;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Migration & Diagnostics</h1>
<?php
require_once 'config.php';

// === MIGRATION ===
echo "<h2>📝 Migration Status</h2>";

try {
    // Check gender column
    $check = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='products' AND COLUMN_NAME='gender'";
    $result = $conn->query($check);
    
    if ($result->num_rows == 0) {
        echo "<div class='status warning'>⚠️ Adding missing 'gender' column...</div>";
        $conn->query("ALTER TABLE `products` ADD COLUMN `gender` VARCHAR(50) DEFAULT 'unisex'");
        echo "<div class='status success'>✅ Column 'gender' added successfully!</div>";
    } else {
        echo "<div class='status success'>✅ Column 'gender' already exists</div>";
    }
    
    // Check gender index
    $check_idx = "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME='products' AND INDEX_NAME='idx_gender'";
    $result_idx = $conn->query($check_idx);
    
    if ($result_idx->num_rows == 0) {
        echo "<div class='status warning'>⚠️ Adding index 'idx_gender'...</div>";
        $conn->query("ALTER TABLE `products` ADD INDEX `idx_gender` (`gender`)");
        echo "<div class='status success'>✅ Index 'idx_gender' added!</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='status error'>❌ Migration Error: " . $e->getMessage() . "</div>";
}

// === DIAGNOSTICS ===
echo "<h2>🔍 Database Diagnostics</h2>";

// Products by category
echo "<h3>📦 Products by Category</h3>";
$sql = "SELECT category, COUNT(*) as count 
        FROM products 
        GROUP BY category 
        ORDER BY category";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Category</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['category']}</td>";
        echo "<td>{$row['count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='status error'>❌ No products found!</div>";
}

// Products by gender
echo "<h3>👶 Products by Gender</h3>";
$sql = "SELECT gender, COUNT(*) as count 
        FROM products 
        GROUP BY gender 
        ORDER BY gender";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Gender</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['gender']}</td>";
        echo "<td>{$row['count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Latest products
echo "<h3>🆕 Latest Products (Top 10)</h3>";
$sql = "SELECT id, name, category, gender, price, discount_percent, stock, created_at 
        FROM products 
        ORDER BY created_at DESC 
        LIMIT 10";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Gender</th>
        <th>Price</th>
        <th>Discount</th>
        <th>Stock</th>
        <th>Created</th>
    </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . substr($row['name'], 0, 25) . "...</td>";
        echo "<td>{$row['category']}</td>";
        echo "<td>{$row['gender']}</td>";
        echo "<td>₫" . number_format($row['price']) . "</td>";
        echo "<td>{$row['discount_percent']}%</td>";
        echo "<td><strong>{$row['stock']}</strong></td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='status warning'>⚠️ No products found in database</div>";
}

// API Test
echo "<h2>🧪 API Test</h2>";
echo "<p>Testing API endpoints:</p>";

// Test GET all products
$test_api = "http://localhost/baby-shop/api-dieu-khien/san-pham.php";
echo "<h3>GET: All Products</h3>";
echo "<code>GET /api-dieu-khien/san-pham.php</code>";

// Test GET by category
echo "<h3>GET: Products by Category (0-3)</h3>";
echo "<code>GET /api-dieu-khien/san-pham.php?category=0-3</code>";

?>
        <a href="cua-hang.php" class="btn-back">🛒 Go to Shop</a>
        <a href="quan-ly-san-pham.php" class="btn-back">⚙️ Product Management</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>
