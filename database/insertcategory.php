<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);
$query = "INSERT INTO categories (name) VALUES 
    ('ALL'),
    ('Link PC'),
    ('Computer Accessories'),
    ('Laptop & Accessories')";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Categories inserted successfully'); window.location.href = '/comtech/home.html';</script>";
} else {
    echo "Error inserting categories: " . mysqli_error($conn);
}
?>
