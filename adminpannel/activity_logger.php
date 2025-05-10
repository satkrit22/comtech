<?php
/**
 * Admin Logger Helper Functions
 * 
 * This file contains functions to log admin activities in the system.
 */

/**
 * Log an admin activity
 * 
 * @param int $admin_id The ID of the admin performing the action
 * @param string $activity_type The type of activity (login, logout, profile_update, etc.)
 * @param string $description A description of the activity
 * @param string $ip_address The IP address of the admin (optional)
 * @return bool True if logging was successful, false otherwise
 */
function log_admin_activity($admin_id, $activity_type, $description, $ip_address = null) {
    global $conn;
    
    if (!$ip_address) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    }
    
    $admin_id = (int)$admin_id;
    $activity_type = mysqli_real_escape_string($conn, $activity_type);
    $description = mysqli_real_escape_string($conn, $description);
    $ip_address = $ip_address ? mysqli_real_escape_string($conn, $ip_address) : 'NULL';
    
    $query = "INSERT INTO admin_activity (admin_id, activity_type, description, ip_address) 
              VALUES ($admin_id, '$activity_type', '$description', " . 
              ($ip_address === 'NULL' ? "NULL" : "'$ip_address'") . ")";
    
    return mysqli_query($conn, $query);
}

/**
 * Get recent activities for the admin
 * 
 * @param int $admin_id The ID of the admin
 * @param int $limit The maximum number of activities to return (default: 10)
 * @return array An array of activity records
 */
function get_admin_activities($admin_id, $limit = 10) {
    global $conn;
    
    $admin_id = (int)$admin_id;
    $limit = (int)$limit;
    
    $query = "SELECT * FROM admin_activity 
              WHERE admin_id = $admin_id 
              ORDER BY created_at DESC 
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $activities = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = $row;
        }
    }
    
    return $activities;
}

/**
 * Update the last login time for the admin
 * 
 * @param int $admin_id The ID of the admin
 * @return bool True if update was successful, false otherwise
 */
function update_admin_last_login($admin_id) {
    global $conn;
    
    $admin_id = (int)$admin_id;
    
    $query = "UPDATE admins SET last_login = NOW() WHERE id = $admin_id";
    
    return mysqli_query($conn, $query);
}

/**
 * Get the count of a specific activity type for the admin
 * 
 * @param int $admin_id The ID of the admin
 * @param string $activity_type The type of activity to count
 * @return int The count of activities
 */
function get_admin_activity_count($admin_id, $activity_type) {
    global $conn;
    
    $admin_id = (int)$admin_id;
    $activity_type = mysqli_real_escape_string($conn, $activity_type);
    
    $query = "SELECT COUNT(*) as count FROM admin_activity 
              WHERE admin_id = $admin_id AND activity_type = '$activity_type'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result)['count'];
    }
    
    return 0;
}
?>