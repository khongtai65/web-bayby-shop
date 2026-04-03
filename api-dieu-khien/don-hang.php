<?php
ob_clean();
require_once '../config.php';

// Create order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION['user_id'];
    $total_price = $data['total_price'] ?? 0;
    $shipping_address = $data['shipping_address'] ?? '';
    $phone = $data['phone'] ?? '';
    
    if (!$total_price || !$shipping_address || !$phone) {
        jsonResponse('error', 'Missing required fields');
    }
    
    // Create order
    $sql = "INSERT INTO orders (user_id, total_price, shipping_address, phone, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $total_price, $shipping_address, $phone);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Get cart items
        $cart_sql = "SELECT c.product_id, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
        $cart_stmt = $conn->prepare($cart_sql);
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        
        // Insert order items
        while ($item = $cart_result->fetch_assoc()) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param("iiii", $order_id, $product_id, $quantity, $price);
            $item_stmt->execute();
        }
        
        // Clear cart
        $clear_sql = "DELETE FROM cart WHERE user_id = ?";
        $clear_stmt = $conn->prepare($clear_sql);
        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();
        
        jsonResponse('success', 'Order created', ['order_id' => $order_id]);
    } else {
        jsonResponse('error', 'Failed to create order');
    }
}

// Get user orders
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse('error', 'Not logged in');
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    
    jsonResponse('success', 'Orders retrieved', $orders);
}
?>
