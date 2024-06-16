<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['command'])) {
        $command = $_POST['command'];
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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
    <h2>Admin Panel</h2>
    <form method="post" action="">
        <label for="command">Enter Command:</label><br>
        <input type="text" id="command" name="command" required><br><br>
        <input type="submit" value="Execute">
    </form>
    <br>

    <a href="/a/dashboard.php" class="button">Dashboard</a>
    <a href="/logout.php" class="button">Logout</a>
</body>
</html>
