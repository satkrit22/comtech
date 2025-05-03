<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

// Establishing connection to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
)";

// Execute the query and check for success
if (mysqli_query($conn, $query)) {
    echo "<script>alert('Categories table created successfully'); window.location.href = '/comtech/home.html';</script>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
