<?php
    $servername="localhost";
    $username="root";
    $password = "";

    $conn = mysqli_connect($servername,$username,$password);

    if(!$conn){
        die("Connection Error".mysqli_connect_error());   
    }
    else{
       echo"Successfully connected";
    }
?>