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

$message = '';
$order_details = null;

// Handle verification form submission
if (isset($_POST['verify_order'])) {
    $confirmation_code = $conn->real_escape_string($_POST['confirmation_code']);
    
    // Search for order with this confirmation code
    $stmt = $conn->prepare("SELECT o.*, u.name as customer_name FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.confirmation_code = ? AND o.status IN ('pending', 'processing', 'completed')");
    $stmt->bind_param("s", $confirmation_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc();
        
        // Get order items
        $items_stmt = $conn->prepare("SELECT oi.*, p.name as product_name FROM order_items oi 
                                     JOIN products p ON oi.product_id = p.id 
                                     WHERE oi.order_id = ?");
        $items_stmt->bind_param("i", $order_details['id']);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        $order_items = $items_result->fetch_all(MYSQLI_ASSOC);
        $items_stmt->close();
        
        $order_details['items'] = $order_items;
        $message = 'Order found successfully!';
    } else {
        $message = 'Invalid confirmation code or order not found.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Verification - Comtech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn {
            background-color: #5e2ced;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #4a23b9;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .order-details {
            display: grid;
            gap: 1rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-confirmed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-processing {
            background-color: #fef3c7;
            color: #92400e;
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
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-search"></i> Order Verification</h2>
            <p>Enter your confirmation code to verify your order</p>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert <?= $order_details ? 'alert-success' : 'alert-error' ?>">
                    <i class="fas <?= $order_details ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="confirmation_code">Confirmation Code</label>
                    <input type="text" id="confirmation_code" name="confirmation_code" class="form-control" 
                           placeholder="Enter your confirmation code (e.g., COM12345678)" required>
                </div>
                <button type="submit" name="verify_order" class="btn">
                    <i class="fas fa-search"></i> Verify Order
                </button>
            </form>
        </div>
    </div>
    
    <?php if ($order_details): ?>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-receipt"></i> Order Details</h3>
        </div>
        <div class="card-body">
            <div class="order-details">
                <div class="order-item">
                    <strong>Order ID:</strong>
                    <span>#<?= $order_details['id'] ?></span>
                </div>
                <div class="order-item">
                    <strong>Confirmation Code:</strong>
                    <span><?= htmlspecialchars($order_details['confirmation_code']) ?></span>
                </div>
                <div class="order-item">
                    <strong>Customer:</strong>
                    <span><?= htmlspecialchars($order_details['customer_name']) ?></span>
                </div>
                <div class="order-item">
                    <strong>Email:</strong>
                    <span><?= htmlspecialchars($order_details['email']) ?></span>
                </div>
                <div class="order-item">
                    <strong>Phone:</strong>
                    <span><?= htmlspecialchars($order_details['number']) ?></span>
                </div>
                <div class="order-item">
                    <strong>Payment Method:</strong>
                    <span><?= htmlspecialchars($order_details['method']) ?></span>
                </div>
                <div class="order-item">
                    <strong>Total Amount:</strong>
                    <span>NPR <?= number_format($order_details['total_price'], 2) ?></span>
                </div>
                <div class="order-item">
                    <strong>Status:</strong>
                    <span class="status-badge status-<?= $order_details['status'] ?>">
                        <?= ucfirst($order_details['status']) ?>
                    </span>
                </div>
                <div class="order-item">
                    <strong>Order Date:</strong>
                    <span><?= date('F j, Y g:i A', strtotime($order_details['placed_on'])) ?></span>
                </div>
            </div>
            
            <h4 style="margin-top: 2rem; margin-bottom: 1rem;"><i class="fas fa-list"></i> Order Items</h4>
            <?php foreach ($order_details['items'] as $item): ?>
            <div class="order-item">
                <div>
                    <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                    <small>Quantity: <?= $item['quantity'] ?> × NPR <?= number_format($item['price'], 2) ?></small>
                </div>
                <span>NPR <?= number_format($item['quantity'] * $item['price'], 2) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
