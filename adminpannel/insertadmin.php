<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "comtech";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Admin username and password to be inserted
    $admin_username = 'admin';
    $admin_password = 'P@ssw0rd';

    // Hash the password
    $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");

    // Bind parameters
    $stmt->bind_param("ss", $admin_username, $password_hash);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Data successfully entered'); window.location.href = 'adminform.html';</script>";
    } else {
        echo "Database error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
?>
