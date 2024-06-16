<?php

$servername = "db";
$username = "user";
$password = "test-password";
$databaseName = "file_sharing";

$mysqli = new mysqli($servername,$username,$password,$databaseName);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} 
//echo "Connected successfully";

?>