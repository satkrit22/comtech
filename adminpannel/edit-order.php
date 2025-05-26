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

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details
$query = "SELECT * FROM orders WHERE id = $order_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    header('Location: orders.php');
    exit();
}

$order = mysqli_fetch_assoc($result);

// Get all users for dropdown
$users_query = "SELECT id, Name, Email FROM users ORDER BY Name";
$users_result = mysqli_query($conn, $users_query);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $number = sanitize($_POST['number']);
    $address = sanitize($_POST['address']);
    $method = sanitize($_POST['method']);
    $payment_status = sanitize($_POST['payment_status']);
    $delivery_status = sanitize($_POST['delivery_status']);
    $total_price = (float)$_POST['total_price'];
    $user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : 'NULL';
    
    // Update order
    $update_query = "UPDATE orders SET 
                    name = '$name', 
                    email = '$email', 
                    number = '$number', 
                    address = '$address', 
                    method = '$method', 
                    payment_status = '$payment_status',
                    delivery_status = '$delivery_status', 
                    total_price = $total_price, 
                    user_id = $user_id 
                    WHERE id = $order_id";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['alert'] = [
            'message' => 'Order updated successfully.',
            'type' => 'success'
        ];
        header('Location: order-details.php?id=' . $order_id);
        exit();
    } else {
        $error_message = 'Error updating order: ' . mysqli_error($conn);
    }
}

// Get order items
$items_query = "SELECT oi.*, p.name 
               FROM order_items oi 
               JOIN products p ON oi.product_id = p.id 
               WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order | Comtech Admin</title>
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

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-block {
            width: 100%;
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

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Order</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
                        <li class="breadcrumb-item active">Edit Order #<?php echo $order_id; ?></li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="order-details.php?id=<?php echo $order_id; ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Order Details
                    </a>
                </div>
            </div>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h2 class="card-title">Order Information</h2>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">Customer Name</label>
                                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $order['name']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $order['email']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="number" class="form-label">Phone Number</label>
                                            <input type="text" id="number" name="number" class="form-control" value="<?php echo $order['number']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="method" class="form-label">Payment Method</label>
                                            <select id="method" name="method" class="form-select" required>
                                                <option value="Cash on Delivery" <?php echo $order['method'] == 'Cash on Delivery' ? 'selected' : ''; ?>>Cash on Delivery</option>
                                                <option value="Khalti" <?php echo $order['method'] == 'Khalti' ? 'selected' : ''; ?>>Khalti</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Shipping Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="3" required><?php echo $order['address']; ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="payment_status" class="form-label">Payment Status</label>
                                            <select id="payment_status" name="payment_status" class="form-select" required>
                                                <option value="pending" <?php echo $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['payment_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="completed" <?php echo $order['payment_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="failed" <?php echo $order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="delivery_status" class="form-label">Delivery Status</label>
                                            <select id="delivery_status" name="delivery_status" class="form-select" required>
                                                <option value="pending" <?php echo $order['delivery_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['delivery_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['delivery_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['delivery_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['delivery_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="total_price" class="form-label">Total Price</label>
                                    <input type="number" id="total_price" name="total_price" class="form-control" value="<?php echo $order['total_price']; ?>" step="0.01" min="0" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="user_id" class="form-label">Linked User Account (Optional)</label>
                                    <select id="user_id" name="user_id" class="form-select">
                                        <option value="">-- No User Account --</option>
                                        <?php 
                                        mysqli_data_seek($users_result, 0);
                                        while ($user = mysqli_fetch_assoc($users_result)): 
                                        ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $order['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo $user['Name'] . ' (' . $user['Email'] . ')'; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (mysqli_num_rows($items_result) > 0): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h2 class="card-title">Order Items</h2>
                                <small class="text-muted">Note: To modify order items, please use the product management interface.</small>
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
                                            while ($item = mysqli_fetch_assoc($items_result)) {
                                                $subtotal = $item['price'] * $item['quantity'];
                                                $total += $subtotal;
                                                ?>
                                                <tr>
                                                    <td><?php echo $item['name']; ?></td>
                                                    <td>NPR.<?php echo number_format($item['price'], 2); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td>NPR.<?php echo number_format($subtotal, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                <td><strong>NPR.<?php echo number_format($total, 2); ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h2 class="card-title">Order Items</h2>
                            </div>
                            <div class="card-body">
                                <p>Order items information:</p>
                                <pre><?php echo $order['total_products']; ?></pre>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Actions</h2>
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block mb-3">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="order-details.php?id=<?php echo $order_id; ?>" class="btn btn-light btn-block mb-3">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <a href="delete-order.php?id=<?php echo $order_id; ?>" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
