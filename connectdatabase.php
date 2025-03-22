<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

// Create a connection to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    // If the connection fails, output the error message and stop the script
    die("Connection failed: " . mysqli_connect_error());
} 

?>
