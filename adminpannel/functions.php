<script>
function getDashboardStats($conn) {
    $stats = [];

    // Total Sales
    $result = $conn->query("SELECT SUM(total_price) AS total_sales FROM orders WHERE status = 'completed'");
    $stats['total_sales'] = $result->fetch_assoc()['total_sales'];

    // Total Orders
    $result = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['total_orders'];

    // Total Products
    $result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
    $stats['total_products'] = $result->fetch_assoc()['total_products'];

    // Total Users
    $result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $stats['total_users'] = $result->fetch_assoc()['total_users'];

    return $stats;
}
function getRecentOrders($conn, $limit = 5) {
    $sql = "SELECT o.id, u.name, o.created_at, o.total_price, o.status
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
</script>