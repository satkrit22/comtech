<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = new mysqli($servername, $email, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all uploaded screenshots
$sql = "SELECT screenshot_name, email, upload_time FROM screenshots ORDER BY upload_time DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Screenshots</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .screenshot-image {
            max-width: 150px;
        }
    </style>
</head>
<body>

    <h1>All Uploaded Screenshots</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Screenshot</th>
                <th>email</th>
                <th>Upload Time</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <!-- Display the image -->
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['screenshot_data']) ?>" class="screenshot-image" />
                    </td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['upload_time'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No screenshots uploaded yet.</p>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
