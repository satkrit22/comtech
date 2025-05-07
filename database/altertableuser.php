<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "ALTER TABLE users
          ADD COLUMN reset_token VARCHAR(255) NULL,
          ADD COLUMN reset_token_expiry DATETIME NULL";

if (mysqli_query($conn, $query)) {
    echo "Columns added successfully!";
} else {
    echo "Error adding columns: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
