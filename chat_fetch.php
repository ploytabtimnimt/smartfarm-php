<?php
session_start();
include("db.php");

$my_id = $_SESSION['user_id'];
$receiver_id = intval($_GET['receiver_id'] ?? 0);
if (!$receiver_id) {
    echo "";
    exit;
}

$stmt = $conn->prepare("SELECT sender_id, message, created_at FROM chat_messages 
                        WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) 
                        ORDER BY created_at ASC");
$stmt->bind_param("iiii", $my_id, $receiver_id, $receiver_id, $my_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $class = ($row['sender_id'] == $my_id) ? "text-end text-white bg-success p-1 rounded mb-1" : "text-start bg-light p-1 rounded mb-1";
    echo "<div class='$class'>{$row['message']}<br><small>{$row['created_at']}</small></div>";
}
