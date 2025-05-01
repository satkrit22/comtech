<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Inserting sample categories
$query = "INSERT INTO categories (name) VALUES 
    ('Electronics'),
    ('Fashion'),
    ('Home Appliances'),
    ('Books')";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Categories inserted successfully'); window.location.href = '/comtech/home.html';</script>";
} else {
    echo "Error inserting categories: " . mysqli_error($conn);
}
?>
