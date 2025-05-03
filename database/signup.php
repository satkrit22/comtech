<?php
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
if (
    !isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['password']) ||
    empty(trim($_POST['name'])) ||
    empty(trim($_POST['email'])) ||
    empty(trim($_POST['phone'])) ||
    empty(trim($_POST['password']))
) {
    die("All fields are required.");
}

$name = htmlspecialchars(trim($_POST['name']));
$email = htmlspecialchars(trim($_POST['email']));
$phone = htmlspecialchars(trim($_POST['phone']));
$password = $_POST['password'];  

// Check if email already exists
$sql_check = "SELECT * FROM users WHERE Email = ?";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    die("Prepare failed: " . $conn->error);
}

$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result && $result->num_rows > 0) {
    echo "<script>alert('Email already exists. Please choose a different email.'); window.location.href = '/comtech/signup.html';</script>";
    exit();
}
$stmt_check->close();

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$sql_insert = "INSERT INTO users (Name, Email, Phone, password) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);

if (!$stmt_insert) {
    die("Insert prepare failed: " . $conn->error);
}

$stmt_insert->bind_param("ssss", $name, $email, $phone, $hashed_password);

if ($stmt_insert->execute()) {
    echo "<script>alert('Signup successful!'); window.location.href = '/comtech/database/login.php';</script>";
} else {
    echo "Error inserting user: " . $stmt_insert->error;
}

$stmt_insert->close();
$conn->close();
?>
