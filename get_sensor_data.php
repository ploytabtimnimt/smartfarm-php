<?php
include("db.php");

$farm_id = intval($_GET['farm_id'] ?? 0);
if (!$farm_id) {
    echo json_encode([]);
    exit;
}

// เตรียม array สำหรับกราฟ
$labels = [];
$temp = [];
$humidity = [];
$light = [];

// ดึงข้อมูล sensor ของ farm
$sensors = $conn->query("SELECT id, type FROM sensors WHERE farm_id = $farm_id");

while ($sensor = $sensors->fetch_assoc()) {
    $sensor_id = $sensor['id'];
    $type = $sensor['type'];

    // ดึง 10 ค่า ล่าสุด
    $res = $conn->query("SELECT value, recorded_at FROM sensor_data 
                         WHERE sensor_id=$sensor_id ORDER BY recorded_at DESC LIMIT 10");
    $values = [];
    $times = [];
    while ($row = $res->fetch_assoc()) {
        $values[] = floatval($row['value']);
        $times[] = $row['recorded_at'];
    }
    $values = array_reverse($values);
    $times = array_reverse($times);

    if ($type === 'temperature') $temp = $values;
    if ($type === 'humidity') $humidity = $values;
    if ($type === 'light') $light = $values;
    $labels = $times;
}

echo json_encode([
    'labels' => $labels,
    'temperature' => $temp,
    'humidity' => $humidity,
    'light' => $light
]);
