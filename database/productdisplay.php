<?php
require 'config/db.php';

$products = $pdo->query("SELECT * FROM products")->fetchAll();
foreach ($products as $product) {
    echo "<div>";
    echo "<h3>{$product['name']}</h3>";
    echo "<p>\${$product['price']}</p>";
    echo "<a href='cart.php?add={$product['id']}'>Add to Cart</a>";
    echo "</div>";
}
?>