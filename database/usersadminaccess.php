<?php
// Database connection
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "comtech";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deleting user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM users WHERE id = $delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

// Handle adding new user
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql_add = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
    if ($conn->query($sql_add) === TRUE) {
        echo "New user added successfully.";
    } else {
        echo "Error adding user: " . $conn->error;
    }
}

// Fetch all users
$sql = "SELECT id, name, email, created_at FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .form-container {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<h1>User Management</h1>

<!-- Add User Form -->
<div class="form-container">
    <h3>Add New User</h3>
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <button type="submit" name="add_user" class="btn">Add User</button>
    </form>
</div>

<!-- Display Users -->
<h3>User List</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['email'] . "</td>
                        <td>" . $row['created_at'] . "</td>
                        <td>
                            <a href='?delete_id=" . $row['id'] . "' class='btn btn-danger'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No users found</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
