<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
include("header.php");
?>

<div class="container my-3 p-3 bg-white shadow rounded-3">
    <h2>ยินดีต้อนรับ, <?php echo $_SESSION["username"]; ?> 🌱</h2>
    <p>นี่คือ Dashboard ของคุณ</p>
    <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
</div>

</body>

</html>