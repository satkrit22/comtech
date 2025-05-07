<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

$id = $_GET['id'];
$query = "DELETE FROM products WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Product deleted successfully'); window.location.href = 'productindex.php';</script>";
} else {
    echo "Error deleting product: " . mysqli_error($conn);
}
?>
