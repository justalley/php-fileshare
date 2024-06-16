<?php
session_start();

include('/var/www/html/mysql/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
    
    $stmt->bind_param("s", $username);
    
    $stmt->execute();
    
    $stmt->bind_result($user_id, $db_username, $db_password_hash, $role);
    
    if ($stmt->fetch()) {
        if (password_verify($password, $db_password_hash)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;
            
            if ($role == "admin") {
                header("Location: /a/admin.php");
                exit();
            }
            else {
                header("Location: /a/dashboard.php");
                exit();
            }
        } else {
            $error = "Wrong creds...";
        }
    } else {
        $error = "Wrong creds...";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorization</title>
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
    <h2>Authorization</h2>
    <?php if (isset($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>
    <form method="post" action="">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Log In">
    </form>
    <a href="/login/register.php" class="button">Register</a>
</body>
</html>
