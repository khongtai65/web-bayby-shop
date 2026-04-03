<?php
require_once '../config.php';

// GET: Lấy giỏ hàng của user
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        if (!isset($_SESSION['user_id'])) {
            jsonResponse('error', 'Not logged in', null, 401);
        }
        
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image_path 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
                ORDER BY c.id ASC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed');
        }
        $result = $stmt->get_result();
        $cart = $result->fetch_all(MYSQLI_ASSOC);
        
        jsonResponse('success', 'Cart retrieved', $cart, 200);
    } catch (Exception $e) {
        error_log("Cart GET error: " . $e->getMessage());
        jsonResponse('error', 'Failed to retrieve cart', null, 500);
    }
}

// POST: Thêm vào giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (!isset($_SESSION['user_id'])) {
            jsonResponse('error', 'Not logged in', null, 401);
        }
        
        if (!checkRateLimit($_SESSION['user_id'], 100)) {
            jsonResponse('error', 'Too many requests', null, 429);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION['user_id'];
        $product_id = $data['product_id'] ?? 0;
        $quantity = $data['quantity'] ?? 1;
        
        if (!validateInt($product_id) || $product_id <= 0) {
            jsonResponse('error', 'Invalid product ID', null, 400);
        }
        if (!validateInt($quantity) || $quantity <= 0 || $quantity > 1000) {
            jsonResponse('error', 'Invalid quantity', null, 400);
        }
        
        // Check if product already in cart
        $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception('Prepare failed');
        }
        $check_stmt->bind_param("ii", $user_id, $product_id);
        if (!$check_stmt->execute()) {
            throw new Exception('Check execute failed');
        }
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update quantity
            $row = $check_result->fetch_assoc();
            $new_qty = $row['quantity'] + $quantity;
            if ($new_qty > 10000) {
                jsonResponse('error', 'Quantity exceeds limit', null, 400);
            }
            $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                throw new Exception('Update prepare failed');
            }
            $update_stmt->bind_param("iii", $new_qty, $user_id, $product_id);
            if (!$update_stmt->execute()) {
                throw new Exception('Update execute failed');
            }
            jsonResponse('success', 'Cart updated', null, 200);
        } else {
            // Insert new
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                throw new Exception('Insert prepare failed');
            }
            $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
            if (!$insert_stmt->execute()) {
                throw new Exception('Insert execute failed');
            }
            jsonResponse('success', 'Added to cart', null, 201);
        }
    } catch (Exception $e) {
        error_log("Cart POST error: " . $e->getMessage());
        jsonResponse('error', 'Failed to update cart', null, 500);
    }
}

// DELETE: Xóa khỏi giỏ
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    try {
        if (!isset($_SESSION['user_id'])) {
            jsonResponse('error', 'Not logged in', null, 401);
        }
        
        if (!checkRateLimit($_SESSION['user_id'], 100)) {
            jsonResponse('error', 'Too many requests', null, 429);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION['user_id'];
        $product_id = $data['product_id'] ?? 0;
        
        if (!validateInt($product_id) || $product_id <= 0) {
            jsonResponse('error', 'Invalid product ID', null, 400);
        }
        
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }
        $stmt->bind_param("ii", $user_id, $product_id);
        
        if ($stmt->execute()) {
            jsonResponse('success', 'Removed from cart', null, 200);
        } else {
            throw new Exception('Delete execute failed');
        }
    } catch (Exception $e) {
        error_log("Cart DELETE error: " . $e->getMessage());
        jsonResponse('error', 'Failed to remove from cart', null, 500);
    }
}

// PUT: Cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    try {
        if (!isset($_SESSION['user_id'])) {
            jsonResponse('error', 'Not logged in', null, 401);
        }
        
        if (!checkRateLimit($_SESSION['user_id'], 100)) {
            jsonResponse('error', 'Too many requests', null, 429);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION['user_id'];
        $product_id = $data['product_id'] ?? 0;
        $quantity = $data['quantity'] ?? 0;
        
        if (!validateInt($product_id) || $product_id <= 0) {
            jsonResponse('error', 'Invalid product ID', null, 400);
        }
        if (!validateInt($quantity) || $quantity <= 0) {
            jsonResponse('error', 'Invalid quantity', null, 400);
        }
        
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        
        if ($stmt->execute()) {
            jsonResponse('success', 'Quantity updated', null, 200);
        } else {
            throw new Exception('Update execute failed');
        }
    } catch (Exception $e) {
        error_log("Cart PUT error: " . $e->getMessage());
        jsonResponse('error', 'Failed to update quantity', null, 500);
    }
}
