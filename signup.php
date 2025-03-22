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
$name = htmlspecialchars(trim($_POST['name']));    // Sanitize name
$email = htmlspecialchars(trim($_POST['email']));    // Sanitize email
$password = $_POST['password']; // Password (we will hash it)

// Check if the email already exists
$sql_check = "SELECT * FROM users WHERE Email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // If email already exists, show an error message
    echo "<script>alert('Email already exists. Please choose a different email.');window.location.href = 'signup.html';</script>";
    exit(); // Stop the script here if the email is already registered
}

// Hash the password before storing it
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert data into the 'users' table
$sql = "INSERT INTO users (Name, Email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashed_password);

// Execute the query
if ($stmt->execute()) {
    echo "<script>alert('Signup successful!'); window.location.href = 'signup.html';</script>";
    exit(); // Ensure no further code is executed
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
mysqli_close($conn);
?>
