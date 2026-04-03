<?php
require_once '../config.php';

// GET: Lấy giỏ hàng của user
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image_path 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
            ORDER BY c.id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = $result->fetch_all(MYSQLI_ASSOC);
    
    jsonResponse('success', 'Cart retrieved', $cart);
}

// POST: Thêm vào giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION['user_id'];
    $product_id = $data['product_id'] ?? 0;
    $quantity = $data['quantity'] ?? 1;
    
    if ($product_id <= 0 || $quantity <= 0) {
        jsonResponse('error', 'Invalid product or quantity');
    }
    
    // Check if product already in cart
    $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update quantity
        $row = $check_result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $new_qty, $user_id, $product_id);
        $update_stmt->execute();
        jsonResponse('success', 'Cart updated');
    } else {
        // Insert new
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        jsonResponse('success', 'Added to cart');
    }
}

// DELETE: Xóa khỏi giỏ
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION['user_id'];
    $product_id = $data['product_id'] ?? 0;
    
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        jsonResponse('success', 'Removed from cart');
    } else {
        jsonResponse('error', 'Failed to remove from cart');
    }
}

// PUT: Cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION['user_id'];
    $product_id = $data['product_id'] ?? 0;
    $quantity = $data['quantity'] ?? 0;
    
    if ($quantity <= 0) {
        jsonResponse('error', 'Invalid quantity');
    }
    
    $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    
    if ($stmt->execute()) {
        jsonResponse('success', 'Quantity updated');
    } else {
        jsonResponse('error', 'Failed to update quantity');
    }
}
?>
