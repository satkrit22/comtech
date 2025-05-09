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
<?php
// Function to sanitize input data
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Function to redirect to a page
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to display alert message
function alert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Function to get product count by category
function getProductCountByCategory($category_id) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM products WHERE category_id = $category_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Function to get user by ID
function getUserById($user_id) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get product by ID
function getProductById($product_id) {
    global $conn;
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get category by ID
function getCategoryById($category_id) {
    global $conn;
    $query = "SELECT * FROM categories WHERE id = $category_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get all categories
function getAllCategories() {
    global $conn;
    $query = "SELECT * FROM categories";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

// Function to format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Function to generate pagination
function generatePagination($current_page, $total_pages, $url_params = '') {
    $pagination = '<ul class="pagination">';
    
    // Previous button
    if ($current_page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . ($current_page - 1) . $url_params . '">Previous</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $pagination .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . $i . $url_params . '">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="?page=' . ($current_page + 1) . $url_params . '">Next</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
    }
    
    $pagination .= '</ul>';
    return $pagination;
}
?>
