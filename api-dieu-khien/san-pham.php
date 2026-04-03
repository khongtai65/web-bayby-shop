<?php
ob_clean();
require_once '../config.php';

// GET: Lấy tất cả sản phẩm hoặc lọc theo danh mục
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    // Lấy 1 sản phẩm
    if ($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            jsonResponse('success', 'Product found', $product);
        } else {
            jsonResponse('error', 'Product not found');
        }
    }
    
    // Lấy tất cả sản phẩm hoặc lọc theo danh mục
    if ($category) {
        $sql = "SELECT * FROM products WHERE category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
    } else {
        $sql = "SELECT * FROM products ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    
    jsonResponse('success', 'Products retrieved', $products);
}

// POST: Thêm sản phẩm mới
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $name = $data['name'] ?? '';
    $price = $data['price'] ?? 0;
    $description = $data['description'] ?? '';
    $category = $data['category'] ?? '';
    $stock = $data['stock'] ?? 100;
    
    if (!$name || $price <= 0) {
        jsonResponse('error', 'Name and valid price required');
    }
    
    $sql = "INSERT INTO products (name, price, description, category, stock) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $name, $price, $description, $category, $stock);
    
    if ($stmt->execute()) {
        jsonResponse('success', 'Product added', ['id' => $conn->insert_id]);
    } else {
        jsonResponse('error', 'Failed to add product');
    }
}

// PUT: Cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = $data['id'] ?? 0;
    $name = $data['name'] ?? '';
    $price = $data['price'] ?? 0;
    $description = $data['description'] ?? '';
    $stock = $data['stock'] ?? 100;
    
    if ($id <= 0) {
        jsonResponse('error', 'Product ID required');
    }
    
    $sql = "UPDATE products SET name = ?, price = ?, description = ?, stock = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", $name, $price, $description, $stock, $id);
    
    if ($stmt->execute()) {
        jsonResponse('success', 'Product updated');
    } else {
        jsonResponse('error', 'Failed to update product');
    }
}

// DELETE: Xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? 0;
    
    if ($id <= 0) {
        jsonResponse('error', 'Product ID required');
    }
    
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse('success', 'Product deleted');
    } else {
        jsonResponse('error', 'Failed to delete product');
    }
}
?>
