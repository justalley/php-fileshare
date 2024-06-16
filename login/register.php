<?php

include('/var/www/html/mysql/db.php');
$base_url = '/var/www/html/';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $error = "Password mismatch...";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This usename already exists.";
        } else {
            $user_folder = $base_url . 'uploads/' . $username;
            if (!is_dir($user_folder)) {
                mkdir($user_folder);
                $default_group = $user_folder . '/default_group';
                mkdir($default_group);
            }

            $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hash);

            if ($stmt->execute()) {
                header("Location: /login/index.php");
                exit();
            } else {
                $error = "Registration error...";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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
    <h2>Registration</h2>
    <?php if (isset($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>
    <form method="post" action="">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Confirm password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" value="Register">
    </form>
    <a href="/login/index.php" class="button">Login</a>
</body>
</html>
