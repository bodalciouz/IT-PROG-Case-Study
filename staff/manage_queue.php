<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

// UPDATE QUEUE STATUS
if(isset($_POST['update_queue'])){
    $queue_id = $_POST['queue_id'];
    $status = $_POST['status'];

    // update queue table
    $stmt = $conn->prepare("UPDATE queue SET status=? WHERE queue_id=?");
    $stmt->bind_param("si", $status, $queue_id);
    $stmt->execute();

    // sync appointment status
    $conn->query("
        UPDATE appointments 
        SET status='$status'
        WHERE appointment_id = (
            SELECT appointment_id FROM queue WHERE queue_id = $queue_id
        )
    ");
}

// FETCH QUEUE
$query = "
SELECT q.*, u.first_name, u.last_name, s.estimated_duration
FROM queue q
JOIN appointments a ON q.appointment_id = a.appointment_id
JOIN users u ON a.patient_id = u.user_id
JOIN services s ON a.service_id = s.service_id
ORDER BY q.queue_number ASC
";

$result = $conn->query($query);

$current_position = 1;
?>

<h2>Queue Management</h2>

<?php while($row = $result->fetch_assoc()): ?>

<?php
$estimated_wait = ($row['queue_number'] - $current_position) * $row['estimated_duration'];
?>

<div style="border:1px solid #ccc; padding:10px; margin:10px;">
    <p>
        Queue #<?= $row['queue_number'] ?> |
        <?= $row['first_name'] ?> <?= $row['last_name'] ?> |
        Status: <?= $row['status'] ?> |
        Est Wait: <?= $estimated_wait ?> mins
    </p>

    <form method="POST">
        <input type="hidden" name="queue_id" value="<?= $row['queue_id'] ?>">

        <button name="status" value="ongoing">Start</button>
        <button name="status" value="completed">Complete</button>
        <button name="status" value="missed">Miss</button>

        <input type="hidden" name="update_queue" value="1">
    </form>
</div>

<?php endwhile; ?>