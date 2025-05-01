<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

$query = "CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Categories table created successfully'); window.location.href = '/comtech/home.html';</script>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>
