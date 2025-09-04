<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $email    = $_POST["email"];
    $message  = $_POST["message"];

    // ตัวอย่าง: แค่แสดงผลออกมา
    echo "<h3>ขอบคุณที่ติดต่อเรา</h3>";
    echo "ชื่อ: $fullname <br>";
    echo "อีเมล: $email <br>";
    echo "ข้อความ: $message <br>";

    // ถ้าต้องการส่งอีเมลให้ Admin → ใช้ PHPMailer ได้
}
