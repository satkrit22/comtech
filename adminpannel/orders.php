<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Handle order status updates
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $update_query = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        $status_message = "Order status updated successfully.";
    } else {
        $error_message = "Error updating order status: " . mysqli_error($conn);
    }
}

// Pagination
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Build the query
$where = "1=1"; // Default condition that's always true

// Apply filters if set
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

if (isset($_GET['customer']) && !empty($_GET['customer'])) {
    $customer = mysqli_real_escape_string($conn, $_GET['customer']);
    $where .= " AND (name LIKE '%$customer%' OR email LIKE '%$customer%')";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM orders WHERE $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get orders with pagination
$query = "SELECT o.*, u.Name as customer_name, u.Email as customer_email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE $where 
          ORDER BY o.created_at DESC 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="orders.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Orders</h1>
                    
                </div>
                
            </div>

            <?php if (isset($status_message)): ?>
            <div class="alert alert-success">
                <?php echo $status_message; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <!-- Order Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="order-filters">
                        <div class="order-filter-item">
                            <label for="status-filter" class="form-label">Status</label>
                            <select id="status-filter" name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="order-filter-item">
                            <label for="customer-filter" class="form-label">Customer</label>
                            <input type="text" id="customer-filter" name="customer" class="form-control" placeholder="Search customer" value="<?php echo isset($_GET['customer']) ? $_GET['customer'] : ''; ?>">
                        </div>
                        <div class="order-filter-item" style="align-self: flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="orders.php" class="btn btn-light">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">All Orders</h2>
                    <div class="card-tools">
                        <div class="table-search">
                            <input type="text" class="form-control table-search-input" placeholder="Search orders...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table order-table">
                            <thead>
                                <tr>
                                    <th class="sortable">Order ID</th>
                                    <th>Customer</th>
                                    <th class="sortable">Date</th>
                                    <th>Status</th>
                                    <th class="sortable">Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while ($order = mysqli_fetch_assoc($result)) {
                                        // Format date
                                        $date = date('Y-m-d', strtotime($order['created_at']));
                                        
                                        // Get customer name and email
                                        $customer_name = !empty($order['customer_name']) ? $order['customer_name'] : $order['name'];
                                        $customer_email = !empty($order['customer_email']) ? $order['customer_email'] : $order['email'];
                                        
                                        echo '<tr>';
                                        echo '<td class="order-id">' . $order['id'] . '</td>';
                                        echo '<td>
                                            <div class="customer-info">
                                                <img src="https://ui-avatars.com/api/?name=' . urlencode($customer_name) . '&background=4361ee&color=fff" alt="Customer" class="customer-avatar">
                                                <div>
                                                    <div class="customer-name">' . $customer_name . '</div>
                                                    <div class="customer-email">' . $customer_email . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td class="order-date">' . $date . '</td>';
                                        echo '<td><span class="order-status ' . strtolower($order['status']) . '"><i class="fas fa-circle"></i> ' . ucfirst($order['status']) . '</span></td>';
                                        echo '<td class="order-total">NPR.' . number_format($order['total_price'], 2) . '</td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="order-details.php?id=' . $order['id'] . '" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-order.php?id=' . $order['id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-order.php?id=' . $order['id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    // If no orders found
                                    echo '<tr><td colspan="6" class="text-center">No orders found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing <?php echo $start + 1; ?> to <?php echo min($start + mysqli_num_rows($result), $total_records); ?> of <?php echo $total_records; ?> entries
                        </div>
                        <ul class="pagination">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['date_range']) ? '&date_range=' . $_GET['date_range'] : ''; ?><?php echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : ''; ?>" tabindex="-1">Previous</a>
                            </li>
                            
                            <?php
                            // Determine the range of page numbers to display
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            // Always show first page button
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1';
                                echo isset($_GET['status']) ? '&status=' . $_GET['status'] : '';
                                echo isset($_GET['date_range']) ? '&date_range=' . $_GET['date_range'] : '';
                                echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : '';
                                echo '">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                            }
                            
                            // Display the page numbers
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                echo '<li class="page-item ' . (($page == $i) ? 'active' : '') . '"><a class="page-link" href="?page=' . $i;
                                echo isset($_GET['status']) ? '&status=' . $_GET['status'] : '';
                                echo isset($_GET['date_range']) ? '&date_range=' . $_GET['date_range'] : '';
                                echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : '';
                                echo '">' . $i . '</a></li>';
                            }
                            
                            // Always show last page button
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages;
                                echo isset($_GET['status']) ? '&status=' . $_GET['status'] : '';
                                echo isset($_GET['date_range']) ? '&date_range=' . $_GET['date_range'] : '';
                                echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : '';
                                echo '">' . $total_pages . '</a></li>';
                            }
                            ?>
                            
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['date_range']) ? '&date_range=' . $_GET['date_range'] : ''; ?><?php echo isset($_GET['customer']) ? '&customer=' . $_GET['customer'] : ''; ?>">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Order Status Modal -->
    <div class="modal" id="statusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="order_id">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Table search functionality
            const tableSearch = document.querySelector('.table-search-input');
            if (tableSearch) {
                tableSearch.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const table = document.querySelector('.order-table');
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
                    if (!confirm('Are you sure you want to delete this order?')) {
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
            
            // Status update modal
            const statusButtons = document.querySelectorAll('.status-btn');
            statusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    const currentStatus = this.getAttribute('data-status');
                    document.getElementById('order_id').value = orderId;
                    document.getElementById('status').value = currentStatus;
                    $('#statusModal').modal('show');
                });
            });
            
            // Export functionality
            document.getElementById('export-btn').addEventListener('click', function() {
                window.location.href = 'export-orders.php<?php 
                    $params = [];
                    if (isset($_GET['status'])) $params[] = 'status=' . $_GET['status'];
                    if (isset($_GET['date_range'])) $params[] = 'date_range=' . $_GET['date_range'];
                    if (isset($_GET['customer'])) $params[] = 'customer=' . $_GET['customer'];
                    echo !empty($params) ? '?' . implode('&', $params) : '';
                ?>';
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