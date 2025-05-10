<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Function to execute SQL and handle errors
function execute_sql($conn, $sql, $description) {
    echo "<p>Attempting to $description... ";
    if (mysqli_query($conn, $sql)) {
        echo "<span style='color: green;'>Success!</span></p>";
        return true;
    } else {
        echo "<span style='color: red;'>Failed: " . mysqli_error($conn) . "</span></p>";
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin Table | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .setup-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .setup-footer {
            text-align: center;
            margin-top: 30px;
        }
        
        .sql-code {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin-bottom: 20px;
            white-space: pre-wrap;
            overflow-x: auto;
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
            <!-- Top Navigation -->
            <?php include 'topnav.php'; ?>

            <div class="setup-container">
                <div class="setup-header">
                    <h1>Update Admin Table</h1>
                    <p>This script will add profile fields to the admins table.</p>
                </div>

                <div class="setup-card">
                    <h2><i class="fas fa-table"></i> Updating Admin Table</h2>
                    
                    <?php
                    // Check if admins table exists
                    $check_table = "SHOW TABLES LIKE 'admins'";
                    $table_result = mysqli_query($conn, $check_table);
                    
                    if (mysqli_num_rows($table_result) > 0) {
                        echo "<p>Admins table exists. <span style='color: green;'>✓</span></p>";
                        
                        // Check columns in admins table
                        $check_columns = "SHOW COLUMNS FROM admins";
                        $columns_result = mysqli_query($conn, $check_columns);
                        
                        if ($columns_result) {
                            $existing_columns = [];
                            while ($column = mysqli_fetch_assoc($columns_result)) {
                                $existing_columns[] = $column['Field'];
                            }
                            
                            // Add missing columns
                            $required_columns = [
                                'name' => "ALTER TABLE admins ADD COLUMN name VARCHAR(100) AFTER username",
                                'email' => "ALTER TABLE admins ADD COLUMN email VARCHAR(100) AFTER name",
                                'phone' => "ALTER TABLE admins ADD COLUMN phone VARCHAR(20) AFTER email",
                                'address' => "ALTER TABLE admins ADD COLUMN address TEXT AFTER phone",
                                'bio' => "ALTER TABLE admins ADD COLUMN bio TEXT AFTER address",
                                'profile_image' => "ALTER TABLE admins ADD COLUMN profile_image VARCHAR(255) AFTER bio",
                                'last_login' => "ALTER TABLE admins ADD COLUMN last_login DATETIME AFTER profile_image",
                                'created_at' => "ALTER TABLE admins ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                                'updated_at' => "ALTER TABLE admins ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
                            ];
                            
                            $added_columns = false;
                            foreach ($required_columns as $column => $sql) {
                                if (!in_array($column, $existing_columns)) {
                                    execute_sql($conn, $sql, "add column '$column' to admins table");
                                    $added_columns = true;
                                }
                            }
                            
                            if (!$added_columns) {
                                echo "<p>All required columns already exist in the admins table. <span style='color: green;'>✓</span></p>";
                            }
                            
                            // Create admin_activity table
                            $activity_table_sql = "CREATE TABLE IF NOT EXISTS admin_activity (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                admin_id INT NOT NULL,
                                activity_type VARCHAR(50) NOT NULL,
                                description TEXT NOT NULL,
                                ip_address VARCHAR(45),
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                INDEX (admin_id),
                                INDEX (activity_type),
                                INDEX (created_at)
                            )";
                            
                            execute_sql($conn, $activity_table_sql, "create admin_activity table");
                            
                            // Update admin info if name is not set
                            $check_admin_info = "SELECT * FROM admins WHERE username = 'admin' AND (name IS NULL OR name = '')";
                            $admin_info_result = mysqli_query($conn, $check_admin_info);
                            
                            if ($admin_info_result && mysqli_num_rows($admin_info_result) > 0) {
                                $update_admin_sql = "UPDATE admins SET 
                                    name = 'Administrator',
                                    email = 'admin@comtech.com',
                                    phone = '+977 1234567890',
                                    last_login = NOW()
                                    WHERE username = 'admin'";
                                
                                execute_sql($conn, $update_admin_sql, "update admin information");
                            }
                            
                        } else {
                            echo "<p style='color: red;'>Error checking columns: " . mysqli_error($conn) . "</p>";
                        }
                    } else {
                        echo "<p style='color: red;'>Admins table does not exist. Please run the admin setup script first.</p>";
                    }
                    ?>
                </div>

                <div class="setup-card">
                    <h2><i class="fas fa-code"></i> SQL Statements Used</h2>
                    
                    <div class="sql-code">
-- Add columns to admins table
ALTER TABLE admins ADD COLUMN name VARCHAR(100) AFTER username;
ALTER TABLE admins ADD COLUMN email VARCHAR(100) AFTER name;
ALTER TABLE admins ADD COLUMN phone VARCHAR(20) AFTER email;
ALTER TABLE admins ADD COLUMN address TEXT AFTER phone;
ALTER TABLE admins ADD COLUMN bio TEXT AFTER address;
ALTER TABLE admins ADD COLUMN profile_image VARCHAR(255) AFTER bio;
ALTER TABLE admins ADD COLUMN last_login DATETIME AFTER profile_image;
ALTER TABLE admins ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE admins ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Create admin_activity table
CREATE TABLE IF NOT EXISTS admin_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (admin_id),
    INDEX (activity_type),
    INDEX (created_at)
);

-- Update admin information
UPDATE admins SET 
    name = 'Administrator',
    email = 'admin@comtech.com',
    phone = '+977 1234567890',
    last_login = NOW()
    WHERE username = 'admin';
                    </div>
                </div>

                <div class="setup-footer">
                    <a href="admin_profile.php" class="btn btn-primary">
                        <i class="fas fa-user"></i> Go to Admin Profile
                    </a>
                    <a href="index.php" class="btn btn-light ml-2">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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