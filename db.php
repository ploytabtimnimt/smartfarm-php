<?php
// db.php

$servername = getenv("DB_HOST") ?: "localhost";
$dbport     = getenv("DB_PORT") ?: "3306";
$dbname     = getenv("DB_NAME") ?: "smartfarm";
$dbusername = getenv("DB_USER") ?: "root";
$dbpassword = getenv("DB_PASSWORD") ?: "";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname, $dbport);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
