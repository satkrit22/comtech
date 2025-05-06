<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    if (deleteProduct($conn, $product_id)) {
        $_SESSION['success_msg'] = "Product deleted successfully";
    } else {
        $_SESSION['error_msg'] = "Failed to delete product";
    }
    header('Location: products.php');
    exit();
}

// Get filter parameters
$filter = $_GET['filter'] ?? '';
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Get products based on filters
$products = getProducts($conn, $filter, $category, $search);

// Get all categories for filter dropdown
$categories = getAllCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <?php include 'includes/topnav.php'; ?>

            <!-- Products Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Manage Products</h1>
                    <a href="add-product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>

                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success_msg'] ?>
                        <?php unset($_SESSION['success_msg']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error_msg'] ?>
                        <?php unset($_SESSION['error_msg']); ?>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="filters-container">
                    <form action="" method="GET" class="filters-form">
                        <div class="filter-group">
                            <label for="search">Search:</label>
                            <div class="search-input-group">
                                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search products...">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="category">Category:</label>
                            <select id="category" name="category" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter">Filter:</label>
                            <select id="filter" name="filter" onchange="this.form.submit()">
                                <option value="" <?= $filter == '' ? 'selected' : '' ?>>All Products</option>
                                <option value="low_stock" <?= $filter == 'low_stock' ? 'selected' : '' ?>>Low Stock</option>
                                <option value="out_of_stock" <?= $filter == 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                                <option value="newest" <?= $filter == 'newest' ? 'selected' : '' ?>>Newest First</option>
                                <option value="price_high" <?= $filter == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="price_low" <?= $filter == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <button type="submit" class="btn btn-secondary">Apply Filters</button>
                            <a href="products.php" class="btn btn-outline">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- Products Table -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2><i class="fas fa-box"></i> Products List</h2>
                        <span class="badge"><?= count($products) ?> products</span>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($products) > 0): ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?= $product['id'] ?></td>
                                                <td>
                                                    <img src="/comtech/assets/img/menu/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
                                                </td>
                                                <td><?= htmlspecialchars($product['name']) ?></td>
                                                <td><?= htmlspecialchars($product['category_name']) ?></td>
                                                <td>NPR <?= number_format($product['price']) ?></td>
                                                <td>
                                                    <span class="stock-badge <?= $product['stock'] <= 5 ? 'low-stock' : ($product['stock'] == 0 ? 'out-of-stock' : '') ?>">
                                                        <?= $product['stock'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
                                                <td class="actions-cell">
                                                    <a href="edit-product.php?id=<?= $product['id'] ?>" class="action-btn edit-btn" title="Edit Product">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="products.php?delete=<?= $product['id'] ?>" class="action-btn delete-btn" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No products found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
