<?php
session_start();

// Store and then clear the transaction message
$transactionMsg = $_SESSION['transaction_msg'] ?? '';
unset($_SESSION['transaction_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Message - Comtech</title>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-nav {
            display: flex;
            gap: 1.5rem;
        }
        .navbar-nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .message-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .message-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #5e2ced;
        }
        .message-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .message-text {
            color: #666;
            margin-bottom: 1.5rem;
        }
        .btn {
            background-color: #5e2ced;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #4a23b9;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #5e2ced;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin: 0 auto 1.5rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="productdisplay.php" class="logo" style="display: flex; align-items: center; text-decoration: none; font-size: 24px; color: #333; font-weight: 600;">
        <img src="/comtech/assets/img/logo.png" alt="Company Logo" style="width: 40px; height: 40px; margin-right: 10px;">
        Comtech
    </a>
    <div class="navbar-nav">
        <a href="productdisplay.php"><i class="fas fa-shopping-bag"></i> Shop</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="signout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <div class="message-card" id="message-card">
        <div class="loader" id="loader"></div>
        <div class="message-icon"><i class="fas fa-spinner fa-spin"></i></div>
        <div class="message-title">Processing Your Request</div>
        <div class="message-text">Please wait while we process your transaction...</div>
    </div>
</div>

<?php
// Display the transaction message (already wrapped in a <script> tag)
if ($transactionMsg) {
    echo $transactionMsg;
} else {
    echo '<script>
    Swal.fire({
        icon: "info",
        title: "No message found",
        text: "Redirecting you to the homepage...",
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = "productdisplay.php";
    });
    </script>';
}
?>

<script>
    // Hide the loading message when SweetAlert appears
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new MutationObserver(function(mutations) {
            if (document.querySelector('.swal2-container')) {
                document.getElementById('message-card').style.display = 'none';
                observer.disconnect();
            }
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
        
        // Fallback: If no SweetAlert appears within 3 seconds, redirect
        setTimeout(function() {
            if (!document.querySelector('.swal2-container')) {
                window.location.href = "productdisplay.php";
            }
        }, 3000);
    });
</script>

</body>
</html>