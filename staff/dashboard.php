<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$result = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()");
$appointments_today = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM queue WHERE status='pending'");
$pending_queue = $result->fetch_assoc()['total'];
?>

<h2>Staff Dashboard</h2>

<p>Appointments Today: <?= $appointments_today ?></p>
<p>Pending Queue: <?= $pending_queue ?></p>

<a href="manage_schedule.php">Manage Schedule</a><br>
<a href="manage_appointments.php">Manage Appointments</a><br>
<a href="manage_queue.php">Manage Queue</a><br>
<a href="../logout.php">Logout</a>