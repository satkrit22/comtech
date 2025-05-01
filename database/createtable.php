<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

$query = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Phone VARCHAR(200) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Table created successfully'); window.location.href = '/comtech/home.html'; </script>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>
