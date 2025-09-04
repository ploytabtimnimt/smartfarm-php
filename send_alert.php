<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farm_id = $_POST['farm_id'] ?? 0;
    $sensor_name = trim($_POST['sensor_name']);
    $issue = trim($_POST['issue']);

    if (!$sensor_name || !$issue) {
        die("กรุณากรอกข้อมูล sensor และ issue ให้ครบ");
    }

    // ตรวจสอบสิทธิ์ลูกค้า
    $stmt = $conn->prepare("SELECT id FROM farms WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $farm_id, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        die("คุณไม่มีสิทธิ์แจ้งเตือนในฟาร์มนี้");
    }

    // เพิ่ม alert
    $stmt = $conn->prepare("INSERT INTO alerts (farm_id, sensor_name, issue, resolved, created_at) VALUES (?, ?, ?, 0, NOW())");
    $stmt->bind_param("iss", $farm_id, $sensor_name, $issue);
    $stmt->execute();

    header("Location: customer_dashboard.php");
    exit();
}
