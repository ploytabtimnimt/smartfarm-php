<?php
// กำหนดรหัสผ่านสำหรับ Admin
$admin_password = 'Admin123!';
$admin_hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// กำหนดรหัสผ่านสำหรับ Customer
$customer_password = 'Customer123!';
$customer_hashed_password = password_hash($customer_password, PASSWORD_DEFAULT);

// แสดงผลลัพธ์
echo "<h3>Hashed Password for Admin</h3>";
echo "<p>" . htmlspecialchars($admin_hashed_password) . "</p>";

echo "<h3>Hashed Password for Customer</h3>";
echo "<p>" . htmlspecialchars($customer_hashed_password) . "</p>";
