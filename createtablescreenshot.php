<?php
    $servername="localhost";
    $username="root";
    $password = "";
    $dbname = "comtech";

    $conn = mysqli_connect($servername,$username,$password,$dbname);

    $query = "CREATE TABLE screenshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screenshot_name VARCHAR(255),
    screenshot_data LONGBLOB,
    username VARCHAR(255),
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP);"; 
if(mysqli_query($conn,$query)){
        echo "<script>alert('Data successfully enterd');</script>";
                }
?>