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
        if ($state === 'Other') {
            $grand_total += 200; 
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

// Fetch and calculate cart items for display
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();
$cart_items = [];
$total = 0;
$item_count = 0;

while ($item = $cart_result->fetch_assoc()) {
    $cart_items[] = $item;
    $total += ($item['price'] * $item['quantity']);
    $item_count += $item['quantity'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Comtech</title>
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
            --input-radius: 8px;
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
            padding: 0;
            min-height: 100vh;
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

        .container {
            max-width: 1200px;
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

        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-light);
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            margin: 0;
        }

        .card-header h2 i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 1.4rem;
        }

        .card-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e1e5eb;
            border-radius: var(--input-radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: var(--dark);
            transition: var(--transition);
            background-color: var(--light);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
            outline: none;
            background-color: var(--white);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e1e5eb;
            display: flex;
            align-items: center;
        }

        .form-section-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 1rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-lg {
            padding: 15px 30px;
            font-size: 1.1rem;
        }

        .order-summary {
            margin-bottom: 30px;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e1e5eb;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 15px;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .order-item-price {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .order-item-quantity {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .order-totals {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e5eb;
        }

        .order-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .order-total-row.grand-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e1e5eb;
        }

        .delivery-charge {
            color: var(--danger);
            font-weight: 500;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .payment-method {
            display: none;
        }

        .payment-method + label {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border: 2px solid #e1e5eb;
            border-radius: var(--input-radius);
            cursor: pointer;
            transition: var(--transition);
            flex: 1;
            min-width: 120px;
        }

        .payment-method + label i {
            margin-right: 10px;
            font-size: 1.2rem;
            color: var(--secondary);
            transition: var(--transition);
        }

        .payment-method:checked + label {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }

        .payment-method:checked + label i {
            color: var(--primary);
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-cart i {
            font-size: 4rem;
            color: var(--secondary);
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-cart p {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: var(--input-radius);
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-info {
            background-color: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: var(--warning);
            color: #856404;
        }

        .delivery-option {
            margin-top: 10px;
        }

        .delivery-option label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .delivery-option input[type="radio"] {
            margin-right: 10px;
        }

        .delivery-option .delivery-price {
            margin-left: auto;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .delivery-option .delivery-description {
            font-size: 0.85rem;
            color: var(--secondary);
            margin-left: 25px;
            margin-top: 2px;
        }

        @media (max-width: 992px) {
            .checkout-container {
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

            .payment-methods {
                flex-direction: column;
            }

            .payment-method + label {
                width: 100%;
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

            .btn-lg {
                padding: 12px 25px;
                font-size: 1rem;
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
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="signout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <div class="page-header">
        <h1>Checkout</h1>
        <p>Complete your purchase</p>
    </div>

    <?php if (count($cart_items) > 0): ?>
        <form action="" method="POST">
            <div class="checkout-container">
                <!-- Customer Information -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-circle"></i> Customer Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-id-card"></i> Personal Details
                            </div>
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user_details['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="number">Phone Number</label>
                                <input type="text" id="number" name="number" class="form-control" value="<?= htmlspecialchars($user_details['phone']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user_details['email']) ?>" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-map-marker-alt"></i> Shipping Address
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" class="form-control" value="Nepal" required>
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="street">Street Address</label>
                                <input type="text" id="street" name="street" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="state">Delivery Location</label>
                                <div class="delivery-option">
                                    <label>
                                        <input type="radio" name="state" value="Bagmati Province" checked>
                                        Inside Kathmandu Valley
                                        <span class="delivery-price">Free</span>
                                    </label>
                                    <div class="delivery-description">Delivery within 1-2 business days</div>
                                </div>
                                <div class="delivery-option">
                                    <label>
                                        <input type="radio" name="state" value="Other">
                                        Outside Kathmandu Valley
                                        <span class="delivery-price">NPR 200</span>
                                    </label>
                                    <div class="delivery-description">Delivery within 3-5 business days</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-credit-card"></i> Payment Method
                            </div>
                            <div class="payment-methods">
                                <input type="radio" id="cod" name="method" value="Cash on Delivery" class="payment-method" checked>
                                <label for="cod"><i class="fas fa-money-bill-wave"></i> Cash on Delivery</label>
                                
                                <input type="radio" id="esewa" name="method" value="Esewa" class="payment-method">
                                <label for="esewa"><i class="fas fa-wallet"></i> Esewa</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-shopping-cart"></i> Order Summary</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You have <?= $item_count ?> item(s) in your cart
                        </div>

                        <div class="order-summary">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="order-item">
                                    <div class="order-item-image">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="/comtech/assets/img/menu/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                        <?php else: ?>
                                            <i class="fas fa-box"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="order-item-details">
                                        <div class="order-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="order-item-price">
                                            NPR <?= number_format($item['price'], 2) ?>
                                            <span class="order-item-quantity">x<?= $item['quantity'] ?></span>
                                        </div>
                                    </div>
                                    <div class="order-item-total">
                                        NPR <?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-totals">
                            <div class="order-total-row">
                                <span>Subtotal</span>
                                <span>NPR <?= number_format($total, 2) ?></span>
                            </div>
                            <div class="order-total-row delivery-charge" id="delivery-charge-row" style="display: none;">
                                <span>Delivery Charge</span>
                                <span>NPR 200.00</span>
                            </div>
                            <div class="order-total-row grand-total">
                                <span>Total</span>
                                <span id="grand-total-value">NPR <?= number_format($total, 2) ?></span>
                            </div>
                        </div>

                        <button type="submit" name="order" class="btn btn-block btn-lg">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="card">
            <div class="card-body empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <a href="productdisplay.php" class="btn">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deliveryOptions = document.querySelectorAll('input[name="state"]');
    const deliveryChargeRow = document.getElementById('delivery-charge-row');
    const grandTotalElement = document.getElementById('grand-total-value');
    const extraCharge = 200;
    let initialGrandTotal = <?= $total ?>;

    function updateGrandTotal() {
        let selectedDeliveryOption;
        deliveryOptions.forEach(option => {
            if (option.checked) {
                selectedDeliveryOption = option.value;
            }
        });

        let total = initialGrandTotal;

        if (selectedDeliveryOption === 'Other') {
            total += extraCharge;
            deliveryChargeRow.style.display = 'flex';
        } else {
            deliveryChargeRow.style.display = 'none';
        }

        grandTotalElement.textContent = 'NPR ' + total.toFixed(2);
    }

    deliveryOptions.forEach(option => {
        option.addEventListener('change', updateGrandTotal);
    });

    // Initialize on page load
    updateGrandTotal();

    // Confirm order before submission
    document.querySelector('form').addEventListener('submit', function (e) {
        let total = initialGrandTotal;
        let selectedDeliveryOption;
        
        deliveryOptions.forEach(option => {
            if (option.checked) {
                selectedDeliveryOption = option.value;
            }
        });
        
        if (selectedDeliveryOption === 'Other') {
            total += extraCharge;
        }

        const confirmMsg = `Your total including delivery charge is NPR ${total.toFixed(2)}. Confirm order?`;
        if (!confirm(confirmMsg)) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>