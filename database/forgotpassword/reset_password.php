<?php
session_start();

// Ensure that the token is passed in the URL
if (!isset($_GET['token'])) {
    die("Invalid request. Token is missing.");
}

$token = $_GET['token'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verify token and its expiry
$sql = "SELECT id, reset_token_expiry FROM users WHERE reset_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $expiry = $user['reset_token_expiry'];

    // Check if token has expired
    if (strtotime($expiry) < time()) {
        $_SESSION['error'] = 'The reset token has expired. Please request a new one.';
        header('Location: forgot_password.php');
        exit();
    }

    // Allow the user to reset their password if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validate password input
        if (empty($_POST['password'])) {
            $_SESSION['error'] = 'Password cannot be empty.';
            header('Location: reset_password.php?token=' . $token);
            exit();
        }

        // Hash the new password
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Update the password in the database and clear the reset token
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $token);
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Your password has been successfully reset.';
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to reset the password. Please try again later.';
            header('Location: reset_password.php?token=' . $token);
            exit();
        }
    }
} else {
    $_SESSION['error'] = 'Invalid or expired reset token.';
    header('Location: forgot_password.php');
    exit();
}

$stmt->close();
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="assets/img/favicon.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* Resetting some default browser styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f2f4f7;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 500;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="password"]:focus {
            border-color: #2b79ff;
            outline: none;
        }

        button {
            background-color: #2b79ff;
            color: #fff;
            padding: 12px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #479aff;
        }

        a {
            text-decoration: none;
            color: #2b79ff;
            font-size: 14px;
            margin-top: 15px;
            display: block;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .message {
            color: green;
            margin-bottom: 15px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            button {
                font-size: 14px;
            }

            input[type="password"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Reset Your Password</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="error">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        ?>

        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <input type="password" placeholder="New Password" name="password" required />
            <button type="submit">Reset Password</button>
        </form>
    </div>

</body>
</html>
