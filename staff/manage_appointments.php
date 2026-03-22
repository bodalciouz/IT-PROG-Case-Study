<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

if(isset($_POST['update_status'])){
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();
}

$query = "
SELECT a.*, u.first_name, u.last_name, s.service_name
FROM appointments a
JOIN users u ON a.patient_id = u.user_id
JOIN services s ON a.service_id = s.service_id
ORDER BY appointment_date ASC
";

$result = $conn->query($query);
?>

<h2>Manage Appointments</h2>

<?php while($row = $result->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <p>
            <?= $row['first_name'] ?> <?= $row['last_name'] ?> |
            <?= $row['service_name'] ?> |
            <?= $row['appointment_date'] ?> |
            Status: <?= $row['status'] ?>
        </p>

        <form method="POST">
            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">

            <select name="status">
                <option>pending</option>
                <option>confirmed</option>
                <option>ongoing</option>
                <option>completed</option>
                <option>missed</option>
            </select>

            <button name="update_status">Update</button>
        </form>
    </div>
<?php endwhile; ?>