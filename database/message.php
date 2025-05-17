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
    <title>Transaction Message</title>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
// Display the transaction message (already wrapped in a <script> tag)
if ($transactionMsg) {
    echo $transactionMsg;
} else {
    echo '<script>
    Swal.fire({
        icon: "info",
        title: "No message found",
        showConfirmButton: false,
        timer: 1500
    });
    </script>';
}
?>

<!-- Optional: Redirect to homepage or order summary after a few seconds -->
<script>
    setTimeout(function() {
        window.location.href = "profile.php"; // Change to where you want to redirect
    }, 2000);
</script>

</body>
</html>
