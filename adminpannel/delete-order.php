<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['alert'] = [
        'message' => 'Order ID is required.',
        'type' => 'danger'
    ];
    header('Location: orders.php');
    exit();
}

$order_id = (int)$_GET['id'];

// Check if order exists
$check_query = "SELECT id FROM orders WHERE id = $order_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    $_SESSION['alert'] = [
        'message' => 'Order not found.',
        'type' => 'danger'
    ];
    header('Location: orders.php');
    exit();
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Delete order items first (if they exist)
    $delete_items_query = "DELETE FROM order_items WHERE order_id = $order_id";
    mysqli_query($conn, $delete_items_query);
    
    // Delete the order
    $delete_order_query = "DELETE FROM orders WHERE id = $order_id";
    if (!mysqli_query($conn, $delete_order_query)) {
        throw new Exception("Error deleting order: " . mysqli_error($conn));
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['alert'] = [
        'message' => 'Order deleted successfully.',
        'type' => 'success'
    ];
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    $_SESSION['alert'] = [
        'message' => $e->getMessage(),
        'type' => 'danger'
    ];
}

header('Location: orders.php');
exit();
?>