<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

// Get the POSTed data (ID of screenshot to delete)
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if ($id === null) {
    echo json_encode(['success' => false, 'message' => 'ID not provided']);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete screenshot record by ID
$sql = "DELETE FROM screenshots WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting screenshot']);
}

$stmt->close();
$conn->close();
?>
