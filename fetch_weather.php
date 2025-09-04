<?php
header('Content-Type: application/json');

$apiKey = "2f559b4a950f879abf00cb081b8784ab"; // ใส่ API KEY ของคุณ
$city = "Bangkok,TH";

$url = "https://api.openweathermap.org/data/2.5/forecast?q=$city&units=metric&appid=$apiKey";

// เรียก API โดยใช้ file_get_contents
$response = @file_get_contents($url);

if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(["error" => "ไม่สามารถดึงข้อมูลจาก API ได้"]);
    exit;
}

// ส่งข้อมูล JSON กลับให้ JavaScript
echo $response;
