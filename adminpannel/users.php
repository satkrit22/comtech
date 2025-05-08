<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | Comtech Admin</title>
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

        /* User Specific Styles */
        .user-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .user-filter-item {
            min-width: 150px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .user-name {
            font-weight: 500;
        }

        .user-email {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .user-role {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .user-role.admin {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .user-role.manager {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }

        .user-role.editor {
            background-color: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }

        .user-role.customer {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        .user-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .user-status.active {
            background-color: var(--success);
        }

        .user-status.inactive {
            background-color: var(--danger);
        }

        /* User Cards */
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .user-card {
            background-color: var(--card-bg);
            border-radius: var(--card-border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .user-card-header {
            background-color: var(--primary);
            height: 80px;
            position: relative;
        }

        .user-card-avatar {
            position: absolute;
            bottom: -30px;
            left: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid var(--card-bg);
            background-color: var(--card-bg);
        }

        .user-card-body {
            padding: 40px 20px 20px;
        }

        .user-card-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-card-role {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 15px;
        }

        .user-card-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .user-card-info-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .user-card-info-item i {
            width: 20px;
            margin-right: 10px;
            color: var(--text-muted);
        }

        .user-card-actions {
            display: flex;
            gap: 10px;
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
            
            .user-filters {
                flex-direction: column;
            }
            
            .user-filter-item {
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
                    <h1 class="page-title">Users</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="add-user.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add User
                    </a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-filters">
                        <div class="user-filter-item">
                            <label for="role-filter" class="form-label">Role</label>
                            <select id="role-filter" class="form-select">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="editor">Editor</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>
                        <div class="user-filter-item">
                            <label for="status-filter" class="form-label">Status</label>
                            <select id="status-filter" class="form-select">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="user-filter-item">
                            <label for="date-filter" class="form-label">Registration Date</label>
                            <input type="text" id="date-filter" class="form-control date-range-picker" placeholder="Select date range">
                        </div>
                        <div class="user-filter-item" style="align-self: flex-end;">
                            <button class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">All Users</h2>
                    <div class="card-tools">
                        <div class="table-search">
                            <input type="text" class="form-control table-search-input" placeholder="Search users...">
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
                        <table class="table user-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th class="sortable">Registered</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // If no users yet, show sample data
                                if (mysqli_num_rows($result) == 0) {
                                    $sampleUsers = [
                                        ['user_id' => '1', 'username' => 'admin', 'email' => 'admin@example.com', 'role' => 'Admin', 'registered' => '2023-01-15', 'status' => 'Active'],
                                        ['user_id' => '2', 'username' => 'john_doe', 'email' => 'john@example.com', 'role' => 'Manager', 'registered' => '2023-02-20', 'status' => 'Active'],
                                        ['user_id' => '3', 'username' => 'jane_smith', 'email' => 'jane@example.com', 'role' => 'Editor', 'registered' => '2023-03-10', 'status' => 'Active'],
                                        ['user_id' => '4', 'username' => 'robert_johnson', 'email' => 'robert@example.com', 'role' => 'Customer', 'registered' => '2023-04-05', 'status' => 'Inactive'],
                                        ['user_id' => '5', 'username' => 'emily_davis', 'email' => 'emily@example.com', 'role' => 'Customer', 'registered' => '2023-05-01', 'status' => 'Active'],
                                    ];
                                    
                                    foreach ($sampleUsers as $user) {
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="user-info">
                                                <img src="https://ui-avatars.com/api/?name=' . str_replace('_', '+', $user['username']) . '&background=4361ee&color=fff" alt="User" class="user-avatar">
                                                <div>
                                                    <div class="user-name">' . $user['username'] . '</div>
                                                    <div class="user-email">' . $user['email'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td><span class="user-role ' . strtolower($user['role']) . '">' . $user['role'] . '</span></td>';
                                        echo '<td>' . $user['registered'] . '</td>';
                                        echo '<td><span class="user-status ' . strtolower($user['status']) . '"></span> ' . $user['status'] . '</td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-user.php?id=' . $user['user_id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-user.php?id=' . $user['user_id'] . '" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    while ($user = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>
                                            <div class="user-info">
                                                <img src="https://ui-avatars.com/api/?name=' . str_replace(' ', '+', $user['username']) . '&background=4361ee&color=fff" alt="User" class="user-avatar">
                                                <div>
                                                    <div class="user-name">' . $user['username'] . '</div>
                                                    <div class="user-email">' . $user['email'] . '</div>
                                                </div>
                                            </div>
                                        </td>';
                                        echo '<td><span class="user-role ' . strtolower($user['role']) . '">' . $user['role'] . '</span></td>';
                                        echo '<td>' . ($user['registered'] ?? date('Y-m-d')) . '</td>';
                                        echo '<td><span class="user-status ' . strtolower($user['status'] ?? 'active') . '"></span> ' . ($user['status'] ?? 'Active') . '</td>';
                                        echo '<td>
                                            <div class="btn-group">
                                                <a href="edit-user.php?id=' . $user['user_id'] . '" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-user.php?id=' . $user['user_id'] . '" class="btn btn-sm btn-danger delete-btn">
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

            <div class="user-grid mt-4" style="display: none;">
                <?php
                $sampleUsers = [
                    ['user_id' => '1', 'username' => 'admin', 'email' => 'admin@example.com', 'role' => 'Admin', 'registered' => '2023-01-15', 'status' => 'Active'],
                    ['user_id' => '2', 'username' => 'john_doe', 'email' => 'john@example.com', 'role' => 'Manager', 'registered' => '2023-02-20', 'status' => 'Active'],
                    ['user_id' => '3', 'username' => 'jane_smith', 'email' => 'jane@example.com', 'role' => 'Editor', 'registered' => '2023-03-10', 'status' => 'Active'],
                    ['user_id' => '4', 'username' => 'robert_johnson', 'email' => 'robert@example.com', 'role' => 'Customer', 'registered' => '2023-04-05', 'status' => 'Inactive'],
                ];
                
                foreach ($sampleUsers as $user) {
                    echo '<div class="user-card">
                        <div class="user-card-header"></div>
                        <img src="https://ui-avatars.com/api/?name=' . str_replace('_', '+', $user['username']) . '&background=4361ee&color=fff" alt="User" class="user-card-avatar">
                        <div class="user-card-body">
                            <h3 class="user-card-name">' . $user['username'] . '</h3>
                            <div class="user-card-role">' . $user['role'] . '</div>
                            <div class="user-card-info">
                                <div class="user-card-info-item">
                                    <i class="fas fa-envelope"></i> ' . $user['email'] . '
                                </div>
                                <div class="user-card-info-item">
                                    <i class="fas fa-calendar"></i> Joined ' . $user['registered'] . '
                                </div>
                                <div class="user-card-info-item">
                                    <i class="fas fa-circle ' . ($user['status'] == 'Active' ? 'text-success' : 'text-danger') . '"></i> ' . $user['status'] . '
                                </div>
                            </div>
                            <div class="user-card-actions">
                                <a href="edit-user.php?id=' . $user['user_id'] . '" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete-user.php?id=' . $user['user_id'] . '" class="btn btn-sm btn-danger delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>';
                }
                ?>
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
            const viewButtons = document.querySelectorAll('.btn-group .btn');
            const tableView = document.querySelector('.table-responsive');
            const gridView = document.querySelector('.user-grid');
            
            if (viewButtons.length && tableView && gridView) {
                viewButtons.forEach((button, index) => {
                    button.addEventListener('click', function() {
                        viewButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        
                        if (index === 0) { // List view
                            tableView.style.display = 'block';
                            gridView.style.display = 'none';
                        } else { // Grid view
                            tableView.style.display = 'none';
                            gridView.style.display = 'grid';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>