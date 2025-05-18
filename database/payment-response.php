<?php
session_start();

// Get the pidx from the URL
$pidx = $_GET['pidx'] ?? null;

if (!$pidx) {
    $_SESSION['transaction_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Invalid payment response",
        showConfirmButton: false,
        timer: 1500
    });
    </script>';
    header("Location: checkout.php");
    exit();
}

// Check if we have the order details in session
if (!isset($_SESSION['khalti_order'])) {
    $_SESSION['transaction_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Order information not found",
        showConfirmButton: false,
        timer: 1500
    });
    </script>';
    header("Location: checkout.php");
    exit();
}

$order_id = $_SESSION['khalti_order']['order_id'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify the payment with Khalti
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/lookup/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
    CURLOPT_HTTPHEADER => array(
        'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);
curl_close($curl);

if ($response) {
    $responseArray = json_decode($response, true);
    
    switch ($responseArray['status']) {
        case 'Completed':
            // Update order status to completed
            $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();
            
            // Update product stock
            $stmt = $conn->prepare("SELECT oi.product_id, oi.quantity, p.stock FROM order_items oi 
                                   JOIN products p ON oi.product_id = p.id 
                                   WHERE oi.order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($item = $result->fetch_assoc()) {
                $new_stock = $item['stock'] - $item['quantity'];
                $update_stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
                $update_stmt->bind_param("ii", $new_stock, $item['product_id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
            
            $stmt->close();
            
            // Clear the saved cart and order from session
            unset($_SESSION['khalti_order']);
            unset($_SESSION['saved_cart']);
            unset($_SESSION['saved_cart_user_id']);
            
            // Set success message
            $_SESSION['transaction_msg'] = '<script>
            Swal.fire({
                icon: "success",
                title: "Payment Successful",
                text: "Your order has been confirmed and is being processed!",
                showConfirmButton: true,
                confirmButtonText: "Continue Shopping"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "productdisplay.php";
                } else {
                    window.location.href = "profile.php";
                }
            });
            </script>';
            
            header("Location: message.php");
            exit();
            break;
            
        case 'Expired':
        case 'User canceled':
        default:
            // Restore cart items if payment failed
            if (isset($_SESSION['saved_cart']) && isset($_SESSION['saved_cart_user_id'])) {
                $saved_cart = $_SESSION['saved_cart'];
                $user_id = $_SESSION['saved_cart_user_id'];
                
                // First, check if the cart is already empty
                $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $cart_count = $check_result->fetch_assoc()['count'];
                $check_stmt->close();
                
                // Only restore if cart is empty
                if ($cart_count == 0) {
                    foreach ($saved_cart as $item) {
                        $restore_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                        $restore_stmt->bind_param("iii", $user_id, $item['product_id'], $item['quantity']);
                        $restore_stmt->execute();
                        $restore_stmt->close();
                    }
                }
                
                // Clear saved cart from session
                unset($_SESSION['saved_cart']);
                unset($_SESSION['saved_cart_user_id']);
            }
            
            // Delete the order and order items
            $delete_items_stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
            $delete_items_stmt->bind_param("i", $order_id);
            $delete_items_stmt->execute();
            $delete_items_stmt->close();
            
            $delete_order_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $delete_order_stmt->bind_param("i", $order_id);
            $delete_order_stmt->execute();
            $delete_order_stmt->close();
            
            // Clear the order from session
            unset($_SESSION['khalti_order']);
            
            // Set failure message
            $_SESSION['transaction_msg'] = '<script>
            Swal.fire({
                icon: "error",
                title: "Payment Failed",
                text: "Your payment was not completed. Your cart has been restored.",
                showConfirmButton: true,
                confirmButtonText: "Return to Checkout"
            }).then((result) => {
                window.location.href = "checkout.php";
            });
            </script>';
            
            header("Location: message.php");
            exit();
            break;
    }
} else {
    // Restore cart items if payment verification failed
    if (isset($_SESSION['saved_cart']) && isset($_SESSION['saved_cart_user_id'])) {
        $saved_cart = $_SESSION['saved_cart'];
        $user_id = $_SESSION['saved_cart_user_id'];
        
        // First, check if the cart is already empty
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $cart_count = $check_result->fetch_assoc()['count'];
        $check_stmt->close();
        
        // Only restore if cart is empty
        if ($cart_count == 0) {
            foreach ($saved_cart as $item) {
                $restore_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $restore_stmt->bind_param("iii", $user_id, $item['product_id'], $item['quantity']);
                $restore_stmt->execute();
                $restore_stmt->close();
            }
        }
        
        // Clear saved cart from session
        unset($_SESSION['saved_cart']);
        unset($_SESSION['saved_cart_user_id']);
    }
    
    // Delete the order and order items
    $delete_items_stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $delete_items_stmt->bind_param("i", $order_id);
    $delete_items_stmt->execute();
    $delete_items_stmt->close();
    
    $delete_order_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete_order_stmt->bind_param("i", $order_id);
    $delete_order_stmt->execute();
    $delete_order_stmt->close();
    
    // Set error message
    $_SESSION['transaction_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Failed to verify payment",
        text: "Your cart has been restored. Please try again.",
        showConfirmButton: true,
        confirmButtonText: "Return to Checkout"
    }).then((result) => {
        window.location.href = "checkout.php";
    });
    </script>';
    
    header("Location: message.php");
    exit();
}