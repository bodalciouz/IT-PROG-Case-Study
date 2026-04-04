<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

if(isset($_POST['update_queue'])){
    $queue_id = $_POST['queue_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE queue SET status=? WHERE queue_id=?");
    $stmt->bind_param("si", $status, $queue_id);
    $stmt->execute();

    $stmt2 = $conn->prepare("
        UPDATE appointments 
        SET status=? 
        WHERE appointment_id = (
            SELECT appointment_id FROM queue WHERE queue_id=?
        )
    ");
    $stmt2->bind_param("si", $status, $queue_id);
    $stmt2->execute();
}

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

<body class="dashboard-page">

<div class="app-container">

<div class="sidebar">
    <div class="logo">
        <h1>SmartClinic</h1>
        <span>Staff Panel</span>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="nav-item">Dashboard</a>
        <a href="manage_schedule.php" class="nav-item">Manage Schedule</a>
        <a href="manage_appointments.php" class="nav-item">Appointments</a>
        <a href="manage_queue.php" class="nav-item active">Queue</a>
    </div>

    <div class="logout">
        <a href="../logout.php" class="nav-item">Logout</a>
    </div>
</div>

<div class="main-content">

<div class="top-bar">
    <h2>Queue Management</h2>
</div>

<div class="dashboard">

<div class="content-card">
    <h3 class="section-title">Queue List</h3>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Queue #</th>
                    <th>Patient</th>
                    <th>Status</th>
                    <th>Est Wait</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while($row = $result->fetch_assoc()): 
                $estimated_wait = ($row['queue_number'] - $current_position) * $row['estimated_duration'];
            ?>
                <tr>
                    <td><?= $row['queue_number'] ?></td>
                    <td><?= $row['first_name'] ?> <?= $row['last_name'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $estimated_wait ?> mins</td>

                    <td class="action-cell">
                        <form method="POST">
                            <input type="hidden" name="queue_id" value="<?= $row['queue_id'] ?>">

                            <button class="table-btn" name="status" value="ongoing">Start</button>
                            <button class="table-btn" name="status" value="completed">Complete</button>
                            <button class="table-btn danger-btn" name="status" value="missed">Miss</button>

                            <input type="hidden" name="update_queue" value="1">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>
</div>

</div>
</div>
</div>
</body>