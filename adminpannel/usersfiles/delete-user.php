<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$user_id = (int)$_GET['id'];

// Check if user exists
$check_query = "SELECT id FROM users WHERE id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    $_SESSION['alert'] = [
        'message' => 'User not found.',
        'type' => 'danger'
    ];
    header('Location: users.php');
    exit();
}

// Delete user
$query = "DELETE FROM users WHERE id = $user_id";

if (mysqli_query($conn, $query)) {
    $_SESSION['alert'] = [
        'message' => 'User deleted successfully.',
        'type' => 'success'
    ];
} else {
    $_SESSION['alert'] = [
        'message' => 'Error deleting user: ' . mysqli_error($conn),
        'type' => 'danger'
    ];
}

header('Location: users.php');
exit();
?>