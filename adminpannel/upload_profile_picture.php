<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Check if file was uploaded
if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/admin/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Get file info
$file_name = $_FILES['profile_image']['name'];
$file_tmp = $_FILES['profile_image']['tmp_name'];
$file_size = $_FILES['profile_image']['size'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Check file extension
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($file_ext, $allowed_extensions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
    exit();
}

// Check file size (max 2MB)
$max_size = 2 * 1024 * 1024; // 2MB
if ($file_size > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size is too large. Maximum size is 2MB.']);
    exit();
}

// Generate unique filename
$new_file_name = 'admin_' . $admin_id . '_' . time() . '.' . $file_ext;
$upload_path = $upload_dir . $new_file_name;

// Move uploaded file
if (move_uploaded_file($file_tmp, $upload_path)) {
    // Update admin profile in database
    $profile_image_path = $upload_path;
    $update_query = "UPDATE admins SET profile_image = '$profile_image_path', updated_at = NOW() WHERE id = $admin_id";
    
    if (mysqli_query($conn, $update_query)) {
        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_query = "INSERT INTO admin_activity (admin_id, activity_type, description, ip_address) 
                     VALUES ($admin_id, 'profile_update', 'Updated profile picture', '$ip')";
        mysqli_query($conn, $log_query);
        
        echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully', 'image_path' => $profile_image_path]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
}
?>