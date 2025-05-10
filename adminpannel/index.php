<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Get dashboard statistics
$stats = [
    'total_users' => 0,
    'total_products' => 0,
    'total_categories' => 0,
    'total_orders' => 0,
    'pending_orders' => 0,
    'completed_orders' => 0,
    'total_revenue' => 0,
    'today_revenue' => 0,
    'weekly_revenue' => 0,
    'monthly_revenue' => 0
];

// Get total users
$users_query = "SELECT COUNT(*) as count FROM users";
$users_result = mysqli_query($conn, $users_query);
if ($users_result) {
    $stats['total_users'] = mysqli_fetch_assoc($users_result)['count'];
}

// Get total products
$products_query = "SELECT COUNT(*) as count FROM products";
$products_result = mysqli_query($conn, $products_query);
if ($products_result) {
    $stats['total_products'] = mysqli_fetch_assoc($products_result)['count'];
}

// Get total categories
$categories_query = "SELECT COUNT(*) as count FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
if ($categories_result) {
    $stats['total_categories'] = mysqli_fetch_assoc($categories_result)['count'];
}

// Get total orders
$orders_query = "SELECT COUNT(*) as count FROM orders";
$orders_result = mysqli_query($conn, $orders_query);
if ($orders_result) {
    $stats['total_orders'] = mysqli_fetch_assoc($orders_result)['count'];
}

// Get pending orders
$pending_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
if ($pending_result) {
    $stats['pending_orders'] = mysqli_fetch_assoc($pending_result)['count'];
}

// Get completed orders
$completed_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'completed'";
$completed_result = mysqli_query($conn, $completed_query);
if ($completed_result) {
    $stats['completed_orders'] = mysqli_fetch_assoc($completed_result)['count'];
}

// Get total revenue
$revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'";
$revenue_result = mysqli_query($conn, $revenue_query);
if ($revenue_result) {
    $stats['total_revenue'] = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;
}

// Get today's revenue
$today = date('Y-m-d');
$today_revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed' AND DATE(created_at) = '$today'";
$today_revenue_result = mysqli_query($conn, $today_revenue_query);
if ($today_revenue_result) {
    $stats['today_revenue'] = mysqli_fetch_assoc($today_revenue_result)['total'] ?? 0;
}

// Get weekly revenue
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$weekly_revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed' AND DATE(created_at) BETWEEN '$week_start' AND '$week_end'";
$weekly_revenue_result = mysqli_query($conn, $weekly_revenue_query);
if ($weekly_revenue_result) {
    $stats['weekly_revenue'] = mysqli_fetch_assoc($weekly_revenue_result)['total'] ?? 0;
}

// Get monthly revenue
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$monthly_revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed' AND DATE(created_at) BETWEEN '$month_start' AND '$month_end'";
$monthly_revenue_result = mysqli_query($conn, $monthly_revenue_query);
if ($monthly_revenue_result) {
    $stats['monthly_revenue'] = mysqli_fetch_assoc($monthly_revenue_result)['total'] ?? 0;
}

// Get recent orders
$recent_orders_query = "SELECT o.*, u.Name as customer_name 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC 
                        LIMIT 5";
$recent_orders_result = mysqli_query($conn, $recent_orders_query);

// Get top selling products
$top_products_query = "SELECT p.id, p.name, p.image, p.price, COUNT(oi.id) as order_count 
                       FROM products p 
                       LEFT JOIN order_items oi ON p.id = oi.product_id 
                       GROUP BY p.id 
                       ORDER BY order_count DESC 
                       LIMIT 5";
$top_products_result = mysqli_query($conn, $top_products_query);

// Get monthly sales data for chart
$monthly_sales_data = [];
for ($i = 0; $i < 6; $i++) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    
    $monthly_sales_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed' AND DATE(created_at) BETWEEN '$month_start' AND '$month_end'";
    $monthly_sales_result = mysqli_query($conn, $monthly_sales_query);
    $monthly_total = mysqli_fetch_assoc($monthly_sales_result)['total'] ?? 0;
    
    $monthly_sales_data[date('M Y', strtotime($month))] = $monthly_total;
}
$monthly_sales_data = array_reverse($monthly_sales_data);

// Get sales by category
$category_sales_query = "SELECT c.name, COUNT(oi.id) as order_count, SUM(oi.price * oi.quantity) as total_sales
                         FROM categories c
                         LEFT JOIN products p ON c.id = p.category_id
                         LEFT JOIN order_items oi ON p.id = oi.product_id
                         LEFT JOIN orders o ON oi.order_id = o.id
                         WHERE o.status = 'completed'
                         GROUP BY c.id
                         ORDER BY total_sales DESC
                         LIMIT 5";
