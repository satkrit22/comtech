<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

// Validate input
if (empty($username) || empty($password)) {
    header("Location: adminform.html?error=Please fill in all fields");
    exit;
}

// Prepare and execute query
$stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    
    if (password_verify($password, $admin['password_hash'])) {
        // Set session variables
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_logged_in'] = true;
        
        // Redirect to admin panel
        header("Location: index.php");
        exit;
    }
}

// If login fails
header("Location: adminform.html?error=Invalid credentials");
$stmt->close();
$conn->close();
?>