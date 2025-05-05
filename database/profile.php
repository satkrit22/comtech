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
    SELECT orders.id AS order_id, orders.order_date, products.name AS product_name, order_items.quantity, order_items.subtotal
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
    WHERE orders.user_id = ?
    ORDER BY orders.order_date DESC
");
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
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .profile-info, .order-history {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-info p {
            font-size: 16px;
            color: #555;
        }
        .order-history table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-history th, .order-history td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-history th {
            background-color: #007bff;
            color: white;
        }
        .order-history tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .order-history .total {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

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
                    echo "</tr>";
                }
                ?>
                <tr class="total">
                    <td colspan="4">Total Spent</td>
                    <td>NPR. <?= number_format($total_spent, 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
