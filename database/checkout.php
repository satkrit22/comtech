<?php
session_start();
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch user details from the users table
$stmt = $conn->prepare("SELECT name, phone, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_details = $user_result->fetch_assoc();
$stmt->close();

// Handle order submission
if (isset($_POST['order'])) {
    $name   = $conn->real_escape_string($_POST['name']);
    $number = $conn->real_escape_string($_POST['number']);
    $email  = $conn->real_escape_string($_POST['email']);
    $method = $conn->real_escape_string($_POST['method']);
    $street  = $conn->real_escape_string($_POST['street']);
    $city    = $conn->real_escape_string($_POST['city']);
    $state   = $conn->real_escape_string($_POST['state']);
    $country = $conn->real_escape_string($_POST['country']);

    $address = "Address: $street, $city, $state, $country";

    // Fetch cart items with stock
    $stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (count($cart_items) > 0) {
        $total_products = '';
        $grand_total = 0;
        $out_of_stock = false;

        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['stock']) {
                $out_of_stock = true;
                $message = "Not enough stock for: " . htmlspecialchars($item['name']);
                break;
            }
            $total_products .= $item['name'] . ' (' . $item['price'] . ' x ' . $item['quantity'] . ') - ';
            $grand_total += ($item['price'] * $item['quantity']);
        }

        // Add extra charge based on province (if not Bagmati)
        if ($state !== 'Bagmati Province') {
            $grand_total += 150;  // Add extra charge for non-Bagmati Province
        }

        if (!$out_of_stock) {
            // Insert order into the orders table
            $stmt = $conn->prepare("INSERT INTO orders (user_id, name, number, email, method, address, total_products, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssi", $user_id, $name, $number, $email, $method, $address, $total_products, $grand_total);
            $stmt->execute();
            $order_id = $conn->insert_id;  // Get the last inserted order ID
            $stmt->close();

            // Insert each item into the order_items table
            foreach ($cart_items as $item) {
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $stmt->execute();
                $stmt->close();
            }

            // Update stock for each product
            foreach ($cart_items as $item) {
                $new_stock = $item['stock'] - $item['quantity'];
                $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
                $stmt->bind_param("ii", $new_stock, $item['product_id']);
                $stmt->execute();
                $stmt->close();
            }

            // Clear cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            $message = 'Order placed successfully!';
        }
    } else {
        $message = 'Your cart is empty.';
    }

    echo "<script>alert('$message'); window.location='productdisplay.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - Comtech</title>
<link rel="stylesheet" href="css/style.css">
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f0f2f5;
        padding: 40px;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #222;
        margin-bottom: 20px;
    }

    .checkout-container {
        max-width: 900px;
        margin: auto;
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h3 {
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 20px;
        color: #007bff;
        border-bottom: 2px solid #007bff;
        padding-bottom: 5px;
    }

    .display-orders p {
        padding: 10px 0;
        border-bottom: 1px solid #eaeaea;
        display: flex;
        justify-content: space-between;
        font-size: 15px;
    }

    .grand-total {
        text-align: right;
        font-size: 18px;
        font-weight: bold;
        margin-top: 15px;
        color: #333;
    }

    .inputBox {
        margin-bottom: 20px;
    }

    .inputBox span {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #555;
    }

    .inputBox input,
    .inputBox select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        transition: border-color 0.3s, box-shadow 0.3s;
        font-size: 14px;
    }

    .inputBox input:focus,
    .inputBox select:focus {
        border-color: #007bff;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .btn {
        display: inline-block;
        width: 100%;
        padding: 12px 0;
        background: #007bff;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 10px;
    }

    .btn:hover {
        background: #0056b3;
    }

    .disabled {
        background: #999 !important;
        cursor: not-allowed !important;
    }

    .empty {
        text-align: center;
        color: #c0392b;
        margin: 20px 0;
        font-weight: bold;
        font-size: 16px;
    }

    @media (max-width: 600px) {
        .checkout-container {
            padding: 20px;
        }

        h3 {
            font-size: 18px;
        }

        .grand-total {
            font-size: 16px;
        }

        .btn {
            font-size: 15px;
        }
    }

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.querySelector('select[name="state"]');
    const grandTotalElement = document.getElementById('grand-total-value');
    const deliveryChargeLine = document.getElementById('delivery-charge-line');
    const extraCharge = 150;
    let initialGrandTotal = <?= $grand_total ?>;

    function updateGrandTotal() {
        const selectedProvince = provinceSelect.value;
        let total = initialGrandTotal;

        if (selectedProvince !== 'Bagmati Province') {
            total += extraCharge;
            deliveryChargeLine.style.display = 'flex';
            grandTotalElement.textContent = 'NPR. ' + total.toFixed(2);
        } else {
            deliveryChargeLine.style.display = 'none';
            grandTotalElement.textContent = 'NPR. ' + total.toFixed(2);
        }
    }

    provinceSelect.addEventListener('change', updateGrandTotal);
    updateGrandTotal();
});
</script>
</head>
<body>

<div class="checkout-container">
    <h2>Checkout</h2>

    <form action="" method="POST">
        <h3>Your Information</h3>
        <div class="inputBox">
            <span>Name</span>
            <input type="text" name="name" value="<?= htmlspecialchars($user_details['name']) ?>" required>
        </div>
        <div class="inputBox">
            <span>Phone Number</span>
            <input type="text" name="number" value="<?= htmlspecialchars($user_details['phone']) ?>" required>
        </div>
        <div class="inputBox">
            <span>Email</span>
            <input type="email" name="email" value="<?= htmlspecialchars($user_details['email']) ?>" required>
        </div>
        <div class="inputBox">
            <span>Payment Method</span>
            <select name="method" required>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
            </select>
        </div>

        <h3>Shipping Address</h3>
        <div class="inputBox">
            <span>Street</span>
            <input type="text" name="street" required>
        </div>
        <div class="inputBox">
            <span>City</span>
            <input type="text" name="city" required>
        </div>
        <div class="inputBox">
            <span>State</span>
            <select name="state" required>
                <option value="Bagmati Province">Bagmati Province</option>
                <!-- Add other provinces here -->
            </select>
        </div>
        <div class="inputBox">
            <span>Country</span>
            <input type="text" name="country" required>
        </div>

        <h3>Order Summary</h3>
        <div class="display-orders">
            <?php
            // Fetch and display cart items as an order summary
            $stmt = $conn->prepare("SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_result = $stmt->get_result();
            $total = 0;
            while ($item = $cart_result->fetch_assoc()) {
                echo "<p>" . htmlspecialchars($item['name']) . " <span>(" . number_format($item['price'], 2) . " x " . $item['quantity'] . ")</span></p>";
                $total += ($item['price'] * $item['quantity']);
            }
            $stmt->close();
            ?>
        </div>

        <div class="grand-total">
            Total: <span id="grand-total-value"><?= number_format($total, 2) ?></span>
        </div>

        <div id="delivery-charge-line" style="display: none; flex-direction: row;">
            <div class="grand-total" style="margin-top: 10px;">Delivery charge: NPR 150</div>
        </div>

        <button class="btn" name="order">Place Order</button>
    </form>
</div>

</body>
</html>
