<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password);
$sql = "CREATE DATABASE comtech";
$result=mysqli_query($conn,$sql);
if(!$result) {
  echo "Database error";
} else {
  echo " creating database: ";
}
?>