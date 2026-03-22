<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

if(isset($_POST['add_schedule'])){
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO schedules (user_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $day_of_week, $start_time, $end_time);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM schedules WHERE user_id = $user_id");
?>

<h2>Manage Schedule</h2>

<form method="POST">
    Day: <input type="text" name="day_of_week" required>
    Start: <input type="time" name="start_time" required>
    End: <input type="time" name="end_time" required>
    <button name="add_schedule">Add</button>
</form>

<h3>Your Schedules</h3>

<?php while($row = $result->fetch_assoc()): ?>
    <p><?= $row['day_of_week'] ?> | <?= $row['start_time'] ?> - <?= $row['end_time'] ?></p>
<?php endwhile; ?>