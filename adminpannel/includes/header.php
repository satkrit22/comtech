<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="/comtech/assets/css/admin-dashboard.css">
    
    <!-- Page Specific CSS -->
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    switch ($current_page) {
        case 'orders.php':
            echo '<link rel="stylesheet" href="/comtech/assets/css/orders.css">';
            break;
        case 'products.php':
            echo '<link rel="stylesheet" href="/comtech/assets/css/products.css">';
            break;
        case 'users.php':
            echo '<link rel="stylesheet" href="/comtech/assets/css/users.css">';
            break;
        case 'categories.php':
            echo '<link rel="stylesheet" href="/comtech/assets/css/categories.css">';
            break;
    }
    ?>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>
    
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-cube"></i>
                    AdminPanel
                </div>
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">MAIN</div>
                    <a href="dashboard.php" class="menu-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                        <div class="menu-icon"><i class="fas fa-tachometer-alt"></i></div>
                        <div class="menu-text">Dashboard</div>
                    </a>
                    <a href="orders.php" class="menu-item <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                        <div class="menu-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="menu-text">Orders</div>
                        <div class="menu-badge">New</div>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">CATALOG</div>
                    <a href="products.php" class="menu-item <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                        <div class="menu-icon"><i class="fas fa-box"></i></div>
                        <div class="menu-text">Products</div>
                    </a>
                    <a href="categories.php" class="menu-item <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                        <div class="menu-icon"><i class="fas fa-tags"></i></div>
                        <div class="menu-text">Categories</div>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">USERS</div>
                    <a href="users.php" class="menu-item <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                        <div class="menu-icon"><i class="fas fa-users"></i></div>
                        <div class="menu-text">Users</div>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">SETTINGS</div>
                    <a href="settings.php" class="menu-item <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                        <div class="menu-icon"><i class="fas fa-cog"></i></div>
                        <div class="menu-text">General Settings</div>
                    </a>
                    <a href="logout.php" class="menu-item">
                        <div class="menu-icon"><i class="fas fa-sign-out-alt"></i></div>
                        <div class="menu-text">Logout</div>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                </button>
                
                <div class="header-search">
                </div>
                
                <div class="header-actions">
                    <div class="header-action-item notification-toggle">
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <h6>Notifications</h6>
                                <a href="#">Mark all as read</a>
                            </div>
                            <div class="dropdown-body">
                                <a href="#" class="dropdown-item">
                                    <div class="dropdown-item-icon bg-primary">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="dropdown-item-content">
                                        <p>New order received</p>
                                        <small>15 minutes ago</small>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <div class="dropdown-item-icon bg-info">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="dropdown-item-content">
                                        <p>New user registered</p>
                                        <small>2 hours ago</small>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <div class="dropdown-item-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="dropdown-item-content">
                                        <p>Task completed</p>
                                        <small>4 hours ago</small>
                                    </div>
                                </a>
                            </div>
                            <div class="dropdown-footer">
                                <a href="#">View all notifications</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="header-action-item">
                    </div>
                    
                    <div class="user-dropdown">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=4361ee&color=fff" alt="User" class="user-avatar">
                        <div class="user-info">
                            <div class="user-name">Admin User</div>
                            <div class="user-role">Administrator</div>
                        </div>
                        <div class="dropdown-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="admin-content">