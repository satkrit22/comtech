<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Sample logic to fetch products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

&lt;!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Core Admin Dashboard Styles */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #6c757d;
            --success: #2ecc71;
            --info: #3498db;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #343a40;
            --body-bg: #f5f7fb;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #adb5bd;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            --card-border-radius: 8px;
            --btn-border-radius: 4px;
            --input-border-radius: 4px;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --sidebar-bg: #1e1e2d;
            --sidebar-color: #a2a3b7;
            --sidebar-hover-bg: #282839;
            --sidebar-active-bg: #282839;
            --sidebar-active-color: #ffffff;
            --topnav-height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            line-height: 1.5;
            font-size: 14px;
        }

        a {
            text-decoration: none;
            color: var(--primary);
        }

        a:hover {
            color: var(--primary-dark);
        }

        /* Layout */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        .sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-color);
            z-index: 1000;
            transition: width 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .logo a {
            color: white;
            text-decoration: none;
        }

        .logo h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
        }

        .logo span {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .sidebar-toggle {
            background: transparent;
            border: none;
            color: var(--sidebar-color);
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 4px;
        }

        .sidebar-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: var(--sidebar-color);
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-menu a:hover {
            background-color: var(--sidebar-hover-bg);
            color: white;
        }

        .sidebar-menu li.active a {
            background-color: var(--sidebar-active-bg);
            color: var(--sidebar-active-color);
            border-left: 3px solid var(--primary);
        }

        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-collapsed .sidebar-menu span {
            display: none;
        }

        /* Top Navigation */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: var(--topnav-height);
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .top-nav-left, .top-nav-right {
            display: flex;
            align-items: center;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-container input {
            width: 100%;
            padding: 8px 15px 8px 35px;
            border: 1px solid var(--border-color);
            border-radius: var(--input-border-radius);
            background-color: var(--light);
        }

        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .nav-item {
            position: relative;
            margin-left: 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--light);
            color: var(--primary);
        }

        .badge-counter {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-profile {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: var(--btn-border-radius);
            transition: all 0.3s ease;
        }

        .profile-btn:hover {
            background-color: var(--light);
        }

        .profile-btn span {
            margin-right: 8px;
            font-weight: 500;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--card-bg);
            border-radius: var(--card-border-radius);
            box-shadow: var(--shadow);
            min-width: 180px;
            z-index: 1000;
            display: none;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--light);
            color: var(--primary);
        }

        .dropdown-item i {
            margin-right: 10px;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 5px 0;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .breadcrumb {
            display: flex;
            list-style: none;
            margin-top: 5px;
        }

        .breadcrumb-item {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 5px;
            color: var(--text-muted);
        }

        .breadcrumb-item.active {
            color: var(--primary);
        }

        /* Cards */
        .card {
            background-color: var(--card-bg);
            border-radius: var(--card-border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            background-color: rgba(0, 0, 0, 0.01);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-tools {
            display: flex;
            gap: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .card-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
            background-color: rgba(0, 0, 0, 0.01);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: var(--btn-border-radius);
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #e67e22;
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            color: white;
        }

        .btn-light {
            background-color: var(--light);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .btn-light:hover {
            background-color: #e2e6ea;
            color: var(--text-primary);
        }

        .btn i {
            margin-right: 5px;
        }

        .btn-group {
            display: flex;
            gap: 5px;
        }

        /* Forms */
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--input-border-radius);
            background-color: var(--card-bg);
            color: var(--text-primary);
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--input-border-radius);
            background-color: var(--card-bg);
            color: var(--text-primary);
            transition: border-color 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            font-weight: 600;
            color: var(--text-primary);
            background-color: rgba(0, 0, 0, 0.02);
            text-align: left;
            white-space: nowrap;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        /* Search */
        .table-search {
            position: relative;
            width: 250px;
        }

        .table-search-input {
            padding-left: 35px;
        }

        .table-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        /* Pagination */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item {
            margin: 0 2px;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            min-width: 32px;
            padding: 0 10px;
            border-radius: var(--btn-border-radius);
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: var(--light);
            color: var(--primary);
            border-color: var(--border-color);
        }

        .page-item.active .page-link {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-item.disabled .page-link {
            color: var(--text-muted);
            pointer-events: none;
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        /* Utilities */
        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 10px;
        }

        .mb-4 {
            margin-bottom: 20px;
        }

        .mt-4 {
            margin-top: 20px;
        }

        /* Product Specific Styles */
        .product-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .product-filter-item {
            min-width: 150px;
        }

        .product-info {
            display: flex;
            align-items: center;
        }

        .product-image-small {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            margin-right: 10px;
            object-fit: cover;
        }

        .product-name {
            font-weight: 500;
        }

        .product-sku {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .product-category-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }

        .product-price {
            font-weight: 600;
        }

        .product-stock-qty {
            font-weight: 600;
        }

        .product-stock-qty.in-stock {
            color: var(--success);
        }

        .product-stock-qty.low-stock {
            color: var(--warning);
        }

        .product-stock-qty.out-of-stock {
            color: var(--danger);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .card-tools {
                width: 100%;
                justify-content: flex-end;
            }
            
            .table-search {
                width: 100%;
            }
            
            .product-filters {
                flex-direction: column;
            }
            
            .product-filter-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include 'includes/topnav.php'; ?>

            <div class="page-header">
                <div>
                    <h1 class="page-title">Products</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="add-product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
            </div>

            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="product-filters">
                        <div class="product-filter-item">
                            <label for="category-filter" class="form-label">Category</label>
                            <select id="category-filter" class="form-select">
                                <option value="">All Categories</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="accessories">Accessories</option>
                                <option value="home">Home & Kitchen</option>
                            </select>
                        </div>
                        <div class="product-filter-item">
                            <label for="stock-filter" class="form-label">Stock Status</label>
                            <select id="stock-filter" class="form-select">
                                <option value="">All</option>
                                <option value="in-stock">In Stock</option>
                                <option value="low-stock">Low Stock</option>
                                <option value="out-of-stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="product-filter-item">
                            <label for="price-filter" class="form-label">Price Range</label>
                            <div class="d-flex gap-2">
                                <input type="number" id="price-min" class="form-control" placeholder="Min">
                                <input type="number" id="price-max" class="form-control" placeholder="Max">
                            </div>
                        </div>
                        <div class="product-filter-item" style="align-self: flex-end;">
                            <button class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">All Products</h2>
                    <div class="card-tools">
                        <div class="table-search">
                            <input type="text" class="form-control table-search-input" placeholder="Search products...">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-light active">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="btn btn-light">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table product-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th class="sortable">Price</th>
                                    <th class="sortable">Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // If no products yet, show sample data
                                if (mysqli_num_rows($result) == 0) {
                                    $sampleProducts = [
                                        ['product_id' => '101', 'product_name' => 'Smartphone X', 'product_sku' => 'PHN-001', 'category' => 'Electronics', 'price' => '799.99', 'stock' => '25', 'status' => 'Active'],
                                        ['product_id' => '102', 'product_name' => 'Laptop Pro', 'product_sku' => 'LPT-002', 'category' => 'Electronics', 'price' => '1299.99', 'stock' => '10', 'status' => 'Active'],
                                        ['product_id' => '103', 'product_name' => 'Wireless Headphones', 'product_sku' => 'AUD-003', 'category' => 'Accessories', 'price' => '149.99', 'stock' => '50', 'status' => 'Active'],
                                        ['product_id' => '104', 'product_name' => 'Smart Watch', 'product_sku' => 'WCH-004', 'category' => 'Wearables', 'price' => '249.99', 'stock' => '15', 'status' => 'Active'],
                                        ['product_id' => '105', 'product_name' => 'Bluetooth Speaker', 'product_sku' => 'AUD-005', 'category' => 'Audio', 'price' => '89.99', 'stock' => '0', 'status' => 'Out of Stock'],
                                    ];
                                    
                                    foreach ($sampleProducts as $product) {
                                        $stockClass = $product['stock'] > 20 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock');
                                        
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="product-info">
                                                <img src="https://via.placeholder.com/40" alt="Product" class="product-image-small">
                                                <div>
                                                    <div class="product-name">' . $product['product_name'] . '</div>
                                                    <div class="product-sku">' . $product['product_sku'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td><span class="product-category-badge">' . $product['category'] . '</span></td>';
                                        echo '<td class="product-price">$' . $product['price'] . '</td>';
                                        echo '<td class="product-stock-qty ' . $stockClass . '">' . $product['stock'] . '</td>';
                                        echo '<td><span class="badge status-badge">' . $product['status'] . '</span></td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-product.php?id=' . $product['product_id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-product.php?id=' . $product['product_id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    while ($product = mysqli_fetch_assoc($result)) {
                                        $stockClass = $product['stock'] > 20 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock');
                                        
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="product-info">
                                                <img src="' . ($product['image'] ?? 'https://via.placeholder.com/40') . '" alt="Product" class="product-image-small">
                                                <div>
                                                    <div class="product-name">' . $product['product_name'] . '</div>
                                                    <div class="product-sku">' . $product['product_sku'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td><span class="product-category-badge">' . $product['category'] . '</span></td>';
                                        echo '<td class="product-price">$' . $product['price'] . '</td>';
                                        echo '<td class="product-stock-qty ' . $stockClass . '">' . $product['stock'] . '</td>';
                                        echo '<td><span class="badge status-badge">' . ($product['stock'] > 0 ? 'Active' : 'Out of Stock') . '</span></td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-product.php?id=' . $product['product_id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-product.php?id=' . $product['product_id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Showing 1 to 5 of 5 entries</div>
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Table search functionality
            const tableSearch = document.querySelector('.table-search-input');
            if (tableSearch) {
                tableSearch.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const table = document.querySelector('.product-table');
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
            
            // Sortable columns
            const sortableHeaders = document.querySelectorAll('.sortable');
            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const table = this.closest('table');
                    const index = Array.from(this.parentNode.children).indexOf(this);
                    const rows = Array.from(table.querySelectorAll('tbody tr'));
                    const direction = this.classList.contains('asc') ? 'desc' : 'asc';
                    
                    // Remove sort classes from all headers
                    table.querySelectorAll('th').forEach(th => {
                        th.classList.remove('asc', 'desc');
                    });
                    
                    // Add sort class to current header
                    this.classList.add(direction);
                    
                    // Sort the rows
                    rows.sort((a, b) => {
                        const aValue = a.children[index].textContent.trim();
                        const bValue = b.children[index].textContent.trim();
                        
                        // Check if values are numbers
                        if (!isNaN(aValue.replace('$', '')) && !isNaN(bValue.replace('$', ''))) {
                            return direction === 'asc' 
                                ? parseFloat(aValue.replace('$', '')) - parseFloat(bValue.replace('$', ''))
                                : parseFloat(bValue.replace('$', '')) - parseFloat(aValue.replace('$', ''));
                        }
                        
                        // Sort as strings
                        return direction === 'asc'
                            ? aValue.localeCompare(bValue)
                            : bValue.localeCompare(aValue);
                    });
                    
                    // Reorder the rows
                    const tbody = table.querySelector('tbody');
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
            
            // Delete confirmation
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this product?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Toggle sidebar on mobile
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                });
            }
            
            // Toggle view (list/grid)
            const viewButtons = document.querySelectorAll('.btn-group .btn');
            if (viewButtons.length) {
                viewButtons.forEach((button, index) => {
                    button.addEventListener('click', function() {
                        viewButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            }
        });
    </script>
</body>
</html>