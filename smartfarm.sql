-- ปิดตรวจสอบ Foreign Key ชั่วคราว
SET FOREIGN_KEY_CHECKS=0;

-- ใช้ฐานข้อมูล smartfarm
CREATE DATABASE IF NOT EXISTS smartfarm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartfarm;

-- ตาราง users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตาราง farms
CREATE TABLE IF NOT EXISTS farms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    owner_id INT NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    contact VARCHAR(100) DEFAULT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    service_start DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ตาราง sensors
CREATE TABLE IF NOT EXISTS sensors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farm_id INT NOT NULL,
    type ENUM('temperature','humidity','light') NOT NULL,
    status ENUM('active','offline','error') DEFAULT 'active',
    last_value FLOAT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE CASCADE
);

-- ตาราง sensor_data
CREATE TABLE IF NOT EXISTS sensor_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id INT NOT NULL,
    value FLOAT NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sensor_id) REFERENCES sensors(id) ON DELETE CASCADE
);

-- ตาราง pumps
CREATE TABLE IF NOT EXISTS pumps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farm_id INT NOT NULL,
    status ENUM('on','off') DEFAULT 'off',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE CASCADE
);

-- ตาราง alerts
CREATE TABLE IF NOT EXISTS alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farm_id INT NOT NULL,
    sensor_name VARCHAR(100) NOT NULL,
    issue TEXT NOT NULL,
    resolved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE CASCADE
);

-- ตาราง chat_messages
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- เปิดตรวจสอบ Foreign Key กลับ
SET FOREIGN_KEY_CHECKS=1;

-- -------------------------
-- INSERT ตัวอย่าง USERS ก่อน
INSERT INTO users (id, username, password, role) VALUES
(1, 'admin', '$2y$10$k/fn6K4xvyQY5x3IqaVfNeGn/WYh2rtHeJGhYIlt31Zx7oL1o5C1S', 'admin'),
(2, 'customer1', '$2y$10$6t2AhE6Qj8d4NV4VfhnWcOimYh0wIUV5E68mZubDdR4OguZsyXQqO', 'customer');

-- INSERT ฟาร์มอ้างอิง USER
INSERT INTO farms (id, name, owner_id, location, contact, service_start, latitude, longitude) VALUES 
(1, 'ฟาร์มลูกค้า1', 2, 'เชียงใหม่', '0812345678', '2024-01-01', 18.7883, 98.9853);

-- เพิ่มข้อมูลผู้ใช้และฟาร์มสำหรับลูกค้าใหม่ (customer2)
INSERT INTO users (id, username, password, role) VALUES
(3, 'customer2', '$2y$10$6t2AhE6Qj8d4NV4VfhnWcOimYh0wIUV5E68mZubDdR4OguZsyXQqO', 'customer');

INSERT INTO farms (id, name, owner_id, location, latitude, longitude) VALUES
(2, 'ฟาร์มลูกค้า2', 3, 'กรุงเทพ', 13.7563, 100.5018);

-- INSERT sensor
INSERT INTO sensors (farm_id, type, status) VALUES 
(1, 'temperature', 'active'),
(1, 'humidity', 'active'),
(1, 'light', 'active'),
(2, 'temperature', 'active'),
(2, 'humidity', 'active'),
(2, 'light', 'active');

-- INSERT pump
INSERT INTO pumps (farm_id, status) VALUES 
(1, 'off'),
(2, 'off');

-- INSERT alert
INSERT INTO alerts (farm_id, sensor_name, issue, resolved) VALUES
(1, 'temperature', 'Sensor ไม่ส่งค่า', 0),
(1, 'humidity', 'Sensor offline', 0),
(2, 'temperature', 'Sensor ไม่ส่งค่า', 0);

-- INSERT chat
INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES
(2, 1, 'สวัสดี admin, sensor ไม่ทำงาน'),
(1, 2, 'รับทราบครับ เราจะตรวจสอบให้'),
(3, 1, 'สวัสดี admin, sensor ที่ฟาร์มไม่ทำงาน');