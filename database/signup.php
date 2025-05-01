<?php
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

// Get data from POST request and sanitize inputs
$name = htmlspecialchars(trim($_POST['Name']));
$email = htmlspecialchars(trim($_POST['Email']));
$phone = htmlspecialchars(trim($_POST['phone']));
$password = $_POST['password'];

// Check if the email already exists
$sql_check = "SELECT * FROM users WHERE Email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email already exists. Please choose a different email.'); window.location.href = '/comtech/signup.html';</script>";
    exit();
}

// Hash the password before storing it
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert data into the 'users' table
$sql = "INSERT INTO users (Name, Email, phone, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

if ($stmt->execute()) {
    echo "<script>alert('Signup successful!'); window.location.href = '/comtech/signup.html';</script>";
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
mysqli_close($conn);
?>
