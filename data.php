<?php
session_start();
include("db.php");
include("header.php"); // รวมไฟล์ header.php ที่มี Navbar ที่ถูกต้อง

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$username = $_SESSION['username'] ?? '';

// ดึงข้อมูลฟาร์มตามบทบาทของผู้ใช้
$farms_result = null;
if ($user_role === 'admin') {
    $stmt = $conn->prepare("SELECT f.*, u.username FROM farms f JOIN users u ON f.owner_id = u.id ORDER BY f.id ASC");
} else {
    $stmt = $conn->prepare("SELECT * FROM farms WHERE owner_id=? ORDER BY id ASC");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container my-3 p-3 bg-white shadow rounded-3">
    <h3>ข้อมูลฟาร์ม</h3>

    <div class="mt-4">
        <?php
        if ($result->num_rows > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>ชื่อฟาร์ม</th><th>ที่อยู่</th><th>Contact</th><th>เริ่มใช้</th>";
            if ($user_role === 'admin') echo "<th>Owner</th>";
            echo "<th>Actions</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                echo "<td>" . htmlspecialchars($row['service_start']) . "</td>";
                if ($user_role === 'admin') echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td><a href='alerts.php?farm_id=" . htmlspecialchars($row['id']) . "' class='btn btn-sm btn-warning'>แจ้งเตือน</a></td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>ไม่พบข้อมูลฟาร์ม</p>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>