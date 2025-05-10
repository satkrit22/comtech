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

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Build the query
$where = "1=1"; // Default condition that's always true

// Apply filters if set
if (isset($_GET['role']) && !empty($_GET['role'])) {
    $role = mysqli_real_escape_string($conn, $_GET['role']);
    $where .= " AND role = '$role'";
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where .= " AND status = '$status'";
}

if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
    $date_range = explode(' - ', $_GET['date_range']);
    if (count($date_range) == 2) {
        $start_date = mysqli_real_escape_string($conn, $date_range[0]);
        $end_date = mysqli_real_escape_string($conn, $date_range[1]);
        $where .= " AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    }
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM users WHERE $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get users with pagination
$query = "SELECT * FROM users WHERE $where ORDER BY created_at DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="users.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <a href="index.php">
                <h2>Comtech</h2>
                <span>Admin Dashboard</span>
            </a>
        </div>
        <button class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <a href="products.php">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <a href="categories.php">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="adminlogout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
        <main class="main-content">
            

            <div class="page-header">
                <div>
                    <h1 class="page-title">Users</h1>
                   
                </div>
                <div class="page-actions">
                    <a href="add-user.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add User
                    </a>
                </div>
            </div>
            
            <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
                <?php echo $_SESSION['alert']['message']; ?>
                <?php unset($_SESSION['alert']); ?>
            </div>
            <?php endif; ?>
            
            <!-- <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="user-filters">
                        <div class="user-filter-item">
                            <label for="role-filter" class="form-label">Role</label>
                            <select id="role-filter" name="role" class="form-select">
                                <option value="">All Roles</option>
                                <option value="admin" <?php echo isset($_GET['role']) && $_GET['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="manager" <?php echo isset($_GET['role']) && $_GET['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                                <option value="customer" <?php echo isset($_GET['role']) && $_GET['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                            </select>
                        </div>
                        <div class="user-filter-item">
                            <label for="status-filter" class="form-label">Status</label>
                            <select id="status-filter" name="status" class="form-select">
                                <option value="">All</option>
                                <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo isset($_GET['status']) && $_GET['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="user-filter-item">
                            <label for="date-filter" class="form-label">Registration Date</label>
                            <input type="text" id="date-filter" name="date_range" class="form-control date-range-picker" placeholder="Select date range" value="<?php echo isset($_GET['date_range']) ? $_GET['date_range'] : ''; ?>">
                        </div>
                        <div class="user-filter-item" style="align-self: flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="users.php" class="btn btn-light">Reset</a>
                        </div>
                    </form>
                </div>
            </div> -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">All Users</h2>
                    <div class="card-tools">
                        <div class="table-search">
                            <input type="text" class="form-control table-search-input" placeholder="Search users...">
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
                        <table class="table user-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Phone</th>
                                    <th class="sortable">Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while ($user = mysqli_fetch_assoc($result)) {
                                        $registered_date = date('Y-m-d', strtotime($user['created_at']));
                                        
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="user-info">
                                                <img src="https://ui-avatars.com/api/?name=' . urlencode($user['Name']) . '&background=4361ee&color=fff" alt="User" class="user-avatar">
                                                <div>
                                                    <div class="user-name">' . $user['Name'] . '</div>
                                                    <div class="user-email">' . $user['Email'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td>' . $user['Phone'] . '</td>';
                                        echo '<td>' . $registered_date . '</td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-user.php?id=' . $user['id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-user.php?id=' . $user['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center">No users found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="user-grid mt-4" id="grid-view" style="display: none;">
                        <?php
                        // Reset the result set pointer
                        mysqli_data_seek($result, 0);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($user = mysqli_fetch_assoc($result)) {
                                $registered_date = date('M d, Y', strtotime($user['created_at']));
                                
                                echo '<div class="user-card">
                                    <div class="user-card-header"></div>
                                    <img src="https://ui-avatars.com/api/?name=' . urlencode($user['Name']) . '&background=4361ee&color=fff" alt="User" class="user-card-avatar">
                                    <div class="user-card-body">
                                        <h3 class="user-card-name">' . $user['Name'] . '</h3>
                                        <div class="user-card-role">Customer</div>
                                        <div class="user-card-info">
                                            <div class="user-card-info-item">
                                                <i class="fas fa-envelope"></i> ' . $user['Email'] . '
                                            </div>
                                            <div class="user-card-info-item">
                                                <i class="fas fa-phone"></i> ' . $user['Phone'] . '
                                            </div>
                                            <div class="user-card-info-item">
                                                <i class="fas fa-calendar"></i> Joined ' . $registered_date . '
                                            </div>
                                        </div>
                                        <div class="user-card-actions">
                                            <a href="edit-user.php?id=' . $user['id'] . '" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete-user.php?id=' . $user['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo '<div class="text-center">No users found</div>';
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
                        if (isset($_GET['role'])) $url_params .= '&role=' . $_GET['role'];
                        if (isset($_GET['status'])) $url_params .= '&status=' . $_GET['status'];
                        if (isset($_GET['date_range'])) $url_params .= '&date_range=' . $_GET['date_range'];
                        
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
                    const table = document.querySelector('.user-table');
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
                    if (!confirm('Are you sure you want to delete this user?')) {
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
            
            // Initialize date range picker if available
            if (typeof daterangepicker !== 'undefined') {
                $('.date-range-picker').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
                
                $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                });
                
                $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
            }
        });
    </script>
</body>
</html>