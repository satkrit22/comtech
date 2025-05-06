<?php
session_start();

// Return 0 if user is not logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    echo json_encode(['count' => 0, 'error' => 'Database connection failed']);
    exit();
}

// Get cart count
$stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Return the count (or 0 if null)
echo json_encode(['count' => $row['count'] ?? 0]);

$stmt->close();
$conn->close();
?>
