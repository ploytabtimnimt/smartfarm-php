<?php
$host = getenv("DB_HOST");
$port = getenv("DB_PORT");
$user = getenv("DB_USER");
$pass = getenv("DB_PASSWORD");
$db   = getenv("DB_NAME");

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
