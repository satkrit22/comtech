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

// Assuming you have already retrieved the email and password from POST request
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare the query to fetch user details based on email
$sql = "SELECT * FROM users WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify the password using password_verify
    if (password_verify($password, $user['password'])) {
        // Password is correct, start session and redirect to the dashboard or products page
        session_start();
        $_SESSION['email'] = $user['Email'];
        header('Location:shop.html'); // Redirect to the user's dashboard
    } else {
        // Invalid password
        echo "<script>alert('Incorrect password. Please try again.');</script>";
    }
} else {
    // Email not found
        
    echo "<script>alert('Email does not exist. Please sign up.'); window.location.href = 'signup.html';</script>";
}

$stmt->close();
mysqli_close($conn);
?>
