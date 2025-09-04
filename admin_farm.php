<?php
session_start();
include("db.php");
include("header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_name = $_SESSION['username'];
$farm_id = $_GET['farm_id'] ?? 0;

// ถ้ามี farm_id เลือกฟาร์มนั้น
if ($farm_id) {
    $stmt = $conn->prepare("SELECT f.*, u.username FROM farms f JOIN users u ON f.owner_id=u.id WHERE f.id=? LIMIT 1");
    $stmt->bind_param("i", $farm_id);
    $stmt->execute();
    $farm = $stmt->get_result()->fetch_assoc();
    if (!$farm) die("ไม่พบฟาร์มนี้");

    // Sensor ล่าสุด
    $stmt = $conn->prepare("SELECT t1.value, t2.type FROM sensor_data t1 JOIN sensors t2 ON t1.sensor_id = t2.id WHERE t2.farm_id = ? AND t1.recorded_at = (SELECT MAX(recorded_at) FROM sensor_data WHERE sensor_id = t1.sensor_id)");
    $stmt->bind_param("i", $farm_id);
    $stmt->execute();
    $sensor_data_result = $stmt->get_result();
    $sensor_data = [];
    while ($row = $sensor_data_result->fetch_assoc()) {
        $sensor_data[$row['type']] = $row['value'];
    }

    // Pump ล่าสุด
    $stmt = $conn->prepare("SELECT status FROM pumps WHERE farm_id=? ORDER BY updated_at DESC LIMIT 1");
    $stmt->bind_param("i", $farm_id);
    $stmt->execute();
    $pump_status = $stmt->get_result()->fetch_assoc();
    $pump_on = ($pump_status['status'] ?? 'off') === 'on';

    // ข้อมูล Sensor ย้อนหลัง
    $stmt = $conn->prepare("SELECT * FROM sensor_data WHERE farm_id=? ORDER BY created_at DESC LIMIT 50");
    $stmt->bind_param("i", $farm_id);
    $stmt->execute();
    $sensor_history = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM alerts WHERE farm_id=? ORDER BY created_at DESC");
    $stmt->bind_param("i", $farm_id);
    $stmt->execute();
    $alerts_result = $stmt->get_result();
} else {
    // ไม่มี farm_id แสดงรายชื่อฟาร์มทั้งหมด
    $farms_result = $conn->query("SELECT * FROM farms ORDER BY name ASC");
}

?>

<div class="container my-3 p-3 bg-white shadow rounded-3">

    <?php if ($farm_id): ?>
        <h3>ข้อมูลฟาร์ม: <?= htmlspecialchars($farm['name']) ?> (เจ้าของ: <?= htmlspecialchars($farm['username']) ?>)</h3>
        <p>ที่อยู่: <?= htmlspecialchars($farm['location']) ?></p>
        <p>ติดต่อ: <?= htmlspecialchars($farm['contact']) ?></p>

        <div class="row text-center mt-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                        🌡️ อุณหภูมิ <h2 id="temp"><?= $sensor_data['temperature'] ?? '--' ?></h2> °C
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        💧 ความชื้น <h2 id="humidity"><?= $sensor_data['humidity'] ?? '--' ?></h2> %
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark shadow">
                    <div class="card-body">
                        ☀️ แสง <h2 id="light"><?= $sensor_data['light'] ?? '--' ?></h2> Lux
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h4>💧 ปั๊มน้ำ</h4>
            <form method="POST" action="pump_control.php" class="d-inline-block">
                <input type="hidden" name="farm_id" value="<?= $farm_id ?>">
                <button class="btn <?= $pump_on ? 'btn-danger' : 'btn-success' ?>" name="action" value="<?= $pump_on ? 'off' : 'on' ?>">
                    <?= $pump_on ? 'ปิดปั๊ม' : 'เปิดปั๊ม' ?>
                </button>
            </form>
        </div>

        <div class="mt-5">
            <h4 class="text-center">📈 กราฟ Sensor ล่าสุด</h4>
            <canvas id="sensorChart" height="120"></canvas>
        </div>

        <h4 class="mt-5">ข้อมูล Sensor ย้อนหลัง</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ค่า</th>
                        <th>เวลา</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $sensor_history->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['value']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h4 class="mt-5">ประวัติการแจ้งเตือน</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ปัญหา</th>
                        <th>สถานะ</th>
                        <th>เวลา</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($alert = $alerts_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($alert['id']) ?></td>
                            <td><?= htmlspecialchars($alert['issue']) ?></td>
                            <td>
                                <?php if ($alert['resolved']): ?>
                                    <span class="badge bg-success">แก้ไขแล้ว</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">ยังไม่แก้ไข</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($alert['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
            </table>
        </div>

        <a href="admin_farm.php" class="btn btn-secondary mt-2">กลับ</a>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('sensorChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Temperature', 'Humidity', 'Light'],
                    datasets: [{
                        label: 'Sensor Value',
                        data: [
                            <?= $sensor_data['temperature'] ?? 0 ?>,
                            <?= $sensor_data['humidity'] ?? 0 ?>,
                            <?= $sensor_data['light'] ?? 0 ?>
                        ],
                        borderColor: ['red', 'blue', 'orange'],
                        backgroundColor: ['rgba(255,0,0,0.2)', 'rgba(0,0,255,0.2)', 'rgba(255,165,0,0.2)'],
                        fill: true
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>

    <?php else: ?>

        <h3>เลือกดูข้อมูลฟาร์ม</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อฟาร์ม</th>
                        <th>ที่อยู่</th>
                        <th>เจ้าของ</th>
                        <th>ดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($farm = $farms_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($farm['id']) ?></td>
                            <td><?= htmlspecialchars($farm['name']) ?></td>
                            <td><?= htmlspecialchars($farm['location']) ?></td>
                            <td><?= htmlspecialchars($farm['owner_id']) ?></td>
                            <td>
                                <a href="admin_farm.php?farm_id=<?= $farm['id'] ?>" class="btn btn-primary btn-sm">ดูข้อมูล</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>