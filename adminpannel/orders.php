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

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Handle order status updates
if (isset($_POST['update_payment_status']) && isset($_POST['order_id']) && isset($_POST['payment_status'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    
    $update_query = "UPDATE orders SET payment_status = '$payment_status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        $status_message = "Payment status updated successfully.";
    } else {
        $error_message = "Error updating payment status: " . mysqli_error($conn);
    }
}

if (isset($_POST['update_delivery_status']) && isset($_POST['order_id']) && isset($_POST['delivery_status'])) {
    $order_id = $_POST['order_id'];
    $delivery_status = $_POST['delivery_status'];
    
    $update_query = "UPDATE orders SET delivery_status = '$delivery_status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        $status_message = "Delivery status updated successfully.";
    } else {
        $error_message = "Error updating delivery status: " . mysqli_error($conn);
    }
}

// Pagination
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Build the query
$where = "1=1"; // Default condition that's always true

// Apply filters if set
if (isset($_GET['payment_status']) && !empty($_GET['payment_status'])) {
    $payment_status = mysqli_real_escape_string($conn, $_GET['payment_status']);
    $where .= " AND payment_status = '$payment_status'";
}

if (isset($_GET['delivery_status']) && !empty($_GET['delivery_status'])) {
    $delivery_status = mysqli_real_escape_string($conn, $_GET['delivery_status']);
    $where .= " AND delivery_status = '$delivery_status'";
}

if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
    $date_range = explode(' - ', $_GET['date_range']);
    if (count($date_range) == 2) {
        $start_date = mysqli_real_escape_string($conn, $date_range[0]);
        $end_date = mysqli_real_escape_string($conn, $date_range[1]);
        $where .= " AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    }
}

