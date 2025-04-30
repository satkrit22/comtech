<?php
session_start();
require 'config/db.php';

$cart = $pdo->prepare("
    SELECT product_id, quantity, price 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$cart->execute([$_SESSION['user_id']]);
$items = $cart->fetchAll();

$total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $total]);
$order_id = $pdo->lastInsertId();

foreach ($items as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
}

$pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$_SESSION['user_id']]);
$pdo->commit();

echo "Order placed successfully!";

?>