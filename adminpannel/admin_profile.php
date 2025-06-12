<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Get admin details
$query = "SELECT * FROM admins WHERE id = $admin_id";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

// Process profile update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    
    // Update profile
    $update_query = "UPDATE admins SET 
                    name = '$name', 
                    email = '$email', 
                    phone = '$phone', 
                    address = '$address', 
                    bio = '$bio',
                    updated_at = NOW()
                    WHERE id = $admin_id";
    
    if (mysqli_query($conn, $update_query)) {
        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_query = "INSERT INTO admin_activity (admin_id, activity_type, description, ip_address) 
                     VALUES ($admin_id, 'profile_update', 'Updated profile information', '$ip')";
        mysqli_query($conn, $log_query);
        
        $profile_success = 'Profile updated successfully.';
        
        // Refresh admin data
        $result = mysqli_query($conn, $query);
        $admin = mysqli_fetch_assoc($result);
    } else {
        $profile_error = 'Error updating profile: ' . mysqli_error($conn);
    }
}

// Process password update
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (!password_verify($current_password, $admin['password_hash'])) {
        $password_error = 'Current password is incorrect.';
    } elseif ($new_password !== $confirm_password) {
        $password_error = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $password_error = 'Password must be at least 6 characters long.';
    } else {
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $update_query = "UPDATE admins SET password_hash = '$hashed_password', updated_at = NOW() WHERE id = $admin_id";
        
        if (mysqli_query($conn, $update_query)) {
            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_query = "INSERT INTO admin_activity (admin_id, activity_type, description, ip_address) 
                         VALUES ($admin_id, 'password_change', 'Changed account password', '$ip')";
            mysqli_query($conn, $log_query);
            
            $password_success = 'Password updated successfully.';
        } else {
            $password_error = 'Error updating password: ' . mysqli_error($conn);
        }
    }
}

// Get admin activity log
$activity_query = "SELECT * FROM admin_activity WHERE admin_id = $admin_id ORDER BY created_at DESC LIMIT 10";
$activity_result = mysqli_query($conn, $activity_query);

// Log this profile view
$ip = $_SERVER['REMOTE_ADDR'];
$log_query = "INSERT INTO admin_activity (admin_id, activity_type, description, ip_address) 
             VALUES ($admin_id, 'profile_view', 'Viewed admin profile page', '$ip')";
mysqli_query($conn, $log_query);

// Get system stats
$stats = [
    'total_orders' => 0,
    'total_products' => 0,
    'total_users' => 0,
    'total_revenue' => 0
];

// Get total orders
$orders_query = "SELECT COUNT(*) as count FROM orders";
$orders_result = mysqli_query($conn, $orders_query);
if ($orders_result) {
    $stats['total_orders'] = mysqli_fetch_assoc($orders_result)['count'];
}

// Get total products
$products_query = "SELECT COUNT(*) as count FROM products";
$products_result = mysqli_query($conn, $products_query);
if ($products_result) {
    $stats['total_products'] = mysqli_fetch_assoc($products_result)['count'];
}

