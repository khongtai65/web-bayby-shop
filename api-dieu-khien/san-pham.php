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
            $sql = "SELECT id, name, price, discount_percent, description, category, stock, image FROM products WHERE id = ?";
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
            $sql = "SELECT id, name, price, discount_percent, description, category, stock, image FROM products WHERE category = ? LIMIT 1000";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed');
            }
            $stmt->bind_param("s", $category);
        } else {
            $sql = "SELECT id, name, price, discount_percent, description, category, stock, image FROM products ORDER BY id ASC LIMIT 1000";
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
        
        // Lấy dữ liệu từ $_POST
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';
        $stock = $_POST['stock'] ?? 100;
        $discount_percent = $_POST['discount_percent'] ?? 0;
        
        // Validate input
        if (!$name || !is_numeric($price) || $price <= 0) {
            jsonResponse('error', 'Name and valid price required', null, 400);
        }
        
        if (!validateInt($stock) || $stock < 0) {
            jsonResponse('error', 'Valid stock required', null, 400);
        }
        
        // Validate discount
        if (!is_numeric($discount_percent) || $discount_percent < 0 || $discount_percent > 100) {
            jsonResponse('error', 'Discount must be between 0 and 100', null, 400);
        }
        
        $name = sanitizeInput($name);
        $description = sanitizeInput($description);
        $category = sanitizeInput($category);
        $discount_percent = floatval($discount_percent);
        
        // Xử lý upload ảnh
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = basename($file['name']);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($ext, $allowed)) {
                jsonResponse('error', 'Only JPG and PNG files are allowed', null, 400);
            }
            
            // Create uploads directory if not exists
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $unique_name = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $unique_name;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_path = 'uploads/' . $unique_name;
            } else {
                jsonResponse('error', 'Failed to upload image', null, 500);
            }
        } else {
            jsonResponse('error', 'Image file is required', null, 400);
        }
        
        $sql = "INSERT INTO products (name, price, discount_percent, description, category, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }
        $stmt->bind_param("sddssss", $name, $price, $discount_percent, $description, $category, $stock, $image_path);
        
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

// PUT: Cập nhật sản phẩm (Admin only)
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    try {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            jsonResponse('error', 'Unauthorized', null, 403);
        }
        
        if (!checkRateLimit($_SESSION['user_id'], 50)) {
            jsonResponse('error', 'Too many requests', null, 429);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? 0;
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        $description = $data['description'] ?? '';
        $stock = $data['stock'] ?? 100;
        $discount_percent = $data['discount_percent'] ?? 0;
        
        if (!validateInt($id) || $id <= 0) {
            jsonResponse('error', 'Valid product ID required', null, 400);
        }
        
        if (!$name || !is_numeric($price) || $price <= 0) {
            jsonResponse('error', 'Name and valid price required', null, 400);
        }
        
        if (!is_numeric($discount_percent) || $discount_percent < 0 || $discount_percent > 100) {
            jsonResponse('error', 'Discount must be between 0 and 100', null, 400);
        }
        
        $name = sanitizeInput($name);
        $description = sanitizeInput($description);
        
        $sql = "UPDATE products SET name = ?, price = ?, discount_percent = ?, description = ?, stock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidsii", $name, $price, $discount_percent, $description, $stock, $id);
        
        if ($stmt->execute()) {
            jsonResponse('success', 'Product updated', null, 200);
        } else {
            throw new Exception('Update failed');
        }
    } catch (Exception $e) {
        error_log("Product PUT error: " . $e->getMessage());
        jsonResponse('error', 'Failed to update product', null, 500);
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
