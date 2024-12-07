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
    $sql = "SELECT file_name, file_url, file_type, mime_type FROM media WHERE id = ?";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        img, video {
            max-width: 90%;
            max-height: 500px;
        }
        .copy-box {
            margin-top: 20px;
        }
        input.copy-link {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }
        button.copy-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button.copy-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Viewing: <?php echo htmlspecialchars($fileName); ?></h1>
    <?php if ($fileType === 'image'): ?>
        <img src="<?php echo htmlspecialchars($fileURL); ?>" alt="<?php echo htmlspecialchars($fileName); ?>">
    <?php elseif ($fileType === 'video'): ?>
        <video src="<?php echo htmlspecialchars($fileURL); ?>" controls></video>
    <?php else: ?>
        <p>Unsupported file type.</p>
    <?php endif; ?>

    <div class="copy-box">
        <h3>Embed Link</h3>
        <input type="text" class="copy-link" value="<?php echo htmlspecialchars($fileURL); ?>" id="fileLink" readonly>
        <button class="copy-button" onclick="copyToClipboard()">Copy Link</button>
    </div>

    <p><a href="index.html">Upload another file</a></p>

    <script>
        function copyToClipboard() {
            const copyText = document.getElementById("fileLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            alert("Link copied: " + copyText.value);
        }
    </script>
</body>
</html>
