<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "media_uploads";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a random string
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2)); // Generate a secure random string
}

// Handle the file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["file"]["name"]);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Extract file extension
    $randomName = generateRandomString(15) . "." . $fileExtension; // Randomize the filename
    $targetFilePath = $targetDir . $randomName;

    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Move the uploaded file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Get file metadata
        $fileSize = $_FILES["file"]["size"];
        $mimeType = mime_content_type($targetFilePath);
        $fileType = strpos($mimeType, 'image') !== false ? 'image' : 'video';
        
        // Generate a unique file URL
        $fileURL = "http://localhost/fileHosting/uploads/uploads/" . $randomName; // Replace 'localhost' with your domain

        // Insert metadata into the database
        $sql = "INSERT INTO media (file_name, file_url, file_type, file_size, mime_type) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssds", $randomName, $fileURL, $fileType, $fileSize, $mimeType);

        if ($stmt->execute()) {
            $insertedId = $conn->insert_id;
            // Redirect to the view page
            header("Location: view.php?id=" . $insertedId);
            exit();
        } else {
            echo "Error saving to database: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "File upload failed.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Navigation</h2>
        <ul>
            <li><a href="upload.html">Home</a></li>
            <li><a href="pastUploads.php">Past Uploads</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Upload Your Media</h1>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <label for="file">Choose an image or video to upload:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit">Upload</button>
        </form>
    </div>

</body>
</html>
