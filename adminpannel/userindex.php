<?php
include 'connect.php';
$result = mysqli_query($conn, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head><title>Users</title><style><?php include 'style.css'; ?></style></head>
<body>
<h2>Users</h2>
<table>
<tr><th>Name</th><th>Email</th><th>Role</th><th>Delete</th></tr>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['is_admin'] ? 'Admin' : 'User' ?></td>
    <td><a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete user?')">Delete</a></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
