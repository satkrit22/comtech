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

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Build the query
$where = "1=1"; // Default condition that's always true

// Apply filters if set
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where .= " AND status = '$status'";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM categories WHERE $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get categories with pagination
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          WHERE $where 
          GROUP BY c.id 
          ORDER BY c.name 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/categories.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include 'includes/topnav.php'; ?>

            <div class="page-header">
                <div>
                    <h1 class="page-title">Categories</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="add-category.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Category
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
                    <h2 class="card-title">All Categories</h2>
                    <div class="card-tools">
                        <div class="table-search">
                            <input type="text" class="form-control table-search-input" placeholder="Search categories...">
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
                        <table class="table category-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="sortable">Products</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while ($category = mysqli_fetch_assoc($result)) {
                                        $icon = !empty($category['icon']) ? $category['icon'] : 'fas fa-folder';
                                        $status = isset($category['status']) ? $category['status'] : 'Active';
                                        
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="category-info">
                                                <div class="category-icon"><i class="' . $icon . '"></i></div>
                                                <div>
                                                    <div class="category-name">' . $category['name'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td>' . $category['description'] . '</td>';
                                        echo '<td class="category-count">' . $category['product_count'] . '</td>';
                                        echo '<td><span class="status-badge ' . (strtolower($status) === 'inactive' ? 'inactive' : '') . '">' . $status . '</span></td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-category.php?id=' . $category['id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-category.php?id=' . $category['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No categories found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="category-grid mt-4" id="grid-view" style="display: none;">
                        <?php
                        // Reset the result set pointer
                        mysqli_data_seek($result, 0);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($category = mysqli_fetch_assoc($result)) {
                                $icon = !empty($category['icon']) ? $category['icon'] : 'fas fa-folder';
                                $status = isset($category['status']) ? $category['status'] : 'Active';
                                
                                echo '<div class="category-card">
                                    <div class="category-card-header">
                                        <div class="category-icon"><i class="' . $icon . '"></i></div>
                                        <div>
                                            <div class="category-name">' . $category['name'] . '</div>
                                            <div class="category-description">' . $category['description'] . '</div>
                                        </div>
                                    </div>
                                    <div class="category-card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="form-label">Products</div>
                                                <div class="category-count">' . $category['product_count'] . '</div>
                                            </div>
                                            <div>
                                                <div class="form-label">Status</div>
                                                <span class="status-badge ' . (strtolower($status) === 'inactive' ? 'inactive' : '') . '">' . $status . '</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="category-card-footer">
                                        <a href="view-category.php?id=' . $category['id'] . '" class="btn btn-light btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <div class="btn-group">
                                            <a href="edit-category.php?id=' . $category['id'] . '" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete-category.php?id=' . $category['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo '<div class="text-center">No categories found</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Showing <?php echo $start + 1; ?> to <?php echo min($start + mysqli_num_rows($result), $total_records); ?> of <?php echo $total_records; ?> entries</div>
                        <?php
                        // Build URL parameters for pagination
                        $url_params = '';
                        if (isset($_GET['status'])) $url_params .= '&status=' . $_GET['status'];
                        
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
                    const table = document.querySelector('.category-table');
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
                        if (!isNaN(aValue) && !isNaN(bValue)) {
                            return direction === 'asc' 
                                ? parseFloat(aValue) - parseFloat(bValue)
                                : parseFloat(bValue) - parseFloat(aValue);
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
                    if (!confirm('Are you sure you want to delete this category?')) {
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
                gridView.style.display = 'grid';
                listView.style.display = 'none';
            });
        });
    </script>
</body>
</html>