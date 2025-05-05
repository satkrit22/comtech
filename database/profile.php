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
    <title>Your Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
        }

        /* Navbar */
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .navbar a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .profile-info, .order-history {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .profile-info h2, .order-history h2 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .profile-info p {
            font-size: 16px;
            color: #444;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 12px;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total td {
            font-weight: bold;
            background-color: #f1f1f1;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                margin: 10px 0 0 0;
            }

            .container {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div><strong>Comtech</strong></div>
    <div>
        <a href="productdisplay.php">Shop</a>
        <a href="signout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Your Profile</h1>

    <div class="profile-info">
        <h2>Profile Information</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
    </div>

    <div class="order-history">
        <h2>Your Order History</h2>
        <?php if (count($orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
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
                        echo "<tr>";
                        echo "<td>{$order['order_id']}</td>";
                        echo "<td>" . date("F j, Y", strtotime($order['order_date'])) . "</td>";
                        echo "<td>" . htmlspecialchars($order['product_name']) . "</td>";
                        echo "<td>{$order['quantity']}</td>";
                        echo "<td>NPR. " . number_format($order['subtotal'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($order['status']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                    <tr class="total">
                        <td colspan="4">Total Spent</td>
                        <td colspan="2">NPR. <?= number_format($total_spent, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not placed any orders yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
