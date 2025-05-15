<?php
session_start();
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$token = isset($_GET['token']) ? $_GET['token'] : '';
$valid_token = false;
$message = '';
$message_type = '';
$user_id = null;

// Check if token exists and is valid
if (!empty($token)) {
    $stmt = $conn->prepare("SELECT pr.user_id, u.email, u.name 
                           FROM password_resets pr 
                           JOIN users u ON pr.user_id = u.id 
                           WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $valid_token = true;
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $user_email = $row['email'];
        $user_name = $row['name'];
    } else {
        $message = "Invalid or expired password reset link. Please request a new one.";
        $message_type = "error";
    }
    $stmt->close();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if (empty($password) || empty($confirm_password)) {
        $message = "Both password fields are required.";
        $message_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $message_type = "error";
    } else {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            // Mark token as used
            $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            
            $message = "Your password has been successfully reset. You can now <a href='login.php'>login</a> with your new password.";
            $message_type = "success";
            $valid_token = false; // Hide the form
            
            // Send confirmation email
            $to = $user_email;
            $subject = "Comtech - Password Reset Successful";
            
            $message_body = "
            <html>
            <head>
                <title>Password Reset Successful</title>
            </head>
            <body>
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                    <h2 style='color: #2b79ff;'>Password Reset Successful</h2>
                    <p>Hello $user_name,</p>
                    <p>Your password for your Comtech account has been successfully reset.</p>
                    <p>If you did not make this change, please contact our support team immediately.</p>
                    <p>Regards,<br>Comtech Team</p>
                </div>
            </body>
            </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: Comtech <noreply@comtech.com>" . "\r\n";
            
            mail($to, $subject, $message_body, $headers);
        } else {
            $message = "An error occurred. Please try again later.";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Comtech</title>
    <link href="assets/img/favicon.png" rel="icon">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: -20px 0 50px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
            margin-bottom: 20px;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        a {
            color: #2b79ff;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        a:hover {
            text-decoration: underline;
        }

        button {
            border-radius: 20px;
            border: 1px solid #2b83ff;
            background-color: #2b79ff;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }

        button:hover {
            background-color: #479aff;
            transform: scale(1.05);
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 500px;
            max-width: 100%;
            min-height: 400px;
            display: flex;
            align-items: center;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 100%;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .password-requirements {
            text-align: left;
            width: 100%;
            margin-bottom: 15px;
            font-size: 12px;
            color: #666;
        }

        .password-requirements ul {
            padding-left: 20px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <div class="logo">
                <img src="/comtech/assets/img/logo.png" alt="Comtech Logo">
                <span class="logo-text">Comtech</span>
            </div>
            
            <h1>Reset Password</h1>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            
            <?php if ($valid_token): ?>
                <p>Please enter your new password below.</p>
                
                <div class="password-requirements">
                    <strong>Password requirements:</strong>
                    <ul>
                        <li>At least 8 characters long</li>
                        <li>Include both uppercase and lowercase letters</li>
                        <li>Include at least one number</li>
                    </ul>
                </div>
                
                <input type="password" name="password" id="password" placeholder="New Password" required minlength="8">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required minlength="8">
                <button type="submit">Reset Password</button>
            <?php else: ?>
                <?php if (empty($message)): ?>
                    <div class="alert alert-error">
                        Invalid or expired password reset link. Please request a new one.
                    </div>
                <?php endif; ?>
                <a href="forgot-password.php">Request a new password reset link</a>
            <?php endif; ?>
            
            <a href="login.php">Back to Login</a>
        </form>
    </div>

    <script>
        // Simple password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>