// Get total users
$users_query = "SELECT COUNT(*) as count FROM users";
$users_result = mysqli_query($conn, $users_query);
if ($users_result) {
    $stats['total_users'] = mysqli_fetch_assoc($users_result)['count'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .profile-header {
            background-color: var(--primary);
            color: white;
            padding: 40px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0) 100%);
            z-index: 1;
        }
        
        .profile-header-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }
        
        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            margin-right: 30px;
            object-fit: cover;
        }
        
        .profile-info h1 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .profile-info p {
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }
        
        .profile-tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        
        .profile-tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
        }
        
        .profile-content {
            display: none;
        }
        
        .profile-content.active {
            display: block;
        }
        
        .avatar-upload {
            position: relative;
            max-width: 150px;
            margin: 0 auto 20px;
        }
        
        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--border-color);
        }
        
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-edit {
            position: absolute;
            right: 5px;
            bottom: 5px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
        }
        
        .avatar-edit input {
            display: none;
        }
        
        .activity-item {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-content {
            display: flex;
            align-items: center;
        }
        
        .activity-details {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            margin-bottom: 3px;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
    </style>
    <link rel="stylesheet" href="/comtech/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/comtech/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-nav">
    <div class="top-nav-left">
        <div class="search-container">
            
        </div>
    </div>
    
    <div class="top-nav-right">
               
        <div class="nav-item">
            <div class="admin-profile">
                <button class="profile-btn">
                    <span>Admin</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu profile-menu">
                    <a href="admin_profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="adminlogout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Admin Profile</h1>
                    
                </div>
            </div>

            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-content">
                    <!-- <img src="/comtech/assets/img/<?php echo !empty($admin['profile_image']) ? $admin['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin['name'] ?? 'Administrator') . '&background=4361ee&color=fff&size=120'; ?>" alt="Admin" class="profile-avatar-large"> -->
                    <div class="profile-info">
                        <h1><?php echo $admin['name'] ?? 'Administrator'; ?></h1>
                        <p><i class="fas fa-envelope"></i> <?php echo $admin['email'] ?? 'admin@comtech.com'; ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo $admin['phone'] ?? 'Not provided'; ?></p>
                        <p><i class="fas fa-shield-alt"></i> System Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Profile Stats -->
            <div class="profile-stats">
                <div class="stat-box">
                    <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">NPR.<?php echo number_format($stats['total_revenue'], 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="card">
                <div class="card-body">
                    <div class="profile-tabs">
                        <div class="profile-tab active" data-tab="profile">Profile Information</div>
                        <div class="profile-tab" data-tab="security">Security</div>
                        <div class="profile-tab" data-tab="activity">Activity Log</div>
                    </div>
                    
                    <!-- Profile Information -->
                    <div class="profile-content active" id="profile-content">
                        <?php if (isset($profile_success)): ?>
                        <div class="alert alert-success">
                            <?php echo $profile_success; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($profile_error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $profile_error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST">
                            <div class="row">
                                <!-- <div class="col-md-4">
                                    <div class="avatar-upload">
                                        <div class="avatar-preview">
                                            <img src="<?php echo !empty($admin['profile_image']) ? $admin['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin['name'] ?? 'Administrator') . '&background=4361ee&color=fff'; ?>" alt="Admin Avatar" id="avatar-preview-img">
                                        </div>
                                        <div class="avatar-edit">
                                            <label for="avatar-upload" title="Upload Avatar">
                                                <i class="fas fa-camera"></i>
                                                <input type="file" id="avatar-upload" accept="image/*">
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-center mb-4">Profile Picture</p>
                                </div> -->
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" id="name" name="name" class="form-control" value="<?php echo $admin['name'] ?? ''; ?>" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $admin['email'] ?? ''; ?>" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo $admin['phone'] ?? ''; ?>" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea id="address" name="address" class="form-control" rows="2"><?php echo $admin['address'] ?? ''; ?></textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea id="bio" name="bio" class="form-control" rows="3"><?php echo $admin['bio'] ?? ''; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" name="update_profile" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Security Settings -->
                    <div class="profile-content" id="security-content">
                        <?php if (isset($password_success)): ?>
                        <div class="alert alert-success">
                            <?php echo $password_success; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($password_error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $password_error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="mb-4">Change Password</h3>
                                <form action="" method="POST">
                                    <div class="form-group mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" name="update_password" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Change Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-4">Security Information</h3>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p><strong>Username:</strong> <?php echo $admin['username']; ?></p>
                                        <p><strong>Last Login:</strong> 
                                            <?php echo $admin['last_login'] ? date('F j, Y, g:i a', strtotime($admin['last_login'])) : 'Not available'; ?>
                                        </p>
                                        <p><strong>Account Created:</strong> 
                                            <?php echo $admin['created_at'] ? date('F j, Y', strtotime($admin['created_at'])) : 'Not available'; ?>
                                        </p>
                                        <p><strong>IP Address:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
                                        <hr>
                                        <h5>Security Tips</h5>
                                        <ul>
                                            <li>Use a strong password with a mix of letters, numbers, and symbols</li>
                                            <li>Change your password regularly</li>
                                            <li>Don't share your login credentials with others</li>
                                            <li>Log out when using public computers</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Log -->
                    <div class="profile-content" id="activity-content">
                        <h3 class="mb-4">Recent Activity</h3>
                        <div class="activity-list">
                            <?php
                            if ($activity_result && mysqli_num_rows($activity_result) > 0) {
                                while ($activity = mysqli_fetch_assoc($activity_result)) {
                                    $icon = 'fa-history';
                                    $title = 'Activity';
                                    
                                    switch ($activity['activity_type']) {
                                        case 'login':
                                            $icon = 'fa-sign-in-alt';
                                            $title = 'Login';
                                            break;
                                        case 'logout':
                                            $icon = 'fa-sign-out-alt';
                                            $title = 'Logout';
                                            break;
                                        case 'profile_update':
                                            $icon = 'fa-user-edit';
                                            $title = 'Profile Update';
                                            break;
                                        case 'password_change':
                                            $icon = 'fa-key';
                                            $title = 'Password Change';
                                            break;
                                        case 'order_update':
                                            $icon = 'fa-shopping-cart';
                                            $title = 'Order Update';
                                            break;
                                        case 'product_update':
                                            $icon = 'fa-box';
                                            $title = 'Product Update';
                                            break;
                                        case 'profile_view':
                                            $icon = 'fa-user';
                                            $title = 'Profile View';
                                            break;
                                    }
                                    ?>
                                    <div class="activity-item">
                                        <div class="activity-content">
                                            <div class="activity-icon">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </div>
                                            <div class="activity-details">
                                                <div class="activity-title"><?php echo $title; ?></div>
                                                <div class="activity-description"><?php echo $activity['description']; ?></div>
                                                <div class="activity-time">
                                                    <i class="far fa-clock"></i> <?php echo date('F j, Y, g:i a', strtotime($activity['created_at'])); ?>
                                                    <?php if (!empty($activity['ip_address'])): ?>
                                                    <span class="ml-2"><i class="fas fa-globe"></i> <?php echo $activity['ip_address']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="text-center p-4">No activity recorded yet.</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabs = document.querySelectorAll('.profile-tab');
            const contents = document.querySelectorAll('.profile-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to current tab and content
                    this.classList.add('active');
                    document.getElementById(tabId + '-content').classList.add('active');
                });
            });
            
            // Avatar upload preview
            const avatarUpload = document.getElementById('avatar-upload');
            const avatarPreview = document.getElementById('avatar-preview-img');
            
            if (avatarUpload) {
                avatarUpload.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                        };
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            // Password confirmation validation
            const passwordForm = document.querySelector('form[name="update_password"]');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('New passwords do not match.');
                    }
                });
            }
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.display = 'none';
                });
            }, 5000);
            
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