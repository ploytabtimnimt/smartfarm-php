<?php
require_once __DIR__ . "/config.php";

$conn = new mysqli(
    $config["DB_HOST"],
    $config["DB_USER"],
    $config["DB_PASS"],
    $config["DB_NAME"],
    (int)$config["DB_PORT"]
);

if ($conn->connect_error) {
    die("❌ DB connect failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
