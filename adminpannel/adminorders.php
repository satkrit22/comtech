<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get filter parameters
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Get orders based on filters
$orders = getOrders($conn, $status, $date_from, $date_to, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <?php include 'includes/topnav.php'; ?>

            <!-- Orders Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Manage Orders</h1>
                </div>

                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success_msg'] ?>
                        <?php unset($_SESSION['success_msg']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error_msg'] ?>
                        <?php unset($_SESSION['error_msg']); ?>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="filters-container">
                    <form action="" method="GET" class="filters-form">
                        <div class="filter-group">
                            <label for="search">Search:</label>
                            <div class="search-input-group">
                                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Order ID or customer name...">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $status == 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="date_from">Date From:</label>
                            <input type="date" id="date_from" name="date_from" value="<?= $date_from ?>">
                        </div>

                        <div class="filter-group">
                            <label for="date_to">Date To:</label>
                            <input type="date" id="date_to" name="date_to" value="<?= $date_to ?>">
                        </div>

                        <div class="filter-group">
                            <button type="submit" class="btn btn-secondary">Apply Filters</button>
                            <a href="orders.php" class="btn btn-outline">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2><i class="fas fa-shopping-bag"></i> Orders List</h2>
                        <span class="badge"><?= count($orders) ?> orders</span>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($orders) > 0): ?>
                                        <?php foreach ($orders as $order): ?>
                                            <?php $status_class = getStatusClass($order['status']); ?>
                                            <tr>
                                                <td>#<?= $order['id'] ?></td>
                                                <td><?= htmlspecialchars($order['name']) ?></td>
                                                <td>
                                                    <div class="contact-info">
                                                        <div><i class="fas fa-phone"></i> <?= htmlspecialchars($order['number']) ?></div>
                                                        <div><i class="fas fa-envelope"></i> <?= htmlspecialchars($order['email']) ?></div>
                                                    </div>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                                <td>NPR <?= number_format($order['total_price']) ?></td>
                                                <td>
                                                    <span class="status-badge <?= $status_class ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="actions-cell">
                                                    <a href="order-details.php?id=<?= $order['id'] ?>" class="action-btn view-btn" title="View Order Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="update-order.php?id=<?= $order['id'] ?>" class="action-btn edit-btn" title="Update Order Status">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No orders found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
