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
    SELECT cart.id AS cart_id, cart.quantity, products.name, products.price, products.image 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<?php
if (isset($_SESSION['message'])) {
    echo "<div class='alert-message'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<link rel="stylesheet" href="css/style.css">
<style>
body {
    font-family: Arial, sans-serif;
     background: #f5f5f5; 
     padding: 20px;
}
h1 {text-align:center;
     color: #333;}
.nav-links {
    text-align:center;
     margin-bottom:20px;
    }
.nav-links a {margin: 0 10px; 
    text-decoration:none;
     color:#007bff;
      font-weight:bold;}
.nav-links a:hover {
    text-decoration:underline;
}
table {width:90%;
     margin:auto; 
     border-collapse:collapse;
      background:white;
       border-radius:8px;
        box-shadow:0 2px 5px rgba(0,0,0,0.1);}
th, td {padding:12px;
     text-align:center;}
th {background-color:#007bff;
     color:white;}
tr:nth-child(even) {
    background-color:#f9f9f9;}
.product-img {width:50px; 
    height:50px; 
    border-radius:4px;
     object-fit:cover;}
input.qty {width:50px; 
    text-align:center;}
button {padding:5px 10px;}
.remove-btn {color:red; text-decoration:none;}
.remove-btn:hover {text-decoration:underline;}
.checkout-btn {display:inline-block; margin-top:20px; background:#28a745; color:white; padding:10px 20px; text-decoration:none; border-radius:4px;}
.checkout-btn:hover {background:#218838;}
.total-row td {
    font-weight:bold; background:#eee;
}
.alert-message {
    width: 90%;
    margin: 10px auto;
    padding: 10px;
    background-color: #ffeeba;
    border: 1px solid #ffc107;
    color: #856404;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}

</style>
</head>

<body>
<h1>Your Cart</h1>
<div class="nav-links">
  <a href="productdisplay.php">Continue Shopping</a> | 
  <a href="cart.php?delete_all" onclick="return confirm('Delete all items?')">Delete All</a> | 
  <a href="signout.php">Logout</a>
</div>

<?php
if (count($cart_items) > 0) {
    echo "<table>";
    echo "<tr><th>Image</th><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>";
    $total = 0;
    foreach ($cart_items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        echo "<tr>";
        echo "<td><img class='product-img' src='/comtech/assets/img/menu/" . htmlspecialchars($item['image']) . "'></td>";
        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td>NPR. " . number_format($item['price'], 2) . "</td>";
        echo "<td><form method='post'>
                <input type='hidden' name='cart_id' value='{$item['cart_id']}'>
                <input type='number' class='qty' name='qty' min='1' max='99' value='{$item['quantity']}' onchange='this.form.submit()'>
<input type='hidden' name='update_qty' value='1'>

              </form></td>";
        echo "<td>NPR. " . number_format($subtotal, 2) . "</td>";
        echo "<td><a class='remove-btn' href='cart.php?remove={$item['cart_id']}' onclick=\"return confirm('Remove item?')\">Remove</a></td>";
        echo "</tr>";
    }
    echo "<tr class='total-row'><td colspan='4'>Total</td><td colspan='2'>NPR. " . number_format($total, 2) . "</td></tr>";
    echo "</table>";
    echo "<div style='text-align:center;'><a class='checkout-btn' href='checkout.php'>Proceed to Checkout</a></div>";
} else {
    echo "<p style='text-align:center;'>Your cart is empty.</p>";
}
?>
</body>
</html>
