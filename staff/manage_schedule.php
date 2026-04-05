<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// INSERT SCHEDULE
if(isset($_POST['add_schedule'])){
    $day   = $_POST['day_of_week'];
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];

    if($start >= $end){
        $error = "Invalid time range";
    } else {
        $stmt = $conn->prepare("INSERT INTO schedules(user_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $day, $start, $end);
        if($stmt->execute()){
            $success = "Schedule added successfully!";
        } else {
            $error = "Failed to add schedule: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM schedules WHERE user_id=$user_id ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="dashboard-page">

<div class="app-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <h2>Manage Schedule</h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Your Schedule</h3>
                <p>Add or view your working hours.</p>
            </div>

            <?php if($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>

            <div class="content-card">
                <h4>Add New Schedule</h4>
                <form method="POST" class="crud-form">

                    <div class="form-group-profile">
                        <label>Select Day</label>
                        <select name="day_of_week" required>
                            <option value="" disabled selected>-- Select Day --</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                            <option>Saturday</option>
                            <option>Sunday</option>
                        </select>
                    </div>

                    <div class="form-group-profile">
                        <label>Start Time</label>
                        <input type="time" name="start_time" required>
                    </div>

                    <div class="form-group-profile">
                        <label>End Time</label>
                        <input type="time" name="end_time" required>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <button type="submit" name="add_schedule" class="update-btn">Add Schedule</button>
                    </div>

                </form>
            </div>

            <div class="content-card">
                <h4>Existing Schedule</h4>
                <?php if($result->num_rows > 0): ?>
                <div class="table-wrapper">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                                <td><?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="no-data"><p>No schedule added yet.</p></div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

    <footer class="footer">
        SmartClinic © 2026. All Rights Reserved
    </footer>

</body>
</html>