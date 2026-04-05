<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

// ACTION
if(isset($_POST['action'])){
    $id = $_POST['queue_id'];
    $status = $_POST['action'];

    $stmt = $conn->prepare("UPDATE queue SET status=? WHERE queue_id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

// QUERY
$query = "
SELECT q.*, u.first_name, u.last_name, s.service_name
FROM queue q
JOIN appointments a ON q.appointment_id = a.appointment_id
JOIN users u ON a.patient_id = u.user_id
JOIN services s ON a.service_id = s.service_id
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body class="dashboard-page">

<div class="app-container">

<?php include 'sidebar.php'; ?>

<div class="main-content">

<div class="top-bar"><h2>Queue</h2></div>

<table class="data-table">

<tr>
<th>#</th>
<th>Patient</th>
<th>Service</th>
<th>Status</th>
<th>Wait</th>
<th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['queue_number'] ?></td>
<td><?= $row['first_name']." ".$row['last_name'] ?></td>
<td><?= $row['service_name'] ?></td>
<td><?= $row['status'] ?></td>
<td><?= $row['estimated_wait'] ?> min</td>

<td>
<form method="POST">
<input type="hidden" name="queue_id" value="<?= $row['queue_id'] ?>">
<button name="action" value="ongoing">Start</button>
<button name="action" value="completed">Done</button>
<button name="action" value="missed">Miss</button>
</form>
</td>
</tr>
<?php endwhile; ?>

</table>

</div>
</div>
</div>
</body>
</html>