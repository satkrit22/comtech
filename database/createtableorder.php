<?php
    $servername="localhost";
    $username="root";
    $password = "";
    $dbname = "comtech";

    $conn = mysqli_connect($servername,$username,$password,$dbname);

    $query = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total INT NOT NULL,
    status ENUM('Pending', 'Processing', 'Completed') DEFAULT 'Pending' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id))"; 
if(mysqli_query($conn,$query)){
        echo "<script>alert('Data successfully enterd'); window.location.href = '/comtech/home.html'; </script>";
                }
?>