<?php
require 'connectdatabase.php';

$result = $conn->query("SELECT * FROM products");

if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . htmlspecialchars($product['name']) . "</h3>";
        echo "<p>\$" . htmlspecialchars($product['price']) . "</p>";
        echo "<a href='cart.php?add=" . urlencode($product['id']) . "'>Add to Cart</a>";
        echo "</div>";
    }
} else {
    echo "No products found.";
}

$conn->close();
?>