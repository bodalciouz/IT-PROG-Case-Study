<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$error = "";

// INSERT
if(isset($_POST['add_schedule'])){
    $day = $_POST['day_of_week'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $max = $_POST['max_patients'];

    if($start >= $end){
        $error = "Invalid time range";
    } else {
        $stmt = $conn->prepare("
        INSERT INTO schedules(user_id,day_of_week,start_time,end_time,max_patients)
        VALUES(?,?,?,?,?)");
        $stmt->bind_param("isssi",$user_id,$day,$start,$end,$max);
        $stmt->execute();
    }
}

$result = $conn->query("SELECT * FROM schedules WHERE user_id=$user_id");
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

<h2>Schedule</h2>

<form method="POST">
<select name="day_of_week">
<option>Monday</option>
<option>Tuesday</option>
</select>

<input type="time" name="start_time">
<input type="time" name="end_time">
<input type="number" name="max_patients">

<button name="add_schedule">Add</button>
</form>

<table class="data-table">

<tr>
<th>Day</th>
<th>Time</th>
<th>Max</th>
</tr>

<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['day_of_week'] ?></td>
<td><?= $row['start_time']."-".$row['end_time'] ?></td>
<td><?= $row['max_patients'] ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>
</div>
</div>
</body>
</html>