if (isset($_GET['customer']) && !empty($_GET['customer'])) {
    $customer = mysqli_real_escape_string($conn, $_GET['customer']);
    $where .= " AND (name LIKE '%$customer%' OR email LIKE '%$customer%')";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM orders WHERE $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get orders with pagination
$query = "SELECT o.*, u.Name as customer_name, u.Email as customer_email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE $where 
          ORDER BY o.created_at DESC 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="orders.css">
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
                    <h1 class="page-title">Orders</h1>
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

            <!-- Order Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="order-filters">
                        <div class="order-filter-item">
                            <label for="payment-status-filter" class="form-label">Payment Status</label>
                            <select id="payment-status-filter" name="payment_status" class="form-select">
                                <option value="">All Payment Status</option>
                                <option value="pending" <?php echo (isset($_GET['payment_status']) && $_GET['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo (isset($_GET['payment_status']) && $_GET['payment_status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?php echo (isset($_GET['payment_status']) && $_GET['payment_status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="failed" <?php echo (isset($_GET['payment_status']) && $_GET['payment_status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>
                        <div class="order-filter-item">
                            <label for="delivery-status-filter" class="form-label">Delivery Status</label>
                            <select id="delivery-status-filter" name="delivery_status" class="form-select">
                                <option value="">All Delivery Status</option>
                                <option value="pending" <?php echo (isset($_GET['delivery_status']) && $_GET['delivery_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo (isset($_GET['delivery_status']) && $_GET['delivery_status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo (isset($_GET['delivery_status']) && $_GET['delivery_status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo (isset($_GET['delivery_status']) && $_GET['delivery_status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo (isset($_GET['delivery_status']) && $_GET['delivery_status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="order-filter-item" style="align-self: flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="orders.php" class="btn btn-light">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">All Orders</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table order-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Confirmation Code</th>
                                    <th>Payment Status</th>
                                    <th>Delivery Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while ($order = mysqli_fetch_assoc($result)) {
                                        // Format date
                                        $date = date('Y-m-d', strtotime($order['created_at']));
                                        
                                        // Get customer name and email
                                        $customer_name = !empty($order['customer_name']) ? $order['customer_name'] : $order['name'];
                                        $customer_email = !empty($order['customer_email']) ? $order['customer_email'] : $order['email'];
                                        
                                        echo '<tr>';
                                        echo '<td class="order-id">' . $order['id'] . '</td>';
                                        echo '<td>
                                            <div class="customer-info">
                                                <img src="https://ui-avatars.com/api/?name=' . urlencode($customer_name) . '&background=4361ee&color=fff" alt="Customer" class="customer-avatar">
                                                <div>
                                                    <div class="customer-name">' . $customer_name . '</div>
                                                    <div class="customer-email">' . $customer_email . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td class="order-date">' . $date . '</td>';
                                        echo '<td>';
                                        if ($order['confirmation_code']) {
                                            echo '<span class="confirmation-code">' . htmlspecialchars($order['confirmation_code']) . '</span>';
                                        } else {
                                            echo '<span style="color: #9ca3af;">Not generated</span>';
                                        }
                                        echo '</td>';
                                        echo '<td><span class="order-status payment-' . $order['payment_status'] . '"><i class="fas fa-circle"></i> ' . ucfirst($order['payment_status']) . '</span></td>';
                                        echo '<td><span class="order-status delivery-' . $order['delivery_status'] . '"><i class="fas fa-circle"></i> ' . ucfirst($order['delivery_status']) . '</span></td>';
                                        echo '<td class="order-total">NPR.' . number_format($order['total_price'], 2) . '</td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="order-details.php?id=' . $order['id'] . '" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-order.php?id=' . $order['id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-primary payment-status-btn" data-id="' . $order['id'] . '" data-status="' . $order['payment_status'] . '">
                                                    <i class="fas fa-credit-card"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning delivery-status-btn" data-id="' . $order['id'] . '" data-status="' . $order['delivery_status'] . '">
                                                    <i class="fas fa-truck"></i>
                                                </button>
                                                <a href="delete-order.php?id=' . $order['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    // If no orders found
                                    echo '<tr><td colspan="8" class="text-center">No orders found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing <?php echo $start + 1; ?> to <?php echo min($start + mysqli_num_rows($result), $total_records); ?> of <?php echo $total_records; ?> entries
                        </div>
                        <ul class="pagination">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['payment_status']) ? '&payment_status=' . $_GET['payment_status'] : ''; ?><?php echo isset($_GET['delivery_status']) ? '&delivery_status=' . $_GET['delivery_status'] : ''; ?><?php echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : ''; ?>" tabindex="-1">Previous</a>
                            </li>
                            
                            <?php
                            // Determine the range of page numbers to display
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            // Display the page numbers
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                echo '<li class="page-item ' . (($page == $i) ? 'active' : '') . '"><a class="page-link" href="?page=' . $i;
                                echo isset($_GET['payment_status']) ? '&payment_status=' . $_GET['payment_status'] : '';
                                echo isset($_GET['delivery_status']) ? '&delivery_status=' . $_GET['delivery_status'] : '';
                                echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : '';
                                echo '">' . $i . '</a></li>';
                            }
                            ?>
                            
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['payment_status']) ? '&payment_status=' . $_GET['payment_status'] : ''; ?><?php echo isset($_GET['delivery_status']) ? '&delivery_status=' . $_GET['delivery_status'] : ''; ?><?php echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : ''; ?>">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Payment Status Modal -->
    <div class="modal" id="paymentStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Payment Status</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="payment_order_id">
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_payment_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delivery Status Modal -->
    <div class="modal" id="deliveryStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Delivery Status</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="delivery_order_id">
                        <div class="form-group">
                            <label for="delivery_status">Delivery Status</label>
                            <select name="delivery_status" id="delivery_status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_delivery_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment status update modal
            const paymentStatusButtons = document.querySelectorAll('.payment-status-btn');
            const paymentModal = document.getElementById('paymentStatusModal');
            
            paymentStatusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    const currentStatus = this.getAttribute('data-status');
                    document.getElementById('payment_order_id').value = orderId;
                    document.getElementById('payment_status').value = currentStatus;
                    paymentModal.classList.add('show');
                });
            });

            // Delivery status update modal
            const deliveryStatusButtons = document.querySelectorAll('.delivery-status-btn');
            const deliveryModal = document.getElementById('deliveryStatusModal');
            
            deliveryStatusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    const currentStatus = this.getAttribute('data-status');
                    document.getElementById('delivery_order_id').value = orderId;
                    document.getElementById('delivery_status').value = currentStatus;
                    deliveryModal.classList.add('show');
                });
            });

            // Close modal functionality
            const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    modal.classList.remove('show');
                });
            });

            // Close modal when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.classList.remove('show');
                }
            });
            
            // Delete confirmation
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this order?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
