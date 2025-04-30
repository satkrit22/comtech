<?php
session_start();
require 'config/db.php';

if (isset($_GET['add'])) {
    $product_id = $_GET['add'];
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
}

$cart_items = $pdo->prepare("
    SELECT cart.id, products.name, products.price, cart.quantity 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$cart_items->execute([$_SESSION['user_id']]);
foreach ($cart_items as $item) {
    echo "<p>{$item['name']} - {$item['quantity']} x \${$item['price']}</p>";
}

?>