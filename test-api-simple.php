<?php
require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 API Test Page</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>1. Database Connection</h2>";
if ($conn && !$conn->connect_error) {
    echo "✅ Database connected successfully<br>";
    echo "Database: " . DB_NAME . " @ " . DB_HOST . "<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test 2: Check Products Table
echo "<h2>2. Products Table</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Total products: " . $row['count'] . "<br>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

// Test 3: Get All Products
echo "<h2>3. Get All Products (Raw Query)</h2>";
$result = $conn->query("SELECT id, name, price, discount_percent, image FROM products LIMIT 5");
if ($result) {
    echo "✅ Query successful. Found " . $result->num_rows . " rows<br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Discount</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['price'] . "</td>";
        echo "<td>" . $row['discount_percent'] . "%</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Query error: " . $conn->error . "<br>";
}

// Test 4: Test API JSON Response
echo "<h2>4. API JSON Response</h2>";
echo "<p>Testing API endpoint: <strong>/api-dieu-khien/san-pham.php</strong></p>";
echo "<pre>";
$ch = curl_init('http://localhost/baby-shop/api-dieu-khien/san-pham.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response) {
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Response (first 500 chars):\n";
    echo htmlspecialchars(substr($response, 0, 500));
    
    $json = json_decode($response, true);
    if ($json) {
        echo "\n\n✅ Valid JSON response\n";
        if (isset($json['status'])) {
            echo "Status: " . $json['status'] . "\n";
        }
        if (isset($json['data'])) {
            echo "Data items: " . count($json['data']) . "\n";
        }
    } else {
        echo "\n\n❌ Invalid JSON response\n";
    }
} else {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
}
echo "</pre>";

// Test 5: Test Click & Product Detail
echo "<h2>5. Test Click Functionality</h2>";
echo "<p>When you click a product card, it should navigate to:</p>";
echo "<code>chi-tiet-san-pham-v2.php?id=PRODUCT_ID</code><br>";
echo "<button onclick=\"window.location.href='chi-tiet-san-pham-v2.php?id=1'\">Test Click to Product #1</button>";

echo "<hr>";
echo "<p><a href='cua-hang.php'>← Back to Shop</a></p>";
?>
