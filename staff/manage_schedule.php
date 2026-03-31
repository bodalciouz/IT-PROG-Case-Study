<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$error = "";

// ADD SCHEDULE
if(isset($_POST['add_schedule'])){
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_patients = $_POST['max_patients'];

    if(empty($day_of_week) || empty($start_time) || empty($end_time)){
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO schedules (user_id, day_of_week, start_time, end_time, max_patients) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssi", $user_id, $day_of_week, $start_time, $end_time, $max_patients);
        $stmt->execute();
    }
}

// FETCH SCHEDULES
$result = $conn->query("SELECT * FROM schedules WHERE user_id = $user_id ORDER BY day_of_week");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Schedule</title>
<link rel="stylesheet" href="../assets/css/styles.css">

</head>
<body>

<div class="container">
<div class="login-section">

<h2>Create Schedule</h2>

<?php if(!empty($error)): ?>
<p style="color:#ff6b6b; font-weight:600; margin-bottom:10px;">
    <?php echo $error; ?>
</p>
<?php endif; ?>

<form method="POST" class="login-form">

<div class="form-group">
<label>Day</label>
<select name="day_of_week" required>
    <option value="">Select Day</option>
    <option>Monday</option>
    <option>Tuesday</option>
    <option>Wednesday</option>
    <option>Thursday</option>
    <option>Friday</option>
    <option>Saturday</option>
    <option>Sunday</option>
</select>
</div>

<div class="form-group">
<label>Start Time</label>
<input type="time" name="start_time" required>
</div>

<div class="form-group">
<label>End Time</label>
<input type="time" name="end_time" required>
</div>

<div class="form-group">
<label>Maximum Patients</label>
<input type="number" name="max_patients" min="1" max="50" value="10" required>
</div>

<button type="submit" name="add_schedule" class="login-btn">Save Schedule</button>

</form>

<hr style="margin:20px 0;">

<h3>Your Schedules</h3>

<?php while($row = $result->fetch_assoc()): ?>

<div class="schedule-card">
    <strong><?php echo $row['day_of_week']; ?></strong><br>

    <?php
    echo date("g:i A", strtotime($row['start_time'])) . " - " .
         date("g:i A", strtotime($row['end_time']));
    ?>

    <br>
    Max Patients: <?php echo $row['max_patients']; ?>
</div>

<?php endwhile; ?>

<a href="dashboard.php" class="login-btn">Back to Dashboard</a>

</div>
</div>

</body>
</html>