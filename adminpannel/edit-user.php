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

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$user_id = (int)$_GET['id'];

// Get user details
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    header('Location: users.php');
    exit();
}

$user = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    
    // Check if email already exists (excluding current user)
    $check_query = "SELECT id FROM users WHERE Email = '$email' AND id != $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['alert'] = [
            'message' => 'Email already exists. Please use a different email.',
            'type' => 'danger'
        ];
    } else {
        // Update user
        $update_query = "UPDATE users SET Name = '$name', Email = '$email', Phone = '$phone' WHERE id = $user_id";
        
        // If password is provided, update it
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET Name = '$name', Email = '$email', Phone = '$phone', password = '$password' WHERE id = $user_id";
        }
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['alert'] = [
                'message' => 'User updated successfully.',
                'type' => 'success'
            ];
            header('Location: users.php');
            exit();
        } else {
            $_SESSION['alert'] = [
                'message' => 'Error updating user: ' . mysqli_error($conn),
                'type' => 'danger'
            ];
        }
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="users.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <?php include 'topnav.php'; ?>
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit User</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/comtech/adminpannel/users.php">Users</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="users.php" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Users
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
                    <h2 class="card-title">User Information</h2>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group mb-4">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $user['Name']; ?>" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $user['Email']; ?>" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo $user['Phone']; ?>" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password" class="form-label">Password (Leave blank to keep current password)</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update User
                            </button>
                            <a href="users.php" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== '' && password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match.');
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