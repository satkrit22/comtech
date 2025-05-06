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
    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0062cc;
            --primary-light: #e6f2ff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --body-bg: #f4f6f8;
            --border-radius: 12px;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: var(--body-bg);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary), #0099ff);
            color: var(--white);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
        }

        .navbar-nav a {
            color: var(--white);
            margin-left: 25px;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: var(--transition);
            padding: 8px 15px;
            border-radius: 50px;
        }

        .navbar-nav a i {
            margin-right: 8px;
        }

        .navbar-nav a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .navbar-nav a.logout {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
        }

        .navbar-nav a.logout:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1140px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--secondary);
            font-size: 1.1rem;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), #0099ff);
            border-radius: 2px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        .card-header h2 i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 1.4rem;
        }

        .card-body {
            padding: 25px;
        }

        .profile-info .user-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 25px;
            border: 5px solid var(--white);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
            overflow: hidden;
        }

        .profile-info .user-avatar i {
            font-size: 60px;
            color: var(--primary);
        }

        .profile-info .user-details {
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-info .user-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .profile-info .user-email {
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .profile-info .user-phone {
            color: var(--secondary);
        }

        .profile-info .user-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 25px;
        }

        .stat-card {
            background-color: var(--light);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            background-color: var(--primary-light);
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            color: var(--secondary);
            font-weight: 500;
        }

        .order-history .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 15px;
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        tr:last-child {
            border-bottom: none;
        }

        td {
            color: var(--dark);
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-delivered {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .status-processing {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .status-pending {
            background-color: rgba(108, 117, 125, 0.1);
            color: var(--secondary);
        }

        .total-row {
            background-color: var(--primary-light);
            font-weight: 700;
        }

        .total-row td {
            color: var(--primary-dark);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary);
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-state p {
            color: var(--secondary);
            font-size: 1.1rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn i {
            margin-right: 5px;
        }

        .edit-profile {
            margin-top: 20px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .navbar-nav a {
                margin-left: 15px;
                padding: 6px 12px;
                font-size: 0.9rem;
            }

            .container {
                padding: 0 15px;
                margin: 30px auto;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }

            .card-header {
                padding: 15px 20px;
            }

            .card-body {
                padding: 20px;
            }

            th, td {
                padding: 12px 10px;
                font-size: 0.9rem;
            }

            .profile-info .user-avatar {
                width: 100px;
                height: 100px;
            }

            .profile-info .user-avatar i {
                font-size: 50px;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                flex-direction: column;
                padding: 15px;
            }

            .navbar-brand {
                margin-bottom: 10px;
            }

            .navbar-nav {
                width: 100%;
                justify-content: space-around;
            }

            .navbar-nav a {
                margin: 0;
                font-size: 0.8rem;
                padding: 6px 10px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .page-header p {
                font-size: 0.9rem;
            }

            .profile-info .user-stats {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 10px 8px;
                font-size: 0.8rem;
            }

            .status {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="index.php" class="navbar-brand">
        <i class="fas fa-laptop-code"></i>
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