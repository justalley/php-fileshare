<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login/index.php");
    exit();
}

include('/var/www/html/mysql/db.php');

$user_id = $_SESSION['user_id'];
$sql_groups = "SELECT id, group_name FROM file_groups WHERE creator_id = ?";
$stmt_groups = $mysqli->prepare($sql_groups);
$stmt_groups->bind_param("i", $user_id);
$stmt_groups->execute();
$result_groups = $stmt_groups->get_result();

$sql_users = "SELECT id, username FROM users WHERE id != ?";
$stmt_users = $mysqli->prepare($sql_users);
$stmt_users->bind_param("i", $user_id);
$stmt_users->execute();
$result_users = $stmt_users->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST['group_id'];
    $user_id_share = $_POST['user_id'];

    $sql_insert = "INSERT INTO user_groups (group_id, user_id) VALUES (?, ?)";
    $stmt_insert = $mysqli->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $group_id, $user_id_share);
    $stmt_insert->execute();

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
    <title>Share Group</title>
    <style>
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
    <h2>Share Group</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="group">Select File:</label>
        <select id="group" name="group_id" required>
            <?php while ($row = $result_groups->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['group_name']; ?></option>
            <?php } ?>
        </select><br><br>
        <label for="user">Select User:</label>
        <select id="user" name="user_id" required>
            <?php while ($row = $result_users->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
            <?php } ?>
        </select><br><br>
        <input type="submit" value="Share">
    </form>
    <br>
    <a href="/a/dashboard.php" class="button">Dashboard</a><br>
    <a href="/a/upload.php" class="button">Upload File</a><br>
    <a href="/logout.php" class="button">Logout</a>
</body>
</html>
