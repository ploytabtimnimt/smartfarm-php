<?php
session_start();
include("db.php");

$my_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if (!$receiver_id || !$message) {
    echo "ข้อมูลไม่ถูกต้อง";
    exit;
}

$stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $my_id, $receiver_id, $message);
$stmt->execute();

echo "ส่งข้อความเรียบร้อย";
