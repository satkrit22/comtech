<?php
    $servername="localhost";
    $username="root";
    $password = "";
    $dbname = "comtech";

    $conn = mysqli_connect($servername,$username,$password,$dbname);

    $query = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"; 
if(mysqli_query($conn,$query)){
        echo "<script>alert('Data successfully enterd'); window.location.href = 'home.html'; </script>";
                }
?>