<?php
session_start();
include('db.php');  
$sql = "
    SELECT o.id AS order_id, o.name, o.email, o.method, o.address, o.total_price, o.status, o.created_at,
           oi.product_id, oi.quantity, oi.price, oi.subtotal, p.name AS product_name
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    ORDER BY o.created_at DESC
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[$row['order_id']]['order_details'] = [
            'name' => $row['name'],
            'email' => $row['email'],
            'method' => $row['method'],
            'address' => $row['address'],
            'total_price' => $row['total_price'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
        ];
        $orders[$row['order_id']]['items'][] = [
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'subtotal' => $row['subtotal'],
        ];
    }
} else {
    $orders = [];
}
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Order status updated successfully!";
        header('Location: orderindex.php'); 
        exit();
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f0f2f5;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .logo {
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        .nav-menu {
            list-style: none;
        }
        .nav-menu li {
            margin: 15px 0;
        }
        .nav-menu a, .logout-btn {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .nav-menu a:hover, .logout-btn:hover {
            background-color: #34495e;
        }
        .nav-menu a.active, .logout-btn.active {
            background-color: #34495e;
        }
        .admin-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 200px;
            height: auto;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .status-btn {
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .pending-btn { background-color: #f39c12; }
        .shipped-btn { background-color: #2980b9; }
        .delivered-btn { background-color: #2ecc71; }
        .cancelled-btn { background-color: #e74c3c; }
        .status-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="logo">Admin Panel</div>
        <ul class="nav-menu">
            <li><a href="admincreateproduct.php">Create Product</a></li>
            <li><a href="" class="active">Order</a></li>
            <li><a href="usersadminaccess.php">Users</a></li>
            <li><a href="adminlogout.php" class="logout-btn" onclick="logout()">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="header" id="dashboard">
            <h1>Orders</h1>
            <div class="user-info">
                Welcome, Admin
            </div>
        </div>
        <div class="admin-container">
            <?php
            if (isset($_SESSION['message'])) {
                echo "<p style='color: green;'>".$_SESSION['message']."</p>";
                unset($_SESSION['message']);
            }
            if (isset($_SESSION['error'])) {
                echo "<p style='color: red;'>".$_SESSION['error']."</p>";
                unset($_SESSION['error']);
            }
            ?>

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Payment Method</th>
                        <th>Address</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Order Items</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($orders as $order_id => $order) {
                        echo "<tr>";
                        echo "<td>" . $order_id . "</td>";
                        echo "<td>" . $order['order_details']['name'] . "</td>";
                        echo "<td>" . $order['order_details']['email'] . "</td>";
                        echo "<td>" . $order['order_details']['method'] . "</td>";
                        echo "<td>" . $order['order_details']['address'] . "</td>";
                        echo "<td>" . $order['order_details']['total_price'] . "</td>";
                        echo "<td>" . $order['order_details']['status'] . "</td>";
                        echo "<td>" . $order['order_details']['created_at'] . "</td>";
                        echo "<td>";
                        foreach ($order['items'] as $item) {
                            echo $item['product_name'] . " (Qty: " . $item['quantity'] . ")<br>";
                        }
                        echo "</td>";
                        echo "<td>";
                        if ($order['order_details']['status'] == 'pending') {
                            echo "<form method='POST' style='display:inline;'>
                                    <input type='hidden' name='order_id' value='$order_id'>
                                    <input type='hidden' name='status' value='shipped'>
                                    <button type='submit' name='update_status' class='status-btn shipped-btn'>Mark as Shipped</button>
                                  </form>";
                        }
                        if ($order['order_details']['status'] == 'shipped') {
                            echo "<form method='POST' style='display:inline;'>
                                    <input type='hidden' name='order_id' value='$order_id'>
                                    <input type='hidden' name='status' value='delivered'>
                                    <button type='submit' name='update_status' class='status-btn delivered-btn'>Mark as Delivered</button>
                                  </form>";
                        }
                        if ($order['order_details']['status'] == 'pending' || $order['order_details']['status'] == 'shipped') {
                            echo "<form method='POST' style='display:inline;'>
                                    <input type='hidden' name='order_id' value='$order_id'>
                                    <input type='hidden' name='status' value='cancelled'>
                                    <button type='submit' name='update_status' class='status-btn cancelled-btn'>Cancel Order</button>
                                  </form>";
                        }
                        echo "</td>";

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
