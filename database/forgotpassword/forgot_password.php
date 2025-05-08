<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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

        /* Page Body */
        body {
            background-color: #f2f4f7;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        /* Container for the form */
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Heading styles */
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 500;
        }

        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Input field styles */
        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus {
            border-color: #2b79ff;
            outline: none;
        }

        /* Button styles */
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

        /* Link styles */
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

        /* Responsive design */
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

            input[type="email"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Forgot Password</h1>
        
        <?php
        session_start();
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<p class="error">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="forgot_password_process.php" method="POST">
            <input type="email" placeholder="Enter your email" name="email" required />
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="login.php">Remembered your password? Go back to login</a>
    </div>

</body>
</html>
