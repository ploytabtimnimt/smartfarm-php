<?php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_pass = $_POST["old_password"];
    $new_pass = $_POST["new_password"];

    $sql = "SELECT password FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($old_pass, $row["password"])) {
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $update->bind_param("si", $new_hash, $_SESSION["user_id"]);
        $update->execute();

        echo "<script>alert('เปลี่ยนรหัสผ่านเรียบร้อย'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('รหัสผ่านเดิมไม่ถูกต้อง');</script>";
    }
}
?>

<h3>เปลี่ยนรหัสผ่าน</h3>
<form method="POST">
    <input type="password" name="old_password" placeholder="รหัสผ่านเดิม" required><br><br>
    <input type="password" name="new_password" placeholder="รหัสผ่านใหม่" required><br><br>
    <button type="submit">เปลี่ยนรหัสผ่าน</button>
</form>