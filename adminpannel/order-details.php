<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get order details
$query = "SELECT o.*, u.Name as customer_name, u.Email as customer_email 
      FROM orders o 
      LEFT JOIN users u ON o.user_id = u.id 
      WHERE o.id = '$order_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: orders.php');
    exit();
}

$order = mysqli_fetch_assoc($result);

// Handle status updates
if (isset($_POST['update_payment_status'])) {
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);

    $update_query = "UPDATE orders SET payment_status = '$payment_status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $update_query)) {
        $status_message = "Payment status updated successfully.";
        // Refresh order data
        $result = mysqli_query($conn, $query);
        $order = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error updating payment status: " . mysqli_error($conn);
    }
}

if (isset($_POST['update_delivery_status'])) {
    $delivery_status = mysqli_real_escape_string($conn, $_POST['delivery_status']);

    $update_query = "UPDATE orders SET delivery_status = '$delivery_status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $update_query)) {
        $status_message = "Delivery status updated successfully.";
        // Refresh order data
        $result = mysqli_query($conn, $query);
        $order = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error updating delivery status: " . mysqli_error($conn);
    }
}

// Get order items
$items_query = "SELECT oi.*, p.name, p.image 
           FROM order_items oi 
           JOIN products p ON oi.product_id = p.id 
           WHERE oi.order_id = '$order_id'";
$items_result = mysqli_query($conn, $items_query);

// If no items found in order_items table, try parsing the total_products field
$order_items = [];
if (mysqli_num_rows($items_result) > 0) {
    while ($item = mysqli_fetch_assoc($items_result)) {
        $order_items[] = $item;
    }
} else {
    $total_products = $order['total_products'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="orders.css">
    <style>
        /* Admin styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            color: #212529;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
        }

        .breadcrumb {
            list-style: none;
            display: flex;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }

        .breadcrumb-item {
            color: #6c757d;
        }

        .breadcrumb-item a {
            color: #4361ee;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #333;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin-left: 0.5rem;
            color: #6c757d;
        }

        .page-actions {
            display: flex;
            gap: 0.5rem;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #4361ee;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px 8px 0 0;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: #4361ee;
            color: white;
        }

        .btn-light {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .row {
            display: flex;
            gap: 1.5rem;
        }

        .col-md-8 {
            flex: 0 0 66.666667%;
        }

        .col-md-6 {
            flex: 0 0 50%;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
        }

        .order-status {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .payment-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .payment-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .payment-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .payment-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .delivery-pending {
            background-color: #f3f4f6;
            color: #374151;
        }

        .delivery-processing {
            background-color: #fef3c7;
            color: #92400e;
        }

        .delivery-shipped {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .delivery-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .delivery-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .confirmation-code {
            font-family: 'Courier New', monospace;
            background-color: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-name {
            font-weight: 500;
        }

        .customer-profile {
            text-align: center;
        }

        .customer-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .customer-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .customer-email, .customer-phone {
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
            color: #333;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Order Details</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
                        <li class="breadcrumb-item active">Order #<?php echo $order['id']; ?></li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="orders.php" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                    <a href="edit-order.php?id=<?php echo $order['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Order
                    </a>
                    <a href="#" class="btn btn-primary" id="print-btn">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>

            <?php if (isset($status_message)): ?>
            <div class="alert alert-success">
                <?php echo $status_message; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <!-- Order Information -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="card-title">Order Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                                    <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Confirmation Code:</strong> 
                                        <?php if ($order['confirmation_code']): ?>
                                            <span class="confirmation-code"><?php echo htmlspecialchars($order['confirmation_code']); ?></span>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">Not generated</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order['method']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Customer Name:</strong> <?php echo $order['name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $order['number']; ?></p>
                                    <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Payment Status:</strong> 
                                        <span class="order-status payment-<?php echo $order['payment_status']; ?>">
                                            <i class="fas fa-circle"></i> <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Delivery Status:</strong> 
                                        <span class="order-status delivery-<?php echo $order['delivery_status']; ?>">
                                            <i class="fas fa-circle"></i> <?php echo ucfirst($order['delivery_status']); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Order Items</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total = 0;
                                        if (!empty($order_items)) {
                                            foreach ($order_items as $item) {
                                                $subtotal = $item['price'] * $item['quantity'];
                                                $total += $subtotal;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="product-info">
                                                            <img src="/comtech/assets/img/menu/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-image">
                                                            <div class="product-name"><?php echo $item['name']; ?></div>
                                                        </div>
                                                    </td>
                                                    <td>NPR.<?php echo number_format($item['price'], 2); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td>NPR.<?php echo number_format($subtotal, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            // If no items found, display the total_products field
                                            echo '<tr><td colspan="4">' . $order['total_products'] . '</td></tr>';
                                            $total = $order['total_price'];
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td><strong>NPR.<?php echo number_format($order['total_price'], 2); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Payment Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="card-title">Update Payment Status</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-select">
                                        <option value="pending" <?php echo $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['payment_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $order['payment_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="failed" <?php echo $order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_payment_status" class="btn btn-primary">Update Payment Status</button>
                            </form>
                        </div>
                    </div>

                    <!-- Delivery Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="card-title">Update Delivery Status</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="delivery_status" class="form-label">Delivery Status</label>
                                    <select name="delivery_status" id="delivery_status" class="form-select">
                                        <option value="pending" <?php echo $order['delivery_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['delivery_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['delivery_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['delivery_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['delivery_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_delivery_status" class="btn btn-primary">Update Delivery Status</button>
                            </form>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Customer Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="customer-profile">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($order['name']); ?>&background=4361ee&color=fff" alt="Customer" class="customer-avatar-large">
                                <h3 class="customer-name"><?php echo $order['name']; ?></h3>
                                <p class="customer-email"><?php echo $order['email']; ?></p>
                                <p class="customer-phone"><?php echo $order['number']; ?></p>
                                <hr>
                                <h4>Shipping Address</h4>
                                <p><?php echo nl2br($order['address']); ?></p>
                                
                                <?php if (!empty($order['customer_name'])): ?>
                                <hr>
                                <h4>Account Information</h4>
                                <p><strong>Registered User:</strong> Yes</p>
                                <p><strong>User ID:</strong> <?php echo $order['user_id']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.display = 'none';
                });
            }, 5000);
            
            // Print functionality
            document.getElementById('print-btn').addEventListener('click', function(e) {
                e.preventDefault();
                window.print();
            });
            
            // Toggle sidebar on mobile
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>
