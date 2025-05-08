<?php
session_start();
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile information
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();

// Fetch user order history
$stmt = $conn->prepare("
    SELECT orders.id AS order_id, orders.created_at AS order_date, orders.status, 
           products.name AS product_name, order_items.quantity, order_items.subtotal
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
    WHERE orders.user_id = ?
    ORDER BY orders.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
$orders = [];
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile | Comtech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
<a href="productdisplay.php" class="logo" style="display: flex; align-items: center; text-decoration: none; font-size: 24px; color: #333; font-weight: 600;">
    <img src="assets/img/logo.png" alt="Company Logo" style="width: 40px; height: 40px; margin-right: 10px;">
    Comtech
</a>
    <div class="navbar-nav">
        <a href="productdisplay.php"><i class="fas fa-shopping-bag"></i> Shop</a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
        <a href="signout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <div class="page-header">
        <h1>Your Profile Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($name) ?>!</p>
    </div>

    <div class="dashboard">
        <div class="profile-info card">
            <div class="card-header">
                <h2><i class="fas fa-user-circle"></i> Profile Information</h2>
            </div>
            <div class="card-body">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name"><?= htmlspecialchars($name) ?></div>
                    <div class="user-email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($email) ?></div>
                    <div class="user-phone"><i class="fas fa-phone"></i> <?= htmlspecialchars($phone) ?></div>
                </div>

                <div class="user-stats">
                    <div class="stat-card">
                        <div class="stat-value"><?= count($orders) ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">NPR. <?= number_format(array_sum(array_column($orders, 'subtotal')), 0) ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-history card">
            <div class="card-header">
                <h2><i class="fas fa-history"></i> Order History</h2>
            </div>
            <div class="card-body">
                <?php if (count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_spent = 0;
                                foreach ($orders as $order) {
                                    $total_spent += $order['subtotal'];
                                    $status_class = '';
                                    switch(strtolower($order['status'])) {
                                        case 'delivered':
                                            $status_class = 'status-delivered';
                                            break;
                                        case 'processing':
                                            $status_class = 'status-processing';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'status-cancelled';
                                            break;
                                        default:
                                            $status_class = 'status-pending';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>#{$order['order_id']}</td>";
                                    echo "<td>" . date("M j, Y", strtotime($order['order_date'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($order['product_name']) . "</td>";
                                    echo "<td>{$order['quantity']}</td>";
                                    echo "<td>NPR. " . number_format($order['subtotal'], 0) . "</td>";
                                    echo "<td><span class='status {$status_class}'>" . htmlspecialchars($order['status']) . "</span></td>";
                                    echo "</tr>";
                                }
                                ?>
                                <tr class="total-row">
                                    <td colspan="4">Total Spent</td>
                                    <td colspan="2">NPR. <?= number_format($total_spent, 0) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <p>You haven't placed any orders yet.</p>
                        <a href="productdisplay.php" class="btn"><i class="fas fa-shopping-cart"></i> Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>