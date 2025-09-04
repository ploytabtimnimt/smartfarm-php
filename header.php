<?php
// ตรวจสอบว่า session_start() ยังไม่ได้ถูกเรียกใช้ เพื่อป้องกัน error
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// ดึงข้อมูลผู้ใช้จาก session
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$user_role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .flowchart-step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .flowchart-arrow {
            text-align: center;
            font-size: 2rem;
            line-height: 0;
            color: #198754;
        }

        @media (min-width: 768px) {
            .flowchart-arrow {
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
            }

            .flowchart-arrow:after {
                content: '▼';
            }
        }
    </style>
</head>

<body class="bg-light">

    <div class="container my-3 p-3 bg-white shadow rounded-3">
        <nav class="navbar navbar-expand-lg navbar-dark bg-success rounded mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">SmartFarm</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="services.php">บริการ</a></li>
                        <li class="nav-item"><a class="nav-link" href="use.php">วิธีการใช้งาน</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">ติดต่อเรา</a></li>

                        <?php if (isset($user_id)): // แสดงรายการเมนูสำหรับผู้ใช้ที่ล็อกอินแล้ว 
                        ?>
                            <li class="nav-item"><a class="nav-link" href="data.php">Data</a></li>

                            <?php if ($user_role === 'admin'): // แสดงเมนูสำหรับ Admin เท่านั้น 
                            ?>
                                <li class="nav-item"><a class="nav-link" href="admin_alerts.php">Alerts</a></li>
                                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">AdminDashboard</a></li>
                            <?php elseif ($user_role === 'customer'): // แสดงเมนูสำหรับ Customer เท่านั้น 
                            ?>
                                <li class="nav-item"><a class="nav-link" href="alerts.php">Alerts</a></li>
                                <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">MyDashboardFarm
                                    <?php endif; ?>
                                <?php endif; ?>
                    </ul>

                    <?php if (isset($user_id)): // แสดงปุ่มสำหรับผู้ใช้ที่ล็อกอินแล้ว 
                    ?>
                        <span class="navbar-text me-2 text-light">สวัสดี, <?= htmlspecialchars($username) ?></span>
                        <a href="logout.php" class="btn btn-light text-success">Logout</a>
                    <?php else: // แสดงปุ่มสำหรับผู้ใช้ที่ยังไม่ล็อกอิน 
                    ?>
                        <button class="btn btn-light text-success" data-bs-toggle="modal" data-bs-target="#authModal">
                            เข้าสู่ระบบ / สมัครสมาชิก
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>

    <div class="modal fade" id="authModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">เข้าสู่ระบบ</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">สมัครสมาชิก</button>
                        </li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                            <form action="login.php" method="POST">
                                <div class="mb-3">
                                    <label for="login-username" class="form-label">ชื่อผู้ใช้</label>
                                    <input type="text" class="form-control" id="login-username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="login-password" class="form-label">รหัสผ่าน</label>
                                    <input type="password" class="form-control" id="login-password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">เข้าสู่ระบบ</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                            <p class="text-center mt-3">หากคุณสนใจใช้บริการ SmartFarm กรุณากรอกแบบฟอร์มเพื่อส่งข้อมูลให้ทีมงาน</p>
                            <a href="member.php" class="btn btn-primary w-100">กรอกแบบฟอร์มเพื่อสมัครสมาชิก</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>