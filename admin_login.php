<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["admin_id"] = $row["id"];
            $_SESSION["admin_username"] = $row["username"];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบผู้ใช้นี้";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="col-md-4 offset-md-4 bg-white p-4 shadow rounded">
            <h3 class="mb-3">Admin Login</h3>
            <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <input type="text" name="username" class="form-control mb-2" placeholder="ชื่อผู้ใช้" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="รหัสผ่าน" required>
                <button type="submit" class="btn btn-success w-100">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>
</body>

</html>