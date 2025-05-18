<?php
session_start();

// Check if order details are provided
if (!isset($_GET['order_id']) || !isset($_GET['amount']) || !isset($_GET['name']) || !isset($_GET['email']) || !isset($_GET['phone'])) {
    $_SESSION['validate_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Invalid request",
        text: "Missing required order information",
        showConfirmButton: false,
        timer: 1500
    });
    </script>';
    header("Location: checkout.php");
    exit();
}

// Get order details from URL
$order_id = $_GET['order_id'];
$amount = $_GET['amount'] * 100;
$name = $_GET['name'];
$email = $_GET['email'];
$phone = $_GET['phone'];
$purchase_order_id = "COM-" . $order_id;
$purchase_order_name = "Comtech Order #" . $order_id;

// Connect to database to verify order
$conn = new mysqli("localhost", "root", "", "comtech");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$order) {
    $_SESSION['validate_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Order not found",
        text: "The order you are trying to pay for could not be found",
        showConfirmButton: false,
        timer: 1500
    });
    </script>';
    header("Location: checkout.php");
    exit();
}

// Store order details in session for verification after payment
$_SESSION['khalti_order'] = [
    'order_id' => $order_id,
    'amount' => $amount,
    'purchase_order_id' => $purchase_order_id
];

// Prepare data for Khalti API
$postFields = array(
    "return_url" => "http://" . $_SERVER['HTTP_HOST'] . "/comtech/database/payment-response.php",
    "website_url" => "http://" . $_SERVER['HTTP_HOST'] . "/",
    "amount" => $amount,
    "purchase_order_id" => $purchase_order_id,
    "purchase_order_name" => $purchase_order_name,
    "customer_info" => array(
        "name" => $name,
        "email" => $email,
        "phone" => $phone
    )
);

$jsonData = json_encode($postFields);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_HTTPHEADER => array(
        'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $_SESSION['transaction_msg'] = '<script>
    Swal.fire({
        icon: "error",
        title: "Payment Error",
        text: "' . curl_error($curl) . '",
        showConfirmButton: true,
        confirmButtonText: "Return to Checkout"
    }).then((result) => {
        window.location.href = "checkout.php";
    });
    </script>';
    header("Location: message.php");
    exit();
} else {
    $responseArray = json_decode($response, true);

    if (isset($responseArray['error'])) {
        $_SESSION['transaction_msg'] = '<script>
        Swal.fire({
            icon: "error",
            title: "Payment Error",
            text: "' . $responseArray['error'] . '",
            showConfirmButton: true,
            confirmButtonText: "Return to Checkout"
        }).then((result) => {
            window.location.href = "checkout.php";
        });
        </script>';
        header("Location: message.php");
        exit();
    } elseif (isset($responseArray['payment_url'])) {
        // Redirect the user to the Khalti payment page
        header('Location: ' . $responseArray['payment_url']);
        exit();
    } else {
        $_SESSION['transaction_msg'] = '<script>
        Swal.fire({
            icon: "error",
            title: "Unexpected Response",
            text: "Please try again later",
            showConfirmButton: true,
            confirmButtonText: "Return to Checkout"
        }).then((result) => {
            window.location.href = "checkout.php";
        });
        </script>';
        header("Location: message.php");
        exit();
    }
}

curl_close($curl);