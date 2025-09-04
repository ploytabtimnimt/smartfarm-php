<?php
// admin_add_user.php
session_start();
include("db.php");

// (แนะนำ) ตรวจสอบสิทธิ์ admin
// if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
//     die("คุณไม่มีสิทธิ์เข้าถึงหน้านี้");
// }

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username  = $_POST["username"];
    $password  = $_POST["password"];
    $farm_name = $_POST["farm_name"];
    $email     = $_POST["email"];

    // hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // เตรียม SQL Insert
    $sql = "INSERT INTO users (username, password, farm_name, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $hashedPassword, $farm_name, $email);

    if ($stmt->execute()) {
        $message = "✅ เพิ่มผู้ใช้สำเร็จแล้ว (username: $username)";
    } else {
        $message = "❌ เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<?php include("header.php"); ?>

<div class="container my-3 p-3 bg-white shadow rounded-3">
    <h2>เพิ่มผู้ใช้ใหม่ (Admin)</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">ชื่อผู้ใช้ (Username)</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">รหัสผ่าน (Password)</label>
            <input type="text" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ชื่อฟาร์ม</label>
            <input type="text" name="farm_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">อีเมลลูกค้า</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">บันทึกผู้ใช้</button>
    </form>
</div>

<?php include("footer.php"); ?>