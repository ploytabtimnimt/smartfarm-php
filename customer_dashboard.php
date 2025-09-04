<?php
session_start();
include("db.php");
include("header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// ดึงฟาร์มของลูกค้า
$stmt = $conn->prepare("SELECT * FROM farms WHERE owner_id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$farm_result = $stmt->get_result();
$farm = $farm_result->fetch_assoc();
$farm_id = $farm['id'] ?? 0;

// ดึงค่าล่าสุดของ sensor แต่ละประเภท
$stmt = $conn->prepare("
    SELECT t1.value, t2.type
    FROM sensor_data t1
    JOIN sensors t2 ON t1.sensor_id = t2.id
    WHERE t2.farm_id = ?
    AND t1.recorded_at = (SELECT MAX(recorded_at) FROM sensor_data WHERE sensor_id = t1.sensor_id)
");
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$sensor_data_result = $stmt->get_result();

$sensor_data = [];
while ($row = $sensor_data_result->fetch_assoc()) {
    $sensor_data[$row['type']] = $row['value'];
}

// ดึง pump status ล่าสุดจากตาราง 'pumps'
$stmt = $conn->prepare("SELECT status FROM pumps WHERE farm_id=? ORDER BY updated_at DESC LIMIT 1");
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$pump_status = $stmt->get_result()->fetch_assoc();
$pump_on = ($pump_status['status'] ?? 'off') === 'on';

?>

<div class="container my-3 p-3 bg-white shadow rounded-3">

    <h3>Dashboard : <?= htmlspecialchars($farm['name'] ?? "ยังไม่มีชื่อ") ?></h3>

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
        <form method="POST" action="pump_control.php">
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

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('sensorChart').getContext('2d');
    const sensorChart = new Chart(ctx, {
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
</body>

</html>