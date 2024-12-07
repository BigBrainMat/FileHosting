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

// Get the file ID from the URL
$fileId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($fileId > 0) {
    // Fetch file details from the database
    $sql = "SELECT file_name, file_url, file_type, mime_type, file_size FROM media WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $file = $result->fetch_assoc();
        $fileName = $file['file_name'];
        $fileURL = $file['file_url'];
        $fileType = $file['file_type'];
        $mimeType = $file['mime_type'];
        $fileSize = $file['file_size'];
    } else {
        echo "File not found.";
        exit;
    }
    $stmt->close();
} else {
    echo "Invalid file ID.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View <?php echo htmlspecialchars($fileName); ?></title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Function to copy the URL to clipboard
        function copyToClipboard(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            document.execCommand("copy");
        }
    </script>
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
        <h1>Viewing: <?php echo htmlspecialchars($fileName); ?></h1>

        <!-- Display the file -->
        <?php if ($fileType === 'image'): ?>
            <img src="<?php echo htmlspecialchars($fileURL); ?>" alt="<?php echo htmlspecialchars($fileName); ?>">
        <?php elseif ($fileType === 'video'): ?>
            <video src="<?php echo htmlspecialchars($fileURL); ?>" controls></video>
        <?php else: ?>
            <p>Unsupported file type.</p>
        <?php endif; ?>

        <h3>File Metadata:</h3>
        <ul>
            <li><strong>File Name:</strong> <?php echo htmlspecialchars($fileName); ?></li>
            <li><strong>File Type:</strong> <?php echo htmlspecialchars($fileType); ?></li>
            <li><strong>Mime Type:</strong> <?php echo htmlspecialchars($mimeType); ?></li>
            <li><strong>File Size:</strong> <?php echo number_format($fileSize / 1024, 2); ?> KB</li>
        </ul>

        <!-- Copyable Image Link with Copy Button -->
        <h3>Copyable Image Link:</h3>
        <input type="text" id="fileURL" value="<?php echo htmlspecialchars($fileURL); ?>" readonly>
        <button onclick="copyToClipboard('fileURL')">Copy Link</button>

        <p><a href="index.html">Upload another file</a></p>
    </div>
</body>
</html>
