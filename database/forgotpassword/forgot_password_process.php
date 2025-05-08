<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if the email exists in the database
    $sql = "SELECT id, Email FROM users WHERE Email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('MySQL prepare error: ' . mysqli_error($conn));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));
        $token_expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Store the token and expiry time in the database
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE Email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('MySQL prepare error: ' . mysqli_error($conn));
        }

        $stmt->bind_param("sss", $token, $token_expiry, $email);
        $stmt->execute();

        // Send the reset link to the user's email
        $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";

        $subject = "Password Reset Request";
        $message = "Hello,\n\nPlease click the following link to reset your password: $reset_link\n\nIf you did not request this, please ignore this email.";
        $headers = "From: noreply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['message'] = 'A password reset link has been sent to your email.';
            header('Location: forgot_password.php');
        } else {
            $_SESSION['error'] = 'Failed to send the reset email. Please try again later.';
            header('Location: forgot_password.php');
        }
    } else {
        $_SESSION['error'] = 'No account found with that email address.';
        header('Location: forgot_password.php');
    }

    $stmt->close();
}

mysqli_close($conn);
?>
