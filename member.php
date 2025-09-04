<?php include("htmlhead.php"); ?>

<body class="bg-light">

    <div class="container my-3 p-3 bg-white shadow rounded-3">
        <h2 class="text-success">📋 แบบฟอร์มสมัครสมาชิก SmartFarm</h2>
        <p>กรอกข้อมูลฟาร์มของคุณเพื่อให้ Admin ติดต่อกลับ</p>

        <form id="memberForm">
            <div class="mb-3">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">ชื่อฟาร์ม</label>
                <input type="text" name="farm_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">ที่ตั้งฟาร์ม</label>
                <input type="text" name="location" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">รายละเอียดเพิ่มเติม (เช่น ต้องการติดตั้ง sensor ชนิดใด)</label>
                <textarea name="message" class="form-control" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">ส่งข้อมูล</button>
        </form>

        <div id="status" class="mt-3"></div>

        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-secondary">ย้อนกลับหน้าหลัก</a>
        </div>
    </div>

    <script>
        // URL ของ Google Apps Script ที่ deploy แล้ว
        // **สำคัญ: คุณต้องแก้ไข URL นี้ให้เป็น URL ของสคริปต์ของคุณเอง**
        const scriptURL = "https://script.google.com/macros/s/AKfycbyZiJV4hcvR7U57g_W6zoTDwKb1xaoQ2c7Ri7HEmo3QhziPvZvzHZ44VFHwj2Wwtos/exec";

        document.getElementById("memberForm").addEventListener("submit", e => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // แสดงกำลังส่ง
            document.getElementById("status").innerHTML =
                "<div class='alert alert-info'>⏳ กำลังส่งข้อมูล...</div>";

            fetch(scriptURL, {
                    method: "POST",
                    body: new URLSearchParams(data) // ใช้ URLSearchParams เพื่อให้สอดคล้องกับฟอร์ม HTML
                })
                .then(res => res.text())
                .then(msg => {
                    if (msg.includes("บันทึกเรียบร้อย")) {
                        document.getElementById("status").innerHTML =
                            "<div class='alert alert-success'>✅ ส่งข้อมูลเรียบร้อยแล้ว! ขอบคุณที่สมัครสมาชิก</div>";
                        e.target.reset(); // ล้างฟอร์มเมื่อส่งสำเร็จ
                    } else {
                        document.getElementById("status").innerHTML =
                            "<div class='alert alert-warning'>⚠️ เกิดข้อผิดพลาด: " + msg + "</div>";
                    }
                })
                .catch(err => {
                    document.getElementById("status").innerHTML =
                        "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการเชื่อมต่อ: " + err + "</div>";
                });
        });
    </script>

</body>

</html>