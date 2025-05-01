<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

$result = mysqli_query($conn, "SELECT products.*, categories.name AS category_name 
                               FROM products 
                               JOIN categories ON products.category_id = categories.id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <style><?php include 'style.css'; ?></style>
</head>
<body>

<h2>Products</h2>
<a href="create.php">Add Product</a>
<table>
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Category</th>
        <th>Price ($)</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= htmlspecialchars($row['category_name']) ?></td>
        <td><?= number_format($row['price'], 2) ?></td>
        <td><?= $row['stock'] ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
