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

// Fetch all media from the database, sorted by name
$sql = "SELECT id, file_name, file_url, file_type FROM media ORDER BY file_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploads</title>
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
        <h1>Uploaded Media</h1>
        <div class="gallery">
            <?php
            if ($result->num_rows > 0) {
                // Output data for each file
                while($row = $result->fetch_assoc()) {
                    $fileURL = $row['file_url'];
                    $fileType = $row['file_type'];
                    $fileId = $row['id'];
                    echo "<div class='gallery-item'>";
                    echo "<h3>" . htmlspecialchars($row['file_name']) . "</h3>";
                    // Create clickable link to the detailed view page
                    echo "<a href='view.php?id=" . $fileId . "'>";
                    if ($fileType == 'image') {
                        echo "<img src='" . $fileURL . "' alt='" . htmlspecialchars($row['file_name']) . "'>";
                    } elseif ($fileType == 'video') {
                        echo "<video src='" . $fileURL . "' controls></video>";
                    }
                    echo "</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No media files uploaded yet.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
