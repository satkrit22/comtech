<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all screenshot records from the database
$sql = "SELECT id, screenshot_name, uploaded_at FROM screenshots";
$result = $conn->query($sql);

$screenshots = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $screenshots[] = $row;
    }
    // Return data as JSON
    echo json_encode($screenshots);
} else {
    echo json_encode([]);
}

$conn->close();
?>
