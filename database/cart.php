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
    <link rel="stylesheet" href="cartcss.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-laptop-code"></i>
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
