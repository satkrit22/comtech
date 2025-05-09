<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if category ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: categories.php');
    exit();
}

$category_id = (int)$_GET['id'];

// Check if category exists
$check_query = "SELECT id FROM categories WHERE id = $category_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    $_SESSION['alert'] = [
        'message' => 'Category not found.',
        'type' => 'danger'
    ];
    header('Location: categories.php');
    exit();
}

// Check if category has products
$products_query = "SELECT COUNT(*) as count FROM products WHERE category_id = $category_id";
$products_result = mysqli_query($conn, $products_query);
$products_count = mysqli_fetch_assoc($products_result)['count'];

if ($products_count > 0) {
    $_SESSION['alert'] = [
        'message' => 'Cannot delete category. It has ' . $products_count . ' products associated with it.',
        'type' => 'danger'
    ];
    header('Location: categories.php');
    exit();
}

// Delete category
$query = "DELETE FROM categories WHERE id = $category_id";

if (mysqli_query($conn, $query)) {
    $_SESSION['alert'] = [
        'message' => 'Category deleted successfully.',
        'type' => 'success'
    ];
} else {
    $_SESSION['alert'] = [
        'message' => 'Error deleting category: ' . mysqli_error($conn),
        'type' => 'danger'
    ];
}

header('Location: categories.php');
exit();
?>