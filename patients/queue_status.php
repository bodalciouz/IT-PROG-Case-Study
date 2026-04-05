<?php
session_start();
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$patient_id = $_SESSION['user_id'];

// Fetch user first name
$name_result = mysqli_query($conn, "SELECT first_name FROM users WHERE user_id = $patient_id");
$user = mysqli_fetch_assoc($name_result);

// Fetch this patient's queue entries joined with appointments and services
$query = "
    SELECT 
        q.queue_id,
        q.status AS queue_status,
        q.estimated_wait,
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status AS appointment_status,
        s.service_name
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    JOIN services s ON a.service_id = s.service_id
    WHERE a.patient_id = $patient_id
    ORDER BY a.appointment_date ASC, a.appointment_time ASC, a.appointment_id ASC
";

$result = mysqli_query($conn, $query); 
$queues = [];
while ($row = mysqli_fetch_assoc($result)) {
    $queues[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Status - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="dashboard-page">

<div class="app-container">

    <aside class="sidebar">
        <div class="logo">
            <h1>SmartClinic</h1>
            <span>Patient Portal</span>
        </div>
        <nav class="menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="book_appointment.php" class="nav-item">Book Appointment</a>
            <a href="my_appointments.php" class="nav-item">My Appointments</a>
            <a href="queue_status.php" class="nav-item active">Queue Status</a>
            <a href="profile.php" class="nav-item">My Profile</a>
            <a href="notifications.php" class="nav-item">Notifications</a>
        </nav>
        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($user['first_name'] ?? '') ?></h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>My Queue Status</h3>
                <p>Live status of your position in the queue</p>
            </div>

            <?php if (empty($queues)): ?>
                <div class="no-data">
                    <p>You have no active queue entries. <a href="book_appointment.php">Book an appointment</a> to join the queue.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive content-card">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Service</th>
                                <th>Queue #</th>
                                <th>Est. Wait</th>
                                <th>Appointment Status</th>
                                <th>Queue Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $current_date = '';
                            $queue_pos = 0;
                            foreach ($queues as $q):
                                // Reset queue number for new date
                                if ($current_date !== $q['appointment_date']) {
                                    $current_date = $q['appointment_date'];
                                    $queue_pos = 1;
                                } else {
                                    $queue_pos++;
                                }

                                $appt_badge = match($q['appointment_status']) {
                                    'pending'   => 'bg-warning',
                                    'confirmed' => 'bg-primary',
                                    'ongoing'   => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                    'missed'    => 'bg-danger',
                                    default     => 'bg-secondary'
                                };
                                $queue_badge = match($q['queue_status']) {
                                    'pending'   => 'bg-warning',
                                    'ongoing'   => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                    'missed'    => 'bg-danger',
                                    default     => 'bg-secondary'
                                };
                            ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($q['appointment_date'])) ?></td>
                                    <td><?= date('g:i A', strtotime($q['appointment_time'])) ?></td>
                                    <td><?= htmlspecialchars($q['service_name']) ?></td>
                                    <td><strong>#<?= $queue_pos ?></strong></td>
                                    <td><?= $q['estimated_wait'] ?> mins</td>
                                    <td>
                                        <span class="badge <?= $appt_badge ?>">
                                            <?= ucfirst($q['appointment_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $queue_badge ?>">
                                            <?= ucfirst($q['queue_status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Auto refresh every 30 seconds -->
                <p class="text-muted" style="font-size:13px; margin-top:10px;">
                    ⟳ Page auto-refreshes every 30 seconds. 
                    <a href="queue_status.php">Refresh now</a>
                </p>
            <?php endif; ?>

        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

<script>
    setTimeout(function() { location.reload(); }, 30000);
</script>

</body>
</html>