<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Get product details
$id = $_GET['id'];
$product_query = "SELECT * FROM products WHERE id = $id";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

// Get all categories for dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];
    $category_id = $_POST['category_id'];

    // Update product details
    $update_query = "UPDATE products SET name='$name', description='$description', price='$price', stock='$stock', image='$image', category_id='$category_id' WHERE id=$id";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Product updated successfully'); window.location.href = 'index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style><?php include 'style.css'; ?></style>
</head>
<body>

<h2>Edit Product</h2>
<form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
    Description: <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br>
    Stock: <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br>
    Image URL: <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" required><br>
    Category: 
    <select name="category_id" required>
        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
            <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endwhile; ?>
    </select><br>
    <button type="submit">Update Product</button>
</form>
<a href="index.php">Back</a>

</body>
</html>
