<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details to delete image file
$query = "SELECT image FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['alert'] = [
        'message' => 'Product not found.',
        'type' => 'danger'
    ];
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Delete product
$delete_query = "DELETE FROM products WHERE id = $product_id";

if (mysqli_query($conn, $delete_query)) {
    // Delete product image if it exists
    if (!empty($product['image']) && file_exists($product['image'])) {
        unlink($product['image']);
    }
    
    $_SESSION['alert'] = [
        'message' => 'Product deleted successfully.',
        'type' => 'success'
    ];
} else {
    $_SESSION['alert'] = [
        'message' => 'Error deleting product: ' . mysqli_error($conn),
        'type' => 'danger'
    ];
}

header('Location: products.php');
exit();
?>