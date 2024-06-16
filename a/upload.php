<?php
session_start();
include('/var/www/html/mysql/db.php');
$base_url = '/var/www/html/';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login/index.php");
    exit();
}
else {
    $user_id = $_SESSION['user_id'];
}

if (isset($_POST['logout'])) {
    header("Location: /logout.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];
    $file_size = $_FILES["file"]["size"];
    $group_name = isset($_POST["group"]) ? $_POST["group"] : "default_group";

    if ($file_size > 4 * 1024 * 1024) {
        echo "Error: File size exceeds the limit of 4 MB.";
        exit();
    }

    $username = $_SESSION['username'];

    if ($group_name != 'default_group') {
        $insert_group_sql = "INSERT INTO file_groups (group_name, creator_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insert_group_sql);
        $stmt->bind_param("si", $file_name, $user_id);
        $stmt->execute();
        $group_id = $stmt->insert_id;
        $stmt->close();
    }

    $group_folder = $base_url . 'uploads/' . $username . '/' . $group_name;

    if (!is_dir($group_folder)) {
        mkdir($group_folder);
    }
    
    $destination = $group_folder . '/' . $file_name;
    move_uploaded_file($file_tmp, $destination);
    $destination = '../uploads/' . $username . '/' . $group_name . '/' . $file_name;

    $insert_file_query = "INSERT INTO files (filename, filepath, uploaded_by, group_id) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($insert_file_query);
    $stmt->bind_param("ssii", $file_name, $destination, $user_id, $group_id);
    $stmt->execute();
    $file_id = $stmt->insert_id;

    echo "File uploaded successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
</head>
<body>
    <h2>Upload File</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required><br><br>
        <label for="group">Group Name (leave empty for default group):</label><br>
        <input type="text" id="group" name="group"><br><br>
        <input type="submit" value="Upload">
    </form>
    <br>
    <form method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
    <br>
    <a href="/a/dashboard.php">Dashboard</a>
</body>
</html>
