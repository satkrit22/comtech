<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

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

// Handle status update
if (isset($_POST['update_status'])) {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $update_query)) {
        $status_message = "Order status updated successfully.";
        // Refresh order data
        $result = mysqli_query($conn, $query);
        $order = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error updating order status: " . mysqli_error($conn);
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
    // Parse the total_products field (assuming it's a serialized or JSON string)
    // This is a fallback if you don't have order_items records
    $total_products = $order['total_products'];
    // You'll need to implement parsing logic based on how your total_products is stored
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
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <?php include 'includes/topnav.php'; ?>

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
                                    <p><strong>Status:</strong> <span class="order-status <?php echo $order['status']; ?>"><i class="fas fa-circle"></i> <?php echo ucfirst($order['status']); ?></span></p>
                                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order['method']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Customer Name:</strong> <?php echo $order['name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $order['number']; ?></p>
                                    <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
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
                                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-image">
                                                            <div class="product-name"><?php echo $item['name']; ?></div>
                                                        </div>
                                                    </td>
                                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
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
                                            <td><strong>$<?php echo number_format($order['total_price'], 2); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Order Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="card-title">Update Status</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
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
        });
    </script>
</body>
</html>