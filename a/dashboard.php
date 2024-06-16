<?php
session_start();

include('/var/www/html/mysql/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: /login/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql_own_files = "SELECT f.id, f.filename, f.filepath, GROUP_CONCAT(CONCAT(u.username, ': ', c.comment) SEPARATOR '<br>') AS comments
                  FROM files f
                  LEFT JOIN comments c ON f.id = c.file_id
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE f.uploaded_by = ?
                  GROUP BY f.id";
$stmt_own_files = $mysqli->prepare($sql_own_files);
$stmt_own_files->bind_param("i", $user_id);
$stmt_own_files->execute();
$result_own_files = $stmt_own_files->get_result();

$sql_shared_files = "SELECT f.id, f.filename, f.filepath, GROUP_CONCAT(CONCAT(u.username, ': ', c.comment) SEPARATOR '<br>') AS comments
                     FROM files f
                     INNER JOIN user_groups ug ON f.group_id = ug.group_id
                     LEFT JOIN comments c ON f.id = c.file_id
                     LEFT JOIN users u ON c.user_id = u.id
                     WHERE ug.user_id = ?
                     GROUP BY f.id";
$stmt_shared_files = $mysqli->prepare($sql_shared_files);
$stmt_shared_files->bind_param("i", $user_id);
$stmt_shared_files->execute();
$result_shared_files = $stmt_shared_files->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Your files</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 200px;">Filename</th>
                <th>Comments</th>
                <th style="width: 100px;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_own_files->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['filename']; ?></td>
                <td><?php echo $row['comments']; ?></td>
                <td>
                    <a href="<?php echo $row['filepath']; ?>" class="button" download>Download</a>
                    <a href="/a/share-group.php" class="button">Share</a>
                </td>   
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Files you have access to</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 200px;">Filename</th>
                <th>Comments</th>
                <th style="width: 100px;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_shared_files->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['filename']; ?></td>
                <td><?php echo $row['comments']; ?></td>
                <td>
                    <a href="<?php echo $row['filepath']; ?>" class="button" download>Download</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="/a/comment.php" class="button">Comment</a>
    <a href="/a/upload.php" class="button">Upload File</a>
    <a href="/logout.php" class="button">Logout</a>
</body>
</html>
