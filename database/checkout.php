<?php
session_start();
require 'connect.php';  // $conn defined here (MySQLi connection)

$user_id = $_SESSION['user_id'];

// Start transaction
$conn->begin_transaction();

// 1. Fetch cart items
$sql = "
    SELECT cart.product_id, cart.quantity, products.price 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

// 2. Calculate total
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// 3. Insert into orders
$stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// 4. Insert into order_items
$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $stmt->execute();
}
$stmt->close();

// 5. Delete cart items
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// 6. Commit transaction
$conn->commit();

echo "Order placed successfully!";

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    /* Reset & Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7fa;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Container */
.confirmation-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

/* Card */
.confirmation-card {
    background: #ffffff;
    padding: 40px 60px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 400px;
    width: 100%;
}

/* Typography */
.confirmation-card h1 {
    font-size: 2em;
    color: #2d3436;
    margin-bottom: 10px;
}

.confirmation-card p {
    font-size: 1.1em;
    color: #636e72;
    margin-bottom: 30px;
}

/* Button */
.btn {
    text-decoration: none;
    background: #0984e3;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #74b9ff;
}
</style>
<body>
    <div class="confirmation-container">
        <div class="confirmation-card">
            <h1>Thank You!</h1>
            <p>Your order has been placed successfully.</p>
            <a href="shop.php" class="btn">Continue Shopping</a>
        </div>
    </div>
</body>
</html>
