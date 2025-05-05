<?php
    $servername="localhost";
    $username="root";
    $password = "";
    $dbname = "comtech";

    $conn = mysqli_connect($servername,$username,$password,$dbname);

    $query = "CREATE TABLE order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) GENERATED ALWAYS AS (quantity * price) STORED,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE)";
    
    
if(mysqli_query($conn,$query)){
        echo "<script>alert('Data successfully enterd'); window.location.href = '/comtech/home.html'; </script>";
                }
?>