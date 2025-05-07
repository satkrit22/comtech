<?php

function getDashboardStats($conn) {
    $stats = [];

    // Total Sales (only completed orders)
    $result = $conn->query("SELECT SUM(total_price) AS total_sales FROM orders WHERE status = 'completed'");
    $row = $result->fetch_assoc();
    $stats['total_sales'] = $row['total_sales'] ?? 0;

    // Total Orders
    $result = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
    $row = $result->fetch_assoc();
    $stats['total_orders'] = $row['total_orders'] ?? 0;

    // Total Products
    $result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
    $row = $result->fetch_assoc();
    $stats['total_products'] = $row['total_products'] ?? 0;

    // Total Users
    $result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $row = $result->fetch_assoc();
    $stats['total_users'] = $row['total_users'] ?? 0;

    return $stats;
}

function getRecentOrders($conn, $limit = 5) {
    $sql = "SELECT o.id, o.created_at, o.total_price, o.status, u.name 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}

function getLowStockProducts($conn, $limit = 5) {
    $sql = "SELECT id, name, price, stock, image 
            FROM products 
            WHERE stock <= 5 
            ORDER BY stock ASC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}

function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'completed':
            return 'status-completed';
        case 'pending':
            return 'status-pending';
        case 'cancelled':
            return 'status-cancelled';
        default:
            return 'status-unknown';
    }
}
?>
