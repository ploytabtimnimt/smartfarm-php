<?php
// openweather.php
// ใส่ API Key ของคุณที่นี่
define('OWM_API_KEY', 'c5d819a05868bcce061209cb96cd237f');

// ตำแหน่งเริ่มต้น (Bangkok) — แก้ไขได้
define('DEFAULT_LAT', 13.7563);
define('DEFAULT_LON', 100.5018);

// อายุแคช (วินาที) ลดโหลด API (ที่นี่ตั้ง 60 วินาที)
define('CACHE_TTL', 60);
