<?php
session_start();
require_once "db.php"; // เชื่อมฐานข้อมูล

$user_role = $_SESSION['role'] ?? null;
$user_id   = $_SESSION['user_id'] ?? null;
$username  = $_SESSION['username'] ?? null;
?>

<?php include("htmlhead.php"); ?>

<body class="bg-light">

    <div class="container my-3 p-3 bg-white shadow rounded-3">

        <!-- Header -->
        <header class="mb-4">
            <h1 class="text-center text-success">🌱 Smart Farm 🌱</h1>
        </header>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success rounded mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">SmartFarm</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : '' ?>" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="services.php">บริการ</a></li>
                        <li class="nav-item"><a class="nav-link" href="use.php">วิธีการใช้งาน</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">ติดต่อเรา</a></li>

                        <?php if ($user_id): ?>
                            <li class="nav-item"><a class="nav-link" href="data.php">Data</a></li>
                            <li class="nav-item"><a class="nav-link" href="alerts.php?farm_id=<?= urlencode($user_id) ?>">Alerts</a></li>
                            <?php if ($user_role === 'admin'): ?>
                                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>

                    <!-- User Menu -->
                    <?php if ($user_id): ?>
                        <span class="navbar-text me-2 text-light">สวัสดี, <?= htmlspecialchars($username) ?></span>
                        <a href="logout.php" class="btn btn-light text-success">Logout</a>
                    <?php else: ?>
                        <button class="btn btn-light text-success" data-bs-toggle="modal" data-bs-target="#authModal">
                            เข้าสู่ระบบ / สมัครสมาชิก
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- About Us -->
        <div class="row">
            <div class="col-md-6">
                <img src="images/Smartfarming.jpg" class="img-fluid rounded shadow" alt="Smart Farming">
            </div>
            <div class="col-md-6">
                <h3>เกี่ยวกับเรา</h3>
                <p>
                    นวัตกรรมเซนเซอร์เกษตรแม่นยำ เป็นการพัฒนาปรับปรุงค่าการวัดของเซนเซอร์ในระบบเกษตรอัจฉริยะ
                    ด้วยการนำระบบการวัดการตรวจสอบหรือการทวนสอบมาปรับปรุงแก้ค่าการวัดของเซนเซอร์
                    ให้มีความแม่นยำสอดคล้องกับข้อกำหนดของผู้ผลิต
                </p>
                <p>
                    วิธีการตรวจสอบหรือทวนสอบเซนเซอร์ โดยใช้วิธีวัดเปรียบเทียบค่ามาตรฐานอ้างอิงกับเซนเซอร์จริง
                    ผลที่ได้เรียกว่า "ค่าความผิดพลาด" (Error Value) และนำไป "แก้ค่า" (Correct Value)
                    อาจอยู่ในรูปสมการเชิงเส้น (Linear) หรือพหุนาม (Polynomial)
                    เพื่อนำไปชดเชยค่าในโปรแกรมควบคุมระบบเกษตรอัจฉริยะ
                </p>
                <p>ข้อมูลทั้งหมดอ้างอิงจากเซ็นเซอร์จริงและสภาพอากาศ OpenWeatherMap</p>
            </div>
        </div>

        <!-- Dashboard -->
        <div id="dashboard" class="row text-center mt-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                        🌡️ อุณหภูมิ <h2 id="temp">--</h2>°C
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        💧 ความชื้น <h2 id="humidity">--</h2>%
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark shadow">
                    <div class="card-body">
                        ☀️ แสง <h2 id="light">--</h2> Lux
                    </div>
                </div>
            </div>
        </div>

        <!-- Weather Chart -->
        <div class="mt-5">
            <h4 class="text-center">📈 กราฟรายชั่วโมง (OpenWeatherMap)</h4>
            <canvas id="weatherChart" height="120"></canvas>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-4 py-3 border-top">
            <p class="mb-0">&copy; 2025 SmartFarm Co., Ltd.</p>
        </footer>
    </div>

    <!-- Auth Modal -->
    <div class="modal fade" id="authModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เข้าสู่ระบบ / สมัครสมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="authTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login">เข้าสู่ระบบ</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#register">สมัครสมาชิก</button></li>
                    </ul>
                    <div class="tab-content mt-3">
                        <!-- Login -->
                        <div class="tab-pane fade show active" id="login">
                            <form method="POST" action="login.php">
                                <input type="text" class="form-control mb-2" name="username" placeholder="ชื่อผู้ใช้ หรืออีเมล" required>
                                <input type="password" class="form-control mb-2" name="password" placeholder="รหัสผ่าน" required>
                                <button type="submit" class="btn btn-success w-100">เข้าสู่ระบบ</button>
                            </form>
                        </div>
                        <!-- Register -->
                        <div class="tab-pane fade" id="register">
                            <div class="text-center">
                                <p>หากคุณสนใจสมัครสมาชิก โปรดกรอกแบบฟอร์มเพื่อให้ทีมงานติดต่อกลับ</p>
                                <a href="member.php" class="btn btn-primary w-100">กรอกแบบฟอร์มเพื่อสมัครสมาชิก</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById("weatherChart").getContext("2d");
        const weatherChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: [],
                datasets: [{
                        label: "Temp (°C)",
                        borderColor: "red",
                        data: [],
                        fill: false
                    },
                    {
                        label: "Humidity (%)",
                        borderColor: "blue",
                        data: [],
                        fill: false
                    },
                    {
                        label: "Light (approx)",
                        borderColor: "orange",
                        data: [],
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "เวลา"
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: "ค่า"
                        }
                    }
                }
            }
        });

        async function fetchHourlyForecast() {
            try {
                const res = await fetch("fetch_weather.php");
                const data = await res.json();
                if (!data.list) return;

                weatherChart.data.labels = [];
                weatherChart.data.datasets.forEach(ds => ds.data = []);

                data.list.slice(0, 8).forEach(item => {
                    const timeLabel = new Date(item.dt * 1000).getHours() + ":00";
                    const temp = item.main.temp;
                    const humidity = item.main.humidity;
                    const light = 100 - item.clouds.all;

                    weatherChart.data.labels.push(timeLabel);
                    weatherChart.data.datasets[0].data.push(temp);
                    weatherChart.data.datasets[1].data.push(humidity);
                    weatherChart.data.datasets[2].data.push(light);
                });

                document.getElementById("temp").innerText = data.list[0].main.temp.toFixed(1);
                document.getElementById("humidity").innerText = data.list[0].main.humidity;
                document.getElementById("light").innerText = 100 - data.list[0].clouds.all;

                weatherChart.update();
            } catch (err) {
                console.error("Fetch error:", err);
            }
        }

        fetchHourlyForecast();
        setInterval(fetchHourlyForecast, 60000);
    </script>

</body>

</html>