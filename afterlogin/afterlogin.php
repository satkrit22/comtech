<?php
session_start();
require 'connectdatabase.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Our Store</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f9f9;
        padding: 20px;
        margin: 0;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
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

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: auto;
    }

    .product-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        text-align: center;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .product-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .product-card h3 {
        margin-top: 0;
        color: #333;
        font-size: 18px;
    }

    .product-card p {
        color: #666;
        margin: 8px 0;
        font-size: 16px;
    }

    .add-cart-btn {
        display: inline-block;
        padding: 8px 16px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.2s;
        margin-top: 8px;
    }

    .add-cart-btn:hover {
        background-color: #218838;
    }
</style>
</head>
<body>

<h1>Welcome to Our Store</h1>

<div class="nav-links">
    <a href="logout.php">Logout</a> | 
    <a href="cart.php">View Cart</a>
</div>

<div class="products-grid">
<?php
$result = $conn->query("SELECT * FROM products");

if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        echo "<div class='product-card'>";
        
        // Display image if exists
        if (!empty($product['image'])) {
            echo "<img src='" . htmlspecialchars($product['image']) . "' alt='" . htmlspecialchars($product['name']) . "'>";
        } else {
            echo "<img src='default-image.jpg' alt='No image'>"; // Optional fallback image
        }

        echo "<h3>" . htmlspecialchars($product['name']) . "</h3>";
        echo "<p>Price: \$" . htmlspecialchars($product['price']) . "</p>";
        echo "<a class='add-cart-btn' href='cart.php?add=" . urlencode($product['id']) . "'>Add to Cart</a>";
        echo "</div>";
    }
} else {
    echo "<p>No products found.</p>";
}

$conn->close();
?>
</div>

</body>
</html>
