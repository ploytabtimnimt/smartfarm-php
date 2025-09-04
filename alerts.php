<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$farm_id = $_GET['farm_id'] ?? 0;

// ดึง alerts ของฟาร์ม
$stmt = $conn->prepare("SELECT * FROM alerts WHERE farm_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$alerts_result = $stmt->get_result();
?>

<?php include("htmlhead.php"); ?>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success rounded mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">SmartFarm</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="data.php?farm_id=<?= $farm_id ?>">Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="alerts.php?farm_id=<?= $farm_id ?>">Alerts</a></li>
                </ul>
                <span class="navbar-text me-2 text-light">สวัสดี, <?= htmlspecialchars($username) ?></span>
                <a href="logout.php" class="btn btn-light text-success">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h4>⚠️ แจ้งเตือนของฟาร์ม</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sensor</th>
                    <th>Issue</th>
                    <th>Resolved</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($alert = $alerts_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($alert['sensor_name']) ?></td>
                        <td><?= htmlspecialchars($alert['issue']) ?></td>
                        <td><?= $alert['resolved'] ? "แก้ไขแล้ว" : "ยังไม่แก้ไข" ?></td>
                        <td><?= $alert['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4">
                        <form method="POST" action="send_alert.php">
                            <input type="hidden" name="farm_id" value="<?= $farm_id ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="sensor_name" placeholder="ชื่อ sensor" required>
                                <input type="text" class="form-control" name="issue" placeholder="ปัญหา" required>
                                <button class="btn btn-danger" type="submit">แจ้งเตือน</button>
                            </div>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>