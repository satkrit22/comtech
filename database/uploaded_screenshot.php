<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comtech";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if file is uploaded
if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
    $fileName = $_FILES['payment_screenshot']['name'];
    $fileTmpName = $_FILES['payment_screenshot']['tmp_name'];
    $fileType = $_FILES['payment_screenshot']['type'];
    $fileContent = file_get_contents($fileTmpName); // Read file content

    // Insert file into the database
    $sql = "INSERT INTO screenshots (screenshot_name, screenshot_data) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sb", $fileName, $fileContent); // "s" for string, "b" for blob
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $uploadSuccess = true;
    } else {
        $uploadSuccess = false;
    }

    $stmt->close();
    $conn->close();
} else {
    $uploadSuccess = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <style>
        /* Styles for the thank-you message */
        .thank-you-message {
            font-size: 2em;
            color: #fff;
            background-color: #4CAF50;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            margin: 50px auto;
            opacity: 0;
            transform: translateY(30px);
            animation: slideUp 1s forwards, fadeIn 2s forwards;
        }

        /* Animation for sliding up and fading in */
        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

<?php if ($uploadSuccess): ?>
    <div class="thank-you-message">Thank you for uploading your payment screenshot!</div>
<?php else: ?>
    <div class="thank-you-message" style="background-color: #f44336;">Error uploading file or no file uploaded!</div>
<?php endif; ?>

</body>
</html>
