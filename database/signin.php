<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$email = $_POST['email'];
$password = $_POST['password'];
$sql = "SELECT * FROM users WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify the password using password_verify
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; 
        header('Location:/comtech/database/productdisplay.php'); 
        exit();
    } else {
        // Invalid password
        echo "<script>alert('Incorrect password. Please try again.');</script>";
    }
} else {
    // Email not found
    echo "<script>alert('Email does not exist. Please sign up.'); window.location.href = '/comtech/database/login.php';</script>";
}

$stmt->close();
mysqli_close($conn);
?>
