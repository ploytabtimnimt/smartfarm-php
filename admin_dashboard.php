<?php
session_start();
include("db.php");
include("header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$selected_farm_id = $_GET['farm_id'] ?? null;

// ดึงรายการฟาร์มทั้งหมดเพื่อใช้สร้างหมุดบนแผนที่
$farms_result = $conn->query("SELECT * FROM farms ORDER BY name ASC");
$farms_for_map = $farms_result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container my-3 p-3 bg-white shadow rounded-3">

    <?php if ($selected_farm_id): ?>
        <?php
        $stmt = $conn->prepare("SELECT f.*, u.username FROM farms f JOIN users u ON f.owner_id=u.id WHERE f.id=? LIMIT 1");
        $stmt->bind_param("i", $selected_farm_id);
        $stmt->execute();
        $farm = $stmt->get_result()->fetch_assoc();

        if ($farm) {
            $stmt = $conn->prepare("
                SELECT t1.value, t2.type
                FROM sensor_data t1
                JOIN sensors t2 ON t1.sensor_id = t2.id
                WHERE t2.farm_id = ?
                AND t1.recorded_at = (SELECT MAX(recorded_at) FROM sensor_data WHERE sensor_id = t1.sensor_id)
            ");
            $stmt->bind_param("i", $selected_farm_id);
            $stmt->execute();
            $sensor_data_result = $stmt->get_result();
            $sensor_data = [];
            while ($row = $sensor_data_result->fetch_assoc()) {
                $sensor_data[$row['type']] = $row['value'];
            }

            $stmt = $conn->prepare("SELECT status FROM pumps WHERE farm_id=? ORDER BY updated_at DESC LIMIT 1");
            $stmt->bind_param("i", $selected_farm_id);
            $stmt->execute();
            $pump_status = $stmt->get_result()->fetch_assoc();
            $pump_on = ($pump_status['status'] ?? 'off') === 'on';
        ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Dashboard ฟาร์ม: <?= htmlspecialchars($farm['name']) ?></h3>
                <a href="admin_dashboard.php" class="btn btn-secondary">กลับไปดูแผนที่</a>
            </div>

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
                    <input type="hidden" name="farm_id" value="<?= $selected_farm_id ?>">
                    <button class="btn <?= $pump_on ? 'btn-danger' : 'btn-success' ?>" name="action" value="<?= $pump_on ? 'off' : 'on' ?>">
                        <?= $pump_on ? 'ปิดปั๊ม' : 'เปิดปั๊ม' ?>
                    </button>
                </form>
            </div>

            <div class="mt-5">
                <h4 class="text-center">📈 กราฟ Sensor ล่าสุด</h4>
                <canvas id="sensorChart" height="120"></canvas>
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
        <?php } else { ?>
            <div class="alert alert-danger">ไม่พบข้อมูลฟาร์มที่เลือก</div>
            <a href="admin_dashboard.php" class="btn btn-secondary mt-3">กลับไปเลือกฟาร์ม</a>
        <?php } ?>

    <?php else: ?>
        <h3>Admin Dashboard - ดูตำแหน่งฟาร์มลูกค้า</h3>
        <p>คลิกที่หมุดบนแผนที่เพื่อดูข้อมูล Dashboard ของฟาร์ม</p>
        <div class="mt-4">
            <div id="map" style="height:400px;" class="rounded shadow"></div>
        </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    <?php if (!$selected_farm_id): ?>
        const map = L.map('map').setView([13.736717, 100.523186], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const farms = <?= json_encode($farms_for_map) ?>;

        farms.forEach(farm => {
            if (farm.latitude && farm.longitude) {
                L.marker([farm.latitude, farm.longitude]).addTo(map)
                    .bindPopup(`<b>${farm.name}</b><br><a href="admin_dashboard.php?farm_id=${farm.id}">ดู Dashboard</a>`);
            }
        });

    <?php endif; ?>
</script>
</body>

</html>