<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Dashboard statistics
$stats = getDashboardStats($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Comtech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/comtech/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <?php include 'includes/topnav.php'; ?>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?= htmlspecialchars($admin_name) ?>!</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card bg-primary">
                        <div class="stat-card-content">
                            <div class="stat-card-info">
                                <h3>Total Sales</h3>
                                <p class="stat-value">NPR <?= number_format($stats['total_sales']) ?></p>
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 12.5% from last month
                                </p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-success">
                        <div class="stat-card-content">
                            <div class="stat-card-info">
                                <h3>Total Orders</h3>
                                <p class="stat-value"><?= number_format($stats['total_orders']) ?></p>
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 8.2% from last month
                                </p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-warning">
                        <div class="stat-card-content">
                            <div class="stat-card-info">
                                <h3>Total Products</h3>
                                <p class="stat-value"><?= number_format($stats['total_products']) ?></p>
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 5.3% from last month
                                </p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-info">
                        <div class="stat-card-content">
                            <div class="stat-card-info">
                                <h3>Total Users</h3>
                                <p class="stat-value"><?= number_format($stats['total_users']) ?></p>
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 15.7% from last month
                                </p>
                            </div>
                            <div class="stat-card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders & Low Stock -->
                <div class="dashboard-grid">
                    <!-- Recent Orders -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h2><i class="fas fa-shopping-bag"></i> Recent Orders</h2>
                            <a href="orders.php" class="view-all">View All</a>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $recent_orders = getRecentOrders($conn, 5);
                                        if (count($recent_orders) > 0):
                                            foreach ($recent_orders as $order):
                                                $status_class = getStatusClass($order['status']);
                                        ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td><?= htmlspecialchars($order['name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                            <td>NPR <?= number_format($order['total_price']) ?></td>
                                            <td><span class="status-badge <?= $status_class ?>"><?= ucfirst($order['status']) ?></span></td>
                                            <td>
                                                <a href="order-details.php?id=<?= $order['id'] ?>" class="action-btn view-btn" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                            endforeach;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No recent orders found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Products -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h2><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h2>
                            <a href="products.php?filter=low_stock" class="view-all">View All</a>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $low_stock_products = getLowStockProducts($conn, 5);
                                        if (count($low_stock_products) > 0):
                                            foreach ($low_stock_products as $product):
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="product-info">
                                                    <img src="/comtech/assets/img/menu/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
                                                    <span><?= htmlspecialchars($product['name']) ?></span>
                                                </div>
                                            </td>
                                            <td>NPR <?= number_format($product['price']) ?></td>
                                            <td>
                                                <span class="stock-badge <?= $product['stock'] <= 5 ? 'low-stock' : '' ?>">
                                                    <?= $product['stock'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit-product.php?id=<?= $product['id'] ?>" class="action-btn edit-btn" title="Edit Product">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                            endforeach;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No low stock products found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Analytics -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2><i class="fas fa-chart-line"></i> Sales Analytics</h2>
                        <div class="period-selector">
                            <button class="period-btn active" data-period="weekly">Weekly</button>
                            <button class="period-btn" data-period="monthly">Monthly</button>
                            <button class="period-btn" data-period="yearly">Yearly</button>
                        </div>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/comtech/assets/js/admin.js"></script>
    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Sample data - replace with actual data from database
        const weeklySales = {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            data: [12500, 18200, 15700, 22300, 19800, 28500, 24100]
        };
        
        const monthlySales = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            data: [152000, 168000, 187000, 193000, 205000, 178000, 199000, 225000, 240000, 262000, 278000, 305000]
        };
        
        const yearlySales = {
            labels: ['2018', '2019', '2020', '2021', '2022', '2023'],
            data: [1850000, 2150000, 1950000, 2350000, 2750000, 3150000]
        };
        
        // Initial chart with weekly data
        let salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeklySales.labels,
                datasets: [{
                    label: 'Sales (NPR)',
                    data: weeklySales.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e1e2d',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#36a2eb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'NPR ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'NPR ' + value.toLocaleString();
                            },
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
        
        // Period selector functionality
        document.querySelectorAll('.period-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.period-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update chart based on selected period
                const period = this.dataset.period;
                let chartData;
                
                switch(period) {
                    case 'weekly':
                        chartData = weeklySales;
                        break;
                    case 'monthly':
                        chartData = monthlySales;
                        break;
                    case 'yearly':
                        chartData = yearlySales;
                        break;
                    default:
                        chartData = weeklySales;
                }
                
                // Update chart data
                salesChart.data.labels = chartData.labels;
                salesChart.data.datasets[0].data = chartData.data;
                salesChart.update();
            });
        });
    </script>
</body>
</html>