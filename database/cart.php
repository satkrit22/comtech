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

// Remove single item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND id = ?");
    $stmt->bind_param("ii", $user_id, $cart_id);
    $stmt->execute();
    $stmt->close();
}

// Delete all
if (isset($_GET['delete_all'])) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: cart.php');
    exit();
}

// Update quantity
if (isset($_POST['update_qty'])) {
    $cart_id = intval($_POST['cart_id']);
    $qty = max(1, min(99, intval($_POST['qty'])));

    // Get product_id from cart
    $stmt = $conn->prepare("SELECT product_id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($product_id);
    if ($stmt->fetch()) {
        $stmt->close();

        // Get stock from products table
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($stock);
        if ($stmt->fetch()) {
            $stmt->close();

            // Check and adjust quantity
            if ($qty > $stock) {
                $qty = $stock;
                $_SESSION['message'] = "Quantity adjusted to available stock: $stock";
            }

            // Now update cart quantity
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $qty, $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: cart.php");
    exit();
}


// Fetch cart items with JOIN
$stmt = $conn->prepare("
    SELECT cart.id AS cart_id, cart.quantity, cart.product_id, products.name, products.price, products.image, products.stock 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate cart totals
$subtotal = 0;
$item_count = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}
$shipping = 0; // You can set shipping cost logic here
$total = $subtotal + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart | Comtech</title>
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

        /* Header & Navigation */
        .header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link i {
            font-size: 1.2rem;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
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

        /* Alert Message */
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border: 1px solid var(--warning);
            color: #856404;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Cart Layout */
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .cart-items, .cart-summary {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        /* Cart Items */
        .cart-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .cart-header .clear-cart {
            color: var(--danger);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: var(--transition);
        }

        .cart-header .clear-cart:hover {
            color: #c82333;
            text-decoration: underline;
        }

        .cart-body {
            padding: 0;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .cart-item:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 1rem;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.3rem;
            font-size: 1.1rem;
        }

        .cart-item-price {
            color: var(--primary);
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .cart-item-stock {
            font-size: 0.85rem;
            color: var(--secondary);
        }

        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid #e1e5eb;
            background-color: var(--white);
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .quantity-btn:hover {
            background-color: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary-light);
        }

        .quantity-input {
            width: 50px;
            height: 30px;
            border: 1px solid #e1e5eb;
            border-radius: 4px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--dark);
            padding: 0 0.5rem;
        }

        .quantity-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .remove-btn {
            color: var(--danger);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .remove-btn:hover {
            color: #c82333;
            transform: scale(1.1);
        }

        .cart-item-subtotal {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.1rem;
            text-align: right;
            min-width: 100px;
        }

        /* Cart Summary */
        .summary-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .summary-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-body {
            padding: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .summary-row:last-child {
            margin-bottom: 0;
        }

        .summary-label {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .summary-value {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .summary-total {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid rgba(0, 0, 0, 0.05);
        }

        .summary-total .summary-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .summary-total .summary-value {
            font-size: 1.3rem;
            color: var(--primary);
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--success);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 1.5rem;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.2);
        }

        .checkout-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
        }

        .checkout-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .continue-shopping:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 3rem 2rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
        }

        .empty-cart i {
            font-size: 4rem;
            color: var(--secondary);
            opacity: 0.3;
            margin-bottom: 1.5rem;
        }

        .empty-cart h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .shop-now-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .shop-now-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .cart-item-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
            }

            .cart-item-actions {
                width: 100%;
                justify-content: space-between;
            }

            .cart-item-subtotal {
                text-align: left;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }

            .cart-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .summary-total .summary-value {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
        <a href="index.php" class="logo" style="display: flex; align-items: center; text-decoration: none; font-size: 24px; color: #333; font-weight: 600;">
    <img src="/comtech/assets/img/logo.png" alt="Company Logo" style="width: 40px; height: 40px; margin-right: 10px;">
    Comtech
</a>
            
            <div class="nav-links">
                <a href="productdisplay.php" class="nav-link">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Shop</span>
                </a>
                
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
                
                <a href="signout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="page-header">
            <h1>Your Shopping Cart</h1>
            <p>Review and modify your items before checkout</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($_SESSION['message']) ?></span>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (count($cart_items) > 0): ?>
            <div class="cart-container">
                <!-- Cart Items -->
                <div class="cart-items">
                    <div class="cart-header">
                        <h2><i class="fas fa-shopping-cart"></i> Cart Items (<?= $item_count ?>)</h2>
                        <a href="cart.php?delete_all" class="clear-cart" onclick="return confirm('Are you sure you want to clear your cart?')">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </a>
                    </div>
                    <div class="cart-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <img src="/comtech/assets/img/menu/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="cart-item-details">
                                    <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="cart-item-price">NPR <?= number_format($item['price'], 2) ?></div>
                                    <div class="cart-item-stock">Available: <?= $item['stock'] ?> units</div>
                                </div>
                                <div class="cart-item-actions">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <input type="hidden" name="update_qty" value="1">
                                        <div class="quantity-control">
                                            <button type="button" class="quantity-btn decrease" onclick="decreaseQuantity(this)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="qty" class="quantity-input" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" onchange="this.form.submit()">
                                            <button type="button" class="quantity-btn increase" onclick="increaseQuantity(this, <?= $item['stock'] ?>)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <a href="cart.php?remove=<?= $item['cart_id'] ?>" class="remove-btn" onclick="return confirm('Remove this item from your cart?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <div class="cart-item-subtotal">
                                        NPR <?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="summary-header">
                        <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                    </div>
                    <div class="summary-body">
                        <div class="summary-row">
                            <span class="summary-label">Subtotal (<?= $item_count ?> items)</span>
                            <span class="summary-value">NPR <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="summary-row summary-total">
                            <span class="summary-label">Total</span>
                            <span class="summary-value">NPR <?= number_format($total, 2) ?></span>
                        </div>

                        <a href="checkout.php" class="checkout-btn">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                        <a href="productdisplay.php" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any products to your cart yet.</p>
                <a href="productdisplay.php" class="shop-now-btn">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function decreaseQuantity(button) {
            const input = button.parentNode.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
                input.form.submit();
            }
        }

        function increaseQuantity(button, maxStock) {
            const input = button.parentNode.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            if (currentValue < maxStock) {
                input.value = currentValue + 1;
                input.form.submit();
            }
        }
    </script>
</body>
</html>
