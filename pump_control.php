<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$farm_id = $_POST['farm_id'] ?? 0;
$action = $_POST['action'] ?? '';
$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// ตรวจสอบสิทธิ์
if ($user_role === 'customer') {
    $stmt = $conn->prepare("SELECT id FROM farms WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $farm_id, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        die("คุณไม่มีสิทธิ์ควบคุมปั๊มของฟาร์มนี้");
    }
}

// กำหนดค่า pump
$status = ($action === 'on') ? 1 : 0;

// บันทึกสถานะ pump
$stmt = $conn->prepare("INSERT INTO pump_status (farm_id, status, updated_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $farm_id, $status);
$stmt->execute();

// กลับไปหน้าที่มาก่อน
if ($user_role === 'admin') {
    header("Location: admin_dashboard.php");
} else {
    header("Location: customer_dashboard.php");
}
exit();
