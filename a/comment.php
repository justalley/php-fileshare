<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login/index.php");
    exit();
}

include('/var/www/html/mysql/db.php');

$user_id = $_SESSION['user_id'];
$sql_own_files = "SELECT id, filename FROM files WHERE uploaded_by = ?";
$stmt_own_files = $mysqli->prepare($sql_own_files);
$stmt_own_files->bind_param("i", $user_id);
$stmt_own_files->execute();
$result_own_files = $stmt_own_files->get_result();

$sql_shared_files = "SELECT f.id, f.filename 
                     FROM files f
                     INNER JOIN user_groups ug ON f.group_id = ug.group_id
                     WHERE ug.user_id = ?";
$stmt_shared_files = $mysqli->prepare($sql_shared_files);
$stmt_shared_files->bind_param("i", $user_id);
$stmt_shared_files->execute();
$result_shared_files = $stmt_shared_files->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = $_POST['comment'];
    $file_id = $_POST['file_id'];

    $sql = "INSERT INTO comments (file_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iis", $file_id, $user_id, $comment);
    $stmt->execute();

    header("Location: /a/dashboard.php");
    exit();
}
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment</title>
</head>
<body>
    <h2>Comment</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="comment">Comment:</label><br>
        <textarea id="comment" name="comment" rows="4" cols="50" required></textarea><br><br>
        <label for="file">Select File:</label>
        <select id="file" name="file_id" required>
            <?php while ($row = $result_own_files->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['filename']; ?></option>
            <?php } ?>
            <?php while ($row = $result_shared_files->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['filename']; ?> (Shared)</option>
            <?php } ?>
        </select><br><br>
        <input type="submit" value="Submit">
    </form>
    <br>
    <a href="/a/dashboard.php" class="button">Dashboard</a>
    <a href="/logout.php" class="button">Logout</a>
</body>
</html>
