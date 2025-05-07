<?php
include 'connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$id");
}
$result = mysqli_query($conn, "SELECT * FROM orders");
?>
<!DOCTYPE html>
<html>
<head><title>Orders</title><style><?php include 'style.css'; ?></style></head>
<body>
<h2>Orders</h2>
<table>
<tr><th>ID</th><th>Total</th><th>Status</th><th>Update</th></tr>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td>#<?= $row['id'] ?></td>
    <td>$<?= $row['total_amount'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <select name="status">
                <option <?= $row['status']=='Processing'?'selected':'' ?>>Processing</option>
                <option <?= $row['status']=='Shipped'?'selected':'' ?>>Shipped</option>
                <option <?= $row['status']=='Delivered'?'selected':'' ?>>Delivered</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
