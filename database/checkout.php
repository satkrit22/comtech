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
    <link rel="stylesheet" href="checkout.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="index.php" class="navbar-brand">
    <img src="/comtech/assets/img/logo.png" alt="Company Logo" style="height: 40px;">
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