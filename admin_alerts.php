<?php
session_start();
include("db.php");
include("header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$alerts_result = $conn->query("SELECT a.*, f.name as farm_name FROM alerts a JOIN farms f ON a.farm_id = f.id ORDER BY a.created_at DESC");
?>

<div class="container my-3 p-3 bg-white shadow rounded-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>การแจ้งเตือนทั้งหมด</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ฟาร์ม</th>
                    <th>Sensor</th>
                    <th>ปัญหา</th>
                    <th>สถานะ</th>
                    <th>เวลา</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($alert = $alerts_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($alert['id']) ?></td>
                        <td><?= htmlspecialchars($alert['farm_name']) ?></td>
                        <td><?= htmlspecialchars($alert['sensor_name']) ?></td>
                        <td><?= htmlspecialchars($alert['issue']) ?></td>
                        <td>
                            <?php if ($alert['resolved']): ?>
                                <span class="badge bg-success">แก้ไขแล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-danger">ยังไม่แก้ไข</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($alert['created_at']) ?></td>
                        <td>
                            <form method="POST" action="resolve_alert.php" style="display:inline;">
                                <input type="hidden" name="alert_id" value="<?= $alert['id'] ?>">
                                <input type="hidden" name="resolved_status" value="<?= $alert['resolved'] ? 0 : 1 ?>">
                                <button type="submit" class="btn btn-sm <?= $alert['resolved'] ? 'btn-warning' : 'btn-success' ?>">
                                    <?= $alert['resolved'] ? "ยกเลิก" : "แก้ไขแล้ว" ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>