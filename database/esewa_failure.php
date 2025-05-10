<?php
session_start();
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if we have transaction details in session
if (!isset($_SESSION['esewa_transaction'])) {
    header('Location: productdisplay.php');
    exit();
}

$transaction = $_SESSION['esewa_transaction'];
$order_id = $transaction['order_id'];

// Update order status to 'failed'
$update_query = "UPDATE orders SET status = 'failed' WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$stmt->close();

// Log the failed payment
$log_query = "INSERT INTO payment_logs (order_id, user_id, payment_method, transaction_id, amount, status) 
              VALUES (?, ?, 'eSewa', ?, ?, 'failed')";
$stmt = $conn->prepare($log_query);
$transaction_id = $transaction['transaction_uuid'];
$amount = $transaction['total_amount'];
$stmt->bind_param("iiss", $order_id, $user_id, $transaction_id, $amount);
$stmt->execute();
$stmt->close();

// Clear the transaction from session
unset($_SESSION['esewa_transaction']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Comtech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --danger: #e74c3c;
            --text-primary: #333;
            --text-secondary: #666;
            --bg-light: #f5f7fb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .failure-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        
        .failure-icon {
            width: 80px;
            height: 80px;
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        h1 {
            color: var(--danger);
            margin-bottom: 20px;
        }
        
        p {
            color: var(--text-secondary);
            margin-bottom: 15px;
        }
        
        .order-details {
            background-color: var(--bg-light);
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #3651d1;
        }
        
        .btn-danger {
            background-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-nav {
            display: flex;
            gap: 20px;
        }
        
        .navbar-nav a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .navbar-nav a:hover {
            color: var(--primary);
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
        <div class="failure-card">
            <div class="failure-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1>Payment Failed</h1>
            <p>We're sorry, but your payment could not be processed. Your order has been saved but is currently on hold.</p>
            
            <?php
            // Fetch order details
            $order_query = "SELECT * FROM orders WHERE id = ?";
            $stmt = $conn->prepare($order_query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $order_result = $stmt->get_result();
            $order = $order_result->fetch_assoc();
            $stmt->close();
            ?>
            
            <div class="order-details">
                <div class="detail-row">
                    <strong>Order ID:</strong>
                    <span>#<?php echo $order_id; ?></span>
                </div>
                <div class="detail-row">
                    <strong>Transaction ID:</strong>
                    <span><?php echo $transaction_id; ?></span>
                </div>
                <div class="detail-row">
                    <strong>Amount:</strong>
                    <span>NPR <?php echo number_format($amount, 2); ?></span>
                </div>
                <div class="detail-row">
                    <strong>Payment Method:</strong>
                    <span>eSewa</span>
                </div>
                <div class="detail-row">
                    <strong>Date:</strong>
                    <span><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                </div>
            </div>
            
            <p>Possible reasons for payment failure:</p>
            <ul style="text-align: left; max-width: 400px; margin: 0 auto; list-style-type: none;">
                <li><i class="fas fa-exclamation-circle" style="color: var(--danger); margin-right: 10px;"></i> Insufficient funds in your eSewa account</li>
                <li><i class="fas fa-exclamation-circle" style="color: var(--danger); margin-right: 10px;"></i> Transaction timeout</li>
                <li><i class="fas fa-exclamation-circle" style="color: var(--danger); margin-right: 10px;"></i> eSewa service disruption</li>
                <li><i class="fas fa-exclamation-circle" style="color: var(--danger); margin-right: 10px;"></i> Payment was cancelled</li>
            </ul>
            
            <div style="margin-top: 30px;">
                <a href="checkout.php" class="btn btn-danger">
                    <i class="fas fa-redo"></i> Try Again
                </a>
                <a href="productdisplay.php" class="btn" style="margin-left: 10px;">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</body>
</html>