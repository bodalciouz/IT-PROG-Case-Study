<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

// UPDATE
if(isset($_POST['update_status'])){
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

// QUERY
$query = "
SELECT a.*, u.first_name, u.last_name, s.service_name
FROM appointments a
JOIN users u ON a.patient_id = u.user_id
JOIN services s ON a.service_id = s.service_id
ORDER BY a.appointment_date ASC
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

<div class="top-bar"><h2>Appointments</h2></div>

<div class="content-card">

<table class="data-table">
<tr>
<th>Patient</th>
<th>Service</th>
<th>Date</th>
<th>Status</th>
<th>Update</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['first_name']." ".$row['last_name'] ?></td>
<td><?= $row['service_name'] ?></td>
<td><?= $row['appointment_date'] ?></td>
<td><?= $row['status'] ?></td>
<td>
<form method="POST">
<input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
<select name="status">
<option>pending</option>
<option>confirmed</option>
<option>completed</option>
<option>missed</option>
</select>
<button name="update_status">Update</button>
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