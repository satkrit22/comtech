<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];
    $category_id = $_POST['category_id'];

    // Insert product into database
    $query = "INSERT INTO products (name, description, price, stock, image, category_id) 
              VALUES ('$name', '$description', '$price', '$stock', '$image', '$category_id')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Product added successfully'); window.location.href = 'index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Get all categories for the dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <style><?php include 'style.css'; ?></style>
</head>
<body>
    <h2>Add Product</h2>
    <form method="post" enctype="multipart/form-data">
        Name: <input type="text" name="name" required><br>
        Description: <textarea name="description" required></textarea><br>
        Price: <input type="number" step="0.01" name="price" required><br>
        Stock: <input type="number" name="stock" required><br>
        Image URL: <input type="text" name="image" required><br>
        Category: 
        <select name="category_id" required>
            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endwhile; ?>
        </select><br>
        <button type="submit">Add Product</button>
    </form>
    <a href="index.php">Back</a>
</body>
</html>
