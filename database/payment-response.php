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
            $stmt = $conn->prepare("UPDATE orders SET status = 'completed', payment_id = ? WHERE id = ?");
            $stmt->bind_param("si", $pidx, $order_id);
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
            
            // Clear the order from session
            unset($_SESSION['khalti_order']);
            
            // Set success message
            $_SESSION['transaction_msg'] = '<script>
            Swal.fire({
                icon: "success",
                title: "Transaction successful",
                showConfirmButton: false,
                timer: 1500
            });
            </script>';
            
            header("Location: message.php");
            exit();
            break;
            
        case 'Expired':
        case 'User canceled':
        default:
            // Update order status to failed
            $stmt = $conn->prepare("UPDATE orders SET status = 'failed' WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();
            
            // Clear the order from session
            unset($_SESSION['khalti_order']);
            
            // Set failure message
            $_SESSION['transaction_msg'] = '<script>
            Swal.fire({
                icon: "error",
                title: "Transaction failed",
                text: "' . $responseArray['status'] . '",
                showConfirmButton: false,
                timer: 1500
            });
            </script>';
            
            header("Location: checkout.php");
            exit();
            break;
    }
} else {
    // Set error message
    $_SESSION['transaction_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Failed to verify payment",
        showConfirmButton: false,
        timer: 1500
    });
    </script>';
    
    header("Location: checkout.php");
    exit();
}