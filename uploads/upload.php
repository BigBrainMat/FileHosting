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

// Handle the file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;

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
        $fileURL = "http://localhost/fileUpload/uploads/" . $fileName; // Replace with actual URL

        // Insert metadata into the database
        $sql = "INSERT INTO media (file_name, file_url, file_type, file_size, mime_type) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssds", $fileName, $fileURL, $fileType, $fileSize, $mimeType);

        if ($stmt->execute()) {
            // Run Git Commands to commit and push to GitHub
            $gitCommand = "cd D:\xampp\htdocs\fileHosting && git add uploads/ && git commit -m 'Add new image: $fileName' && git push origin main";
            $output = shell_exec($gitCommand);

            if ($output) {
                echo "File uploaded and committed to GitHub successfully!<br>";
            } else {
                echo "Error committing to GitHub.<br>";
            }

            echo "View it here: <br>";
            echo "<a href='view.php?id=" . $conn->insert_id . "' target='_blank'>View File</a>";
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
            <li><a href="index.php">Past Uploads</a></li>
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
