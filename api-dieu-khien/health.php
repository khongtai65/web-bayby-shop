<?php
// Health check endpoint für Railway debugging
require_once '../config.php';

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'database' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'connected' => false,
        'tables' => [],
        'products_count' => 0
    ],
    'php_version' => phpversion(),
    'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
];

// Check database connection
if (!$conn->connect_error) {
    $health['database']['connected'] = true;
    
    // Get list of tables
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        while ($row = $result->fetch_row()) {
            $health['database']['tables'][] = $row[0];
        }
    }
    
    // Check products table
    if (in_array('products', $health['database']['tables'])) {
        $result = $conn->query("SELECT COUNT(*) as count FROM products");
        if ($result) {
            $row = $result->fetch_assoc();
            $health['database']['products_count'] = (int)$row['count'];
        }
        
        // Get table columns
        $result = $conn->query("DESCRIBE products");
        if ($result) {
            $cols = [];
            while ($row = $result->fetch_assoc()) {
                $cols[] = $row['Field'];
            }
            $health['database']['products_columns'] = $cols;
        }
    }
} else {
    $health['database']['error'] = $conn->connect_error;
    $health['status'] = 'error';
}

header('Content-Type: application/json');
echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
