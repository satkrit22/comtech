<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders-export-' . date('Y-m-d') . '.csv"');

// Create file pointer connected to PHP output stream
$output = fopen('php://output', 'w');

// Set column headers
fputcsv($output, [
    'Order ID', 
    'Customer Name', 
    'Email', 
    'Phone', 
    'Address', 
    'Payment Method', 
    'Total Products', 
    'Total Price', 
    'Status', 
    'Date'
]);

// Build the query
$where = "1=1"; // Default condition that's always true

// Apply filters if set
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where .= " AND status = '$status'";
}

if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
    $date_range = explode(' - ', $_GET['date_range']);
    if (count($date_range) == 2) {
        $start_date = mysqli_real_escape_string($conn, $date_range[0]);
        $end_date = mysqli_real_escape_string($conn, $date_range[1]);
        $where .= " AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    }
}

if (isset($_GET['customer']) && !empty($_GET['customer'])) {
    $customer = mysqli_real_escape_string($conn, $_GET['customer']);
    $where .= " AND (name LIKE '%$customer%' OR email LIKE '%$customer%')";
}

// Get all orders with filters
$query = "SELECT * FROM orders WHERE $where ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Output each row of the data
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['number'],
        $row['address'],
        $row['method'],
        $row['total_products'],
        $row['total_price'],
        $row['status'],
        $row['created_at']
    ]);
}

// Close the file pointer
fclose($output);
exit();
?>