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

// Fetch user order history with confirmation codes and separate statuses
$stmt = $conn->prepare("
    SELECT DISTINCT orders.id AS order_id, orders.created_at AS order_date, 
           orders.payment_status, orders.delivery_status, orders.confirmation_code,
           orders.total_price, orders.method, orders.address
    FROM orders
    WHERE orders.user_id = ?
    ORDER BY orders.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
$orders = [];
$total_spent = 0;
while ($row = $order_result->fetch_assoc()) {
    // Get order items for this order
    $items_stmt = $conn->prepare("
        SELECT oi.quantity, oi.price, p.name as product_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?");
    $items_stmt->bind_param("i", $row['order_id']);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    $items_stmt->close();
    
    $row['items'] = $items;
    $orders[] = $row;
    $total_spent += $row['total_price'];
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-nav {
            display: flex;
            gap: 1.5rem;
        }

        .navbar-nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar-nav a:hover {
            color: #5e2ced;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            align-items: start;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: #5e2ced;
            color: white;
            padding: 1.5rem;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .user-avatar {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .user-avatar i {
            font-size: 4rem;
            color: #5e2ced;
        }

        .user-details {
            margin-bottom: 1.5rem;
        }

        .user-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .user-email, .user-phone {
            color: #666;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .stat-card {
            text-align: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #5e2ced;
        }

        .stat-label {
            color: #666;
            font-size: 0.875rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .status {
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

        .order-details {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .btn {
            background-color: #5e2ced;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #4a23b9;
        }

        .order-row {
            border-bottom: 2px solid #e5e7eb;
        }

        .order-row:last-child {
            border-bottom: none;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .user-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="productdisplay.php" class="logo" style="display: flex; align-items: center; text-decoration: none; font-size: 24px; color: #333; font-weight: 600;">
        <img src="/comtech/assets/img/logo.png" alt="Company Logo" style="width: 40px; height: 40px; margin-right: 10px;">
        Comtech
    </a>
    <div class="navbar-nav">
        <a href="productdisplay.php"><i class="fas fa-shopping-bag"></i> Shop</a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
        <a href="order-verification.php"><i class="fas fa-search"></i> Verify Order</a>
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
                        <div class="stat-value">NPR <?= number_format($total_spent, 0) ?></div>
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
                                    <th>Order Details</th>
                                    <th>Confirmation Code</th>
                                    <th>Payment Status</th>
                                    <th>Delivery Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="order-row">
                                        <td>
                                            <div><strong>Order #<?= $order['order_id'] ?></strong></div>
                                            <div style="font-size: 0.875rem; color: #666;">
                                                <?= date("M j, Y g:i A", strtotime($order['order_date'])) ?>
                                            </div>
                                            <div style="font-size: 0.875rem; color: #666;">
                                                <?= htmlspecialchars($order['method']) ?>
                                            </div>
                                            <div class="order-details">
                                                <?php foreach ($order['items'] as $item): ?>
                                                    <div><?= htmlspecialchars($item['product_name']) ?> (<?= $item['quantity'] ?>x)</div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($order['confirmation_code']): ?>
                                                <span class="confirmation-code"><?= htmlspecialchars($order['confirmation_code']) ?></span>
                                            <?php else: ?>
                                                <span style="color: #9ca3af;">Not generated</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status payment-<?= $order['payment_status'] ?>">
                                                <?= ucfirst($order['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status delivery-<?= $order['delivery_status'] ?>">
                                                <?= ucfirst($order['delivery_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>NPR <?= number_format($order['total_price'], 2) ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="total-row">
                                    <td colspan="4"><strong>Total Spent</strong></td>
                                    <td><strong>NPR <?= number_format($total_spent, 2) ?></strong></td>
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
