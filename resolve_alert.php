<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alert_id = $_POST['alert_id'] ?? 0;
    $new_status = $_POST['resolved_status'] ?? 0;

    // อัปเดตสถานะการแจ้งเตือน
    $stmt = $conn->prepare("UPDATE alerts SET resolved=? WHERE id=?");
    $stmt->bind_param("ii", $new_status, $alert_id);
    $stmt->execute();
}

// Redirect กลับไปหน้า alerts
header("Location: admin_alerts.php");
exit();