$category_sales_result = mysqli_query($conn, $category_sales_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="/comtech/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/comtech/assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Core Admin Dashboard Styles */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #6c757d;
            --success: #2ecc71;
            --info: #3498db;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #343a40;
            --body-bg: #f5f7fb;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #adb5bd;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            --card-border-radius: 8px;
            --btn-border-radius: 4px;
            --input-border-radius: 4px;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --sidebar-bg: #1e1e2d;
            --sidebar-color: #a2a3b7;
            --sidebar-hover-bg: #282839;
            --sidebar-active-bg: #282839;
            --sidebar-active-color: #ffffff;
            --topnav-height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            line-height: 1.5;
            font-size: 14px;
        }

        a {
            text-decoration: none;
            color: var(--primary);
        }

        a:hover {
            color: var(--primary-dark);
        }

        /* Layout */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        .sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Dashboard Specific Styles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: var(--card-border-radius);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .stat-icon.users {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }

        .stat-icon.products {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        .stat-icon.categories {
            background-color: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }

        .stat-icon.orders {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }

        .stat-icon.revenue {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .order-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .order-status.pending {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }

        .order-status.processing {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }

        .order-status.completed {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        .order-status.cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .product-image-tiny {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            object-fit: cover;
            margin-right: 10px;
        }

        .product-info {
            display: flex;
            align-items: center;
        }

        .product-name {
            font-weight: 500;
        }

        .product-price {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .order-count {
            font-weight: 600;
            color: var(--primary);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        
        .sales-summary {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .sales-card {
            background-color: var(--card-bg);
            border-radius: var(--card-border-radius);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .sales-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .sales-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .sales-summary {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'topnav.php'; ?>

            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    
                </div>
            </div>
            
            <!-- Sales Summary -->
            <div class="sales-summary">
                <div class="sales-card">
                    <div class="sales-value">NPR.<?php echo number_format($stats['today_revenue'], 2); ?></div>
                    <div class="sales-label">Today's Sales</div>
                </div>
                <div class="sales-card">
                    <div class="sales-value">NPR.<?php echo number_format($stats['weekly_revenue'], 2); ?></div>
                    <div class="sales-label">Weekly Sales</div>
                </div>
                <div class="sales-card">
                    <div class="sales-value">NPR.<?php echo number_format($stats['monthly_revenue'], 2); ?></div>
                    <div class="sales-label">Monthly Sales</div>
                </div>
                <div class="sales-card">
                    <div class="sales-value">NPR.<?php echo number_format($stats['total_revenue'], 2); ?></div>
                    <div class="sales-label">Total Revenue</div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: rgb(0, 0, 0); "><?php echo $stats['total_users']; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon products">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: rgb(0, 0, 0); "><?php echo $stats['total_products']; ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon categories">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: rgb(0, 0, 0); "><?php echo $stats['total_categories']; ?></div>
                        <div class="stat-label">Categories</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: rgb(0, 0, 0); "><?php echo $stats['total_orders']; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: rgb(0, 0, 0); ">NPR.<?php echo number_format($stats['total_revenue'], 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orders" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: rgb(0, 0, 0); "><?php echo $stats['pending_orders']; ?></div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                </div>
            </div>
            
            <!-- Sales Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Monthly Sales</h2>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="dashboard-row">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Orders</h2>
                        <div class="card-tools">
                            <a href="orders.php" class="btn btn-sm btn-light">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (mysqli_num_rows($recent_orders_result) > 0) {
                                        while ($order = mysqli_fetch_assoc($recent_orders_result)) {
                                            $status_class = '';
                                            switch ($order['status']) {
                                                case 'pending':
                                                    $status_class = 'pending';
                                                    break;
                                                case 'processing':
                                                    $status_class = 'processing';
                                                    break;
                                                case 'completed':
                                                    $status_class = 'completed';
                                                    break;
                                                case 'cancelled':
                                                    $status_class = 'cancelled';
                                                    break;
                                            }
                                            
                                            echo '<tr>';
                                            echo '<td>#' . $order['id'] . '</td>';
                                            echo '<td>' . $order['name'] . '</td>';
                                            echo '<td>NPR.' . number_format($order['total_price'], 2) . '</td>';
                                            echo '<td><span class="order-status ' . $status_class . '">' . ucfirst($order['status']) . '</span></td>';
                                            echo '<td>' . date('M d, Y', strtotime($order['created_at'])) . '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No orders found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Top Selling Products</h2>
                        <div class="card-tools">
                            <a href="products.php" class="btn btn-sm btn-light">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (mysqli_num_rows($top_products_result) > 0) {
                                        while ($product = mysqli_fetch_assoc($top_products_result)) {
                                            echo '<tr>';
                                            echo '<td>
                                                <div class="product-info">
                                                    <img src="/comtech/assets/img/menu/' . (!empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/40') . '" alt="Product" class="product-image-tiny">
                                                    <div>
                                                        <div class="product-name">' . $product['name'] . '</div>
                                                        <div class="product-price">ID: ' . $product['id'] . '</div>
                                                    </div>
                                                </div>
                                            </td>';
                                            echo '<td>NPR.' . number_format($product['price'], 2) . '</td>';
                                            echo '<td class="order-count">' . $product['order_count'] . '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="3" class="text-center">No products found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Category Sales Chart -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Sales by Category</h2>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categorySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                });
            }
            
            // Monthly Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_keys($monthly_sales_data)) . "'"; ?>],
                    datasets: [{
                        label: 'Monthly Sales',
                        data: [<?php echo implode(', ', array_values($monthly_sales_data)); ?>],
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'NPR.' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Sales: NPR.' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Category Sales Chart
            const categoryCtx = document.getElementById('categorySalesChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php 
                        $category_names = [];
                        $category_sales = [];
                        mysqli_data_seek($category_sales_result, 0);
                        while ($category = mysqli_fetch_assoc($category_sales_result)) {
                            $category_names[] = $category['name'];
                            $category_sales[] = $category['total_sales'] ?? 0;
                        }
                        echo "'" . implode("', '", $category_names) . "'";
                        ?>
                    ],
                    datasets: [{
                        label: 'Sales Amount',
                        data: [<?php echo implode(', ', $category_sales); ?>],
                        backgroundColor: [
                            'rgba(67, 97, 238, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(243, 156, 18, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'NPR.' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Sales: NPR.' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>