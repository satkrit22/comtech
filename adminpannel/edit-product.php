<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Get all categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    
    // Handle image upload
    $image = $product['image']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Delete old image if it exists
            if (!empty($product['image']) && file_exists($product['image'])) {
                unlink($product['image']);
            }
            
            $image = $target_file;
        } else {
            $_SESSION['alert'] = [
                'message' => 'Error uploading image.',
                'type' => 'danger'
            ];
        }
    }
    
    // Update product
    $query = "UPDATE products SET 
              name = '$name', 
              description = '$description', 
              price = $price, 
              stock = $stock, 
              image = '$image', 
              category_id = $category_id 
              WHERE id = $product_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['alert'] = [
            'message' => 'Product updated successfully.',
            'type' => 'success'
        ];
        header('Location: products.php');
        exit();
    } else {
        $_SESSION['alert'] = [
            'message' => 'Error updating product: ' . mysqli_error($conn),
            'type' => 'danger'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="/comtech/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/comtech/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'topnav.php'; ?>

            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Product</h1>
                </div>
                <div class="page-actions">
                    <a href="products.php" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
                <?php echo $_SESSION['alert']['message']; ?>
                <?php unset($_SESSION['alert']); ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Product Information</h2>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="5"><?php echo $product['description']; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" value="<?php echo $product['price']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="stock" class="form-label">Stock Quantity</label>
                                            <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?php echo $product['stock']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select id="category_id" name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php 
                                        mysqli_data_seek($categories_result, 0);
                                        while ($category = mysqli_fetch_assoc($categories_result)): 
                                        ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-4">
                                    <label for="image" class="form-label">Product Image</label>
                                    <div class="product-image-preview mb-3">
                                        <img id="image-preview" src="<?php echo !empty($product['image']) ? $product['image'] : 'comtech/assets/img/menu/'; ?>" alt="Product Image Preview" style="max-width: 100%; height: auto;">
                                    </div>
                                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Leave empty to keep the current image</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                            <a href="products.php" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Toggle sidebar on mobile
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>