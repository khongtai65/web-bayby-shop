<?php
require_once '../config.php';

// GET: Lấy tất cả sản phẩm hoặc lọc theo danh mục
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        // Validate ID if provided
        if ($id && !validateInt($id)) {
            jsonResponse('error', 'Invalid product ID', null, 400);
        }
        
        // Lấy 1 sản phẩm
        if ($id) {
            $sql = "SELECT id, name, price, description, category, stock, image_path FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed');
            }
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed');
            }
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            if ($product) {
                jsonResponse('success', 'Product found', $product, 200);
            } else {
                jsonResponse('error', 'Product not found', null, 404);
            }
        }
        
        // Lấy tất cả sản phẩm hoặc lọc theo danh mục
        if ($category) {
            $sql = "SELECT id, name, price, description, category, stock, image_path FROM products WHERE category = ? LIMIT 1000";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed');
            }
            $stmt->bind_param("s", $category);
        } else {
            $sql = "SELECT id, name, price, description, category, stock, image_path FROM products ORDER BY id ASC LIMIT 1000";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed');
            }
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed');
        }
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        jsonResponse('success', 'Products retrieved', $products, 200);
    } catch (Exception $e) {
        error_log("Product GET error: " . $e->getMessage());
        jsonResponse('error', 'Failed to retrieve products', null, 500);
    }
}

// POST: Thêm sản phẩm mới (Admin only)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Check authentication and rate limiting
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            jsonResponse('error', 'Unauthorized', null, 403);
        }
        
        if (!checkRateLimit($_SESSION['user_id'], 50)) {
            jsonResponse('error', 'Too many requests', null, 429);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        $description = $data['description'] ?? '';
        $category = $data['category'] ?? '';
        $stock = $data['stock'] ?? 100;
        
        // Validate input
        if (!$name || !is_numeric($price) || $price <= 0) {
            jsonResponse('error', 'Name and valid price required', null, 400);
        }
        
        if (!validateInt($stock) || $stock < 0) {
            jsonResponse('error', 'Valid stock required', null, 400);
        }
        
        $name = sanitizeInput($name);
        $description = sanitizeInput($description);
        $category = sanitizeInput($category);
        
        $sql = "INSERT INTO products (name, price, description, category, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }
        $stmt->bind_param("sissi", $name, $price, $description, $category, $stock);
        
        if ($stmt->execute()) {
            jsonResponse('success', 'Product added', ['id' => $conn->insert_id], 201);
        } else {
            throw new Exception('Insert failed');
        }
    } catch (Exception $e) {
        error_log("Product POST error: " . $e->getMessage());
        jsonResponse('error', 'Failed to add product', null, 500);
    }
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
