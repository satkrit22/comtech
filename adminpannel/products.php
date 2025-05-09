<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Build the query
$where = "1=1"; // Default condition that's always true

// Apply filters if set
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where .= " AND p.category_id = '$category'";
}

if (isset($_GET['stock_status']) && !empty($_GET['stock_status'])) {
    $stock_status = mysqli_real_escape_string($conn, $_GET['stock_status']);
    if ($stock_status == 'in-stock') {
        $where .= " AND p.stock > 0";
    } elseif ($stock_status == 'out-of-stock') {
        $where .= " AND p.stock = 0";
    } elseif ($stock_status == 'low-stock') {
        $where .= " AND p.stock > 0 AND p.stock <= 10";
    }
}

if (isset($_GET['price_min']) && !empty($_GET['price_min'])) {
    $price_min = (float)$_GET['price_min'];
    $where .= " AND p.price >= $price_min";
}

if (isset($_GET['price_max']) && !empty($_GET['price_max'])) {
    $price_max = (float)$_GET['price_max'];
    $where .= " AND p.price <= $price_max";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM products p WHERE $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get products with pagination
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE $where 
          ORDER BY p.created_at DESC 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

// Get all categories for filter dropdown
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="product.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'topnav.php'; ?>

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

            <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
                <?php echo $_SESSION['alert']['message']; ?>
                <?php unset($_SESSION['alert']); ?>
            </div>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="product-filters">
                        <div class="product-filter-item">
                            <label for="category-filter" class="form-label">Category</label>
                            <select id="category-filter" name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="product-filter-item">
                            <label for="stock-filter" class="form-label">Stock Status</label>
                            <select id="stock-filter" name="stock_status" class="form-select">
                                <option value="">All</option>
                                <option value="in-stock" <?php echo isset($_GET['stock_status']) && $_GET['stock_status'] == 'in-stock' ? 'selected' : ''; ?>>In Stock</option>
                                <option value="low-stock" <?php echo isset($_GET['stock_status']) && $_GET['stock_status'] == 'low-stock' ? 'selected' : ''; ?>>Low Stock</option>
                                <option value="out-of-stock" <?php echo isset($_GET['stock_status']) && $_GET['stock_status'] == 'out-of-stock' ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                        <div class="product-filter-item">
                            <label for="price-filter" class="form-label">Price Range</label>
                            <div class="d-flex gap-2">
                                <input type="number" id="price-min" name="price_min" class="form-control" placeholder="Min" value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : ''; ?>">
                                <input type="number" id="price-max" name="price_max" class="form-control" placeholder="Max" value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : ''; ?>">
                            </div>
                        </div>
                        <div class="product-filter-item" style="align-self: flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="products.php" class="btn btn-light">Reset</a>
                        </div>
                    </form>
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
                            <button class="btn btn-light active" id="list-view-btn">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="btn btn-light" id="grid-view-btn">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="list-view">
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
                                if (mysqli_num_rows($result) > 0) {
                                    while ($product = mysqli_fetch_assoc($result)) {
                                        $stockClass = $product['stock'] > 10 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock');
                                        $status = $product['stock'] > 0 ? 'Active' : 'Out of Stock';
                                        
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="product-info">
                                                <img src="' . $product['image'] . '" alt="Product" class="product-image-small">
                                                <div>
                                                    <div class="product-name">' . $product['name'] . '</div>
                                                    <div class="product-sku">ID: ' . $product['id'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td><span class="product-category-badge">' . $product['category_name'] . '</span></td>';
                                        echo '<td class="product-price">$' . number_format($product['price'], 2) . '</td>';
                                        echo '<td class="product-stock-qty ' . $stockClass . '">' . $product['stock'] . '</td>';
                                        echo '<td><span class="badge status-badge">' . $status . '</span></td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-product.php?id=' . $product['id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-product.php?id=' . $product['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">No products found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="grid-view" style="display: none;">
                        <div class="row">
                            <?php
                            // Reset the result set pointer
                            mysqli_data_seek($result, 0);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($product = mysqli_fetch_assoc($result)) {
                                    $stockClass = $product['stock'] > 10 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock');
                                    $status = $product['stock'] > 0 ? 'Active' : 'Out of Stock';
                                    
                                    echo '<div class="col-md-4 col-lg-3 mb-4">
                                        <div class="card product-card">
                                            <img src="' . $product['image'] . '" class="card-img-top" alt="' . $product['name'] . '">
                                            <div class="card-body">
                                                <h5 class="card-title">' . $product['name'] . '</h5>
                                                <p class="card-text product-category-badge">' . $product['category_name'] . '</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="product-price">$' . number_format($product['price'], 2) . '</span>
                                                    <span class="product-stock-qty ' . $stockClass . '">' . $product['stock'] . ' in stock</span>
                                                </div>
                                                <div class="btn-group mt-3 w-100">
                                                    <a href="edit-product.php?id=' . $product['id'] . '" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="delete-product.php?id=' . $product['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                            } else {
                                echo '<div class="col-12 text-center">No products found</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Showing <?php echo $start + 1; ?> to <?php echo min($start + mysqli_num_rows($result), $total_records); ?> of <?php echo $total_records; ?> entries</div>
                        <?php
                        // Build URL parameters for pagination
                        $url_params = '';
                        if (isset($_GET['category'])) $url_params .= '&category=' . $_GET['category'];
                        if (isset($_GET['stock_status'])) $url_params .= '&stock_status=' . $_GET['stock_status'];
                        if (isset($_GET['price_min'])) $url_params .= '&price_min=' . $_GET['price_min'];
                        if (isset($_GET['price_max'])) $url_params .= '&price_max=' . $_GET['price_max'];
                        
                        echo generatePagination($page, $total_pages, $url_params);
                        ?>
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
            const listViewBtn = document.getElementById('list-view-btn');
            const gridViewBtn = document.getElementById('grid-view-btn');
            const listView = document.getElementById('list-view');
            const gridView = document.getElementById('grid-view');
            
            listViewBtn.addEventListener('click', function() {
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                listView.style.display = 'block';
                gridView.style.display = 'none';
            });
            
            gridViewBtn.addEventListener('click', function() {
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                gridView.style.display = 'block';
                listView.style.display = 'none';
            });
        });
    </script>
</body>
</html>