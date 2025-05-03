<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add to Cart (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo "Please log in first.";
        exit();
    }

    $product_id = intval($_POST['product_id']);
    $user_id = intval($_SESSION['user_id']);

    // Check if the user exists in the 'users' table
    $user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $user_check->bind_param("i", $user_id);
    $user_check->execute();
    $user_check->store_result();

    if ($user_check->num_rows === 0) {
        echo "Invalid user. Please log in.";
        $user_check->close();
        exit();
    }

    // Check if the product exists in the 'products' table
    $product_check = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $product_check->bind_param("i", $product_id);
    $product_check->execute();
    $product_check->store_result();

    if ($product_check->num_rows === 0) {
        echo "Product not found.";
        $product_check->close();
        exit();
    }

    // Fetch product details
    $product_check->bind_result($product_id, $product_name, $product_price, $product_image);
    $product_check->fetch();
    $product_check->close();

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Product exists in the cart, so update the quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $update->bind_param("ii", $user_id, $product_id);
        $update->execute();
        $update->close();
    } else {
        // Product doesn't exist in the cart, so insert it
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, name, price, image, quantity) VALUES (?, ?, ?, ?, ?, 1)");
        $insert->bind_param("iisss", $user_id, $product_id, $product_name, $product_price, $product_image);
        $insert->execute();
        $insert->close();
    }

    $stmt->close();
    echo "Product added to cart!";
    exit();
}

// AJAX Product Search (Starts With Search)
if (isset($_GET['ajax'])) {
    $search = $_GET['search'] ?? '';
    $search = "$search%";

    // SQL query with case-insensitive 'starts with' search
    $stmt = $conn->prepare("SELECT * FROM products WHERE LOWER(name) LIKE LOWER(?)");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = "/comtech/assets/img/menu/" . htmlspecialchars($row['image']);
            echo '<div class="product-box">';
            echo "<img src='{$imagePath}' alt='" . htmlspecialchars($row['name']) . "'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "<p class='price'>NPR. " . number_format($row['price']) . "</p>";
            echo "<p class='stock'>Stock: " . intval($row['stock']) . "</p>";
            echo "<button onclick='addToCart(" . intval($row['id']) . ")'>Add to Cart</button>";
            echo "</div>";
        }
    } else {
        echo "<p>No products found</p>";
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location:/comtech/home.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ComTech Products</title>
<style>
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background-color: rgb(171, 171, 171);
}

.navbar {
  background-color: #f4f4f4;
  color: black;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar .logo {
  font-size: 24px;
  font-weight: bold;
}

.navbar .logo a {
  color: black;
  text-decoration: none;
}

.navbar .search-bar input {
  padding: 8px;
  border: 3px solid black;
  border-radius: 4px;
  outline: none;
}

.navbar .profile-cart {
  display: flex;
  align-items: center;
}

.navbar .profile-cart a {
  color: black;
  text-decoration: none;
  margin-left: 15px;
}

.navbar .profile-cart a:hover {
  text-decoration: underline;
}

.container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding: 20px;
}

.product-box {
  width: 220px;
  height: 350px;
  margin: 15px;
  padding: 20px;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Hover effect for product box */
.product-box:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  background-color: #f0f0f0;
}

/* Active effect for product box (use this class dynamically in JavaScript) */
.product-box.active {
  border: 2px solid #e67e22;
  background-color: #fff3e0;
}

/* Style for product box images */
.product-box img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
}

.product-box h3 {
  font-size: 18px;
  color: #333;
}

.product-box p {
  font-size: 14px;
  color: #777;
  margin: 5px 0;
}

.product-box .price {
  font-size: 18px;
  font-weight: bold;
  color: #e67e22;
}

.product-box .stock {
  font-size: 14px;
  color: #555;
}

.product-box button {
  padding: 10px 15px;
  background-color: #2ecc71;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.product-box button:hover {
  background-color: #27ae60;
}

</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">
        <a href="index.php">ComTech</a>
    </div>
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search products...">
    </div>
    <div class="profile-cart">
        <a href="profile.php">Profile</a>
        <a href="cart.php">Cart</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="?logout=true">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<!-- Product Display -->
<div class="container" id="product-container">
<?php
$query = "SELECT * FROM products";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = "/comtech/assets/img/menu/" . htmlspecialchars($row['image']);
        echo '<div class="product-box">';
        echo "<img src='{$imagePath}' alt='" . htmlspecialchars($row['name']) . "'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p class='price'>NPR. " . number_format($row['price']) . "</p>";
        echo "<p class='stock'>Stock: " . intval($row['stock']) . "</p>";
        echo "<button onclick='addToCart(" . intval($row['id']) . ")'>Add to Cart</button>";
        echo "</div>";
    }
} else {
    echo "<p>No products available</p>";
}
$conn->close();
?>
</div>

<script>
const input = document.getElementById('search-input');
input.addEventListener('input', () => {
    const query = input.value.trim();
    fetch(`productdisplay.php?ajax=1&search=${encodeURIComponent(query)}`)
    .then(res => res.text())
    .then(data => {
        document.getElementById('product-container').innerHTML = data;
    });
});

function addToCart(productId) {
    fetch("productdisplay.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `add_to_cart=1&product_id=${encodeURIComponent(productId)}`
    })
    .then(response => {
        if (response.status === 403) {
            alert("You must be logged in to add items to the cart.");
            window.location.href = "login.php";
        } else {
            return response.text();
        }
    })
    .then(data => {
        if (data) {
            alert(data);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Something went wrong.");
    });
}
document.querySelectorAll('.product-box').forEach(box => {
  box.addEventListener('click', () => {
    // Remove 'active' class from all product boxes
    document.querySelectorAll('.product-box').forEach(item => item.classList.remove('active'));
    // Add 'active' class to the clicked product box
    box.classList.add('active');
  });
});

</script>

</body>
</html>
