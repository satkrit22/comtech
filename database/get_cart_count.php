<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    echo json_encode(['count' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['count' => 0]);
    exit();
}

$stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(['count' => intval($result['total'])]);
