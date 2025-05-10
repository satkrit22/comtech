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

// Check if category ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: categories.php');
    exit();
}

$category_id = (int)$_GET['id'];

// Get category details
$query = "SELECT * FROM categories WHERE id = $category_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    header('Location: categories.php');
    exit();
}

$category = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    
    // Check if category name already exists (excluding current category)
    $check_query = "SELECT id FROM categories WHERE name = '$name' AND id != $category_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['alert'] = [
            'message' => 'Category name already exists. Please use a different name.',
            'type' => 'danger'
        ];
    } else {
        // Update category
        $query = "UPDATE categories SET name = '$name' WHERE id = $category_id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['alert'] = [
                'message' => 'Category updated successfully.',
                'type' => 'success'
            ];
            header('Location: categories.php');
            exit();
        } else {
            $_SESSION['alert'] = [
                'message' => 'Error updating category: ' . mysqli_error($conn),
                'type' => 'danger'
            ];
        }
    }
}

// Available Font Awesome icons for categories
$icons = [
    'fas fa-laptop' => 'Laptop',
    'fas fa-mobile-alt' => 'Mobile',
    'fas fa-headphones' => 'Headphones',
    'fas fa-tv' => 'TV',
    'fas fa-camera' => 'Camera',
    'fas fa-gamepad' => 'Gaming',
    'fas fa-briefcase' => 'Office',
    'fas fa-folder' => 'Default'
];
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category | Comtech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="categories.css">
    <link rel="stylesheet" href="/comtech/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/comtech/assets/css/admin.css">
    <style>
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .icon-item:hover {
            background-color: #f8f9fa;
        }
        
        .icon-item.selected {
            background-color: rgba(67, 97, 238, 0.1);
            border-color: var(--primary);
        }
        
        .icon-item i {
            font-size: 24px;
            margin-bottom: 5px;
            color: #6c757d;
        }
        
        .icon-item.selected i {
            color: var(--primary);
        }
        
        .icon-item span {
            font-size: 12px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'topnav.php'; ?>

            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Category</h1>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="categories.php">Categories</a></li>
                        <li class="breadcrumb-item active">Edit Category</li>
                    </ul>
                </div>
                <div class="page-actions">
                    <a href="categories.php" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Categories
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
                    <h2 class="card-title">Category Information</h2>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label">Category Name</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?php echo $category['name']; ?>" required>
                                </div>
                                
                                
                                <div class="form-group mb-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select">
                                        <option value="Active" >Active</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label">Category Icon</label>
                                    <input type="hidden" id="icon" name="icon" value="<?php echo $category['icon'] ?? 'fas fa-folder'; ?>">
                                    
                                    <div class="selected-icon mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="category-icon me-3">
                                                <i id="selected-icon-preview" class="<?php echo $category['icon'] ?? 'fas fa-folder'; ?>"></i>
                                            </div>
                                            <div id="selected-icon-name">
                                                <?php 
                                                $current_icon = $category['icon'] ?? 'fas fa-folder';
                                                echo isset($icons[$current_icon]) ? $icons[$current_icon] : 'Default Icon';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="icon-grid">
                                        <?php foreach ($icons as $icon => $name): ?>
                                        <div class="icon-item <?php echo $icon === ($category['icon'] ?? 'fas fa-folder') ? 'selected' : ''; ?>" data-icon="<?php echo $icon; ?>">
                                            <i class="<?php echo $icon; ?>"></i>
                                            <span><?php echo $name; ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Category
                            </button>
                            <a href="categories.php" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Icon selection
            const iconItems = document.querySelectorAll('.icon-item');
            const iconInput = document.getElementById('icon');
            const selectedIconPreview = document.getElementById('selected-icon-preview');
            const selectedIconName = document.getElementById('selected-icon-name');
            
            iconItems.forEach(item => {
                item.addEventListener('click', function() {
                    iconItems.forEach(i => i.classList.remove('selected'));
                    this.classList.add('selected');
                    const icon = this.getAttribute('data-icon');
                    iconInput.value = icon;
                    
                    // Update preview
                    selectedIconPreview.className = icon;
                    selectedIconName.textContent = this.querySelector('span').textContent;
                });
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