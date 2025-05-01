<?php
session_start();
require 'connectdatabase.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle removing item
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $conn->query("DELETE FROM cart WHERE user_id = " . intval($_SESSION['user_id']) . " AND product_id = " . intval($product_id));
}

// Fetch cart items along with stock
$sql = "
    SELECT products.id, products.name, products.price, products.stock, cart.quantity
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = " . intval($_SESSION['user_id']);

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f9f9;
        padding: 20px;
    }

    h1 {
        text-align: center;
        color: #333;
    }

    .nav-links {
        text-align: center;
        margin-bottom: 30px;
    }

    .nav-links a {
        margin: 0 10px;
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }

    .nav-links a:hover {
        text-decoration: underline;
    }

    table {
        width: 80%;
        margin: auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px 15px;
        text-align: center;
    }

    th {
        background-color: #007bff;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .remove-btn {
        color: #dc3545;
        text-decoration: none;
        font-weight: bold;
    }

    .remove-btn:hover {
        text-decoration: underline;
    }

    .warning {
        color: #dc3545;
        font-weight: bold;
        font-size: 0.9em;
    }

    .total-row td {
        font-weight: bold;
        background-color: #f1f1f1;
    }

    .checkout-btn {
        display: inline-block;
        margin-top: 20px;
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .checkout-btn:hover {
        background-color: #218838;
    }

    .disabled-btn {
        background-color: #6c757d;
        cursor: not-allowed;
    }

</style>
</head>
<body>

<h1>Your Cart</h1>

<div class="nav-links">
    <a href="products.php">Continue Shopping</a> | 
    <a href="logout.php">Logout</a>
</div>

<?php
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Product</th><th>Price</th><th>Quantity (In Stock)</th><th>Subtotal</th><th>Action</th></tr>";

    $total = 0;
    $stock_issue = false;

    while ($item = $result->fetch_assoc()) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td>\$" . number_format($item['price'], 2) . "</td>";
        
        echo "<td>";
        echo htmlspecialchars($item['quantity']) . " / " . htmlspecialchars($item['stock']);
        if ($item['quantity'] > $item['stock']) {
            echo " <span class='warning'>(Only " . htmlspecialchars($item['stock']) . " available)</span>";
            $stock_issue = true;
        }
        echo "</td>";

        echo "<td>\$" . number_format($subtotal, 2) . "</td>";
        echo "<td><a class='remove-btn' href='cart.php?remove=" . urlencode($item['id']) . "'>Remove</a></td>";
        echo "</tr>";
    }

    echo "<tr class='total-row'>";
    echo "<td colspan='3'>Total</td>";
    echo "<td colspan='2'>\$" . number_format($total, 2) . "</td>";
    echo "</tr>";
    echo "</table>";

    echo "<div style='text-align:center;'>";
    if ($stock_issue) {
        echo "<p class='warning'>Please adjust cart — some items exceed available stock!</p>";
        echo "<a class='checkout-btn disabled-btn' href='#' onclick='return false;'>Checkout Disabled</a>";
    } else {
        echo "<a class='checkout-btn' href='checkout.php'>Proceed to Checkout</a>";
    }
    echo "</div>";

} else {
    echo "<p style='text-align:center;'>Your cart is empty.</p>";
}

$conn->close();
?>

</body>
</html>
