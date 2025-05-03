<?php
// Include database connection
include '../database/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $stock = $_POST['stock']; 
    
    // Handle file upload
    if (isset($_FILES['image'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];
        $imageError = $_FILES['image']['error'];
        $imageType = $_FILES['image']['type'];

        // Check if the file is an image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            if ($imageError === 0) {
                if ($imageSize < 1000000) { // Limit size to 1MB
                    // Generate a unique name for the image
                    $imageDestination = 'uploads/' . uniqid('', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
                    move_uploaded_file($imageTmpName, $imageDestination);
                } else {
                    echo "Your image is too large!";
                    exit;
                }
            } else {
                echo "There was an error uploading your image!";
                exit;
            }
        } else {
            echo "Invalid image type. Please upload a JPEG, PNG, or GIF image.";
            exit;
        }
    } else {
        echo "No image uploaded!";
        exit;
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image, category_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisi", $productName, $description, $price, $stock, $imageDestination, $category);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New product created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Create New Product</h2>
    <form method="POST" action="admincreateproduct.php" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label><br>
        <input type="text" id="product_name" name="product_name" required><br><br>

        <label for="price">Price:</label><br>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="stock">Stock Quantity:</label><br>
        <input type="number" id="stock" name="stock" required><br><br>

        <label for="image">Product Image:</label><br>
        <input type="file" id="image" name="image" required><br><br>

        <label for="category">Category:</label><br>
        <select id="category" name="category" required>
            <option value="1">Link Pc</option>
            <option value="2">Computer Accessories</option>
            <option value="3">Laptop & Accessories</option>
        </select><br><br>

        <button type="submit">Create Product</button>
    </form>
</body>
</html>
