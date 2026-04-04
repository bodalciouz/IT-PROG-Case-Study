<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$stmt = $conn->prepare("
    SELECT 
        q.queue_id,
        q.queue_number,
        q.status,
        q.estimated_wait,
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        u.first_name,
        u.last_name,
        s.service_name
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    JOIN users u ON a.patient_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    WHERE a.appointment_date = ?
    ORDER BY q.queue_number ASC
");
$stmt->bind_param("s", $date);
$stmt->execute();
$queue = $stmt->get_result();

$active_result = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    WHERE a.appointment_date = ?
    AND q.status IN ('pending', 'ongoing')
");
$active_result->bind_param("s", $date);
$active_result->execute();
$active_count = $active_result->get_result()->fetch_assoc()['total'];

$completed_result = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    WHERE a.appointment_date = ?
    AND q.status = 'completed'
");
$completed_result->bind_param("s", $date);
$completed_result->execute();
$completed_count = $completed_result->get_result()->fetch_assoc()['total'];

$missed_result = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    WHERE a.appointment_date = ?
    AND q.status = 'missed'
");
$missed_result->bind_param("s", $date);
$missed_result->execute();
$missed_count = $missed_result->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Overview - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="dashboard-page admin-page">

<div class="app-container">
    <aside class="sidebar">
        <div class="logo">
            <h1>SmartClinic</h1>
            <span>Admin Panel</span>
        </div>

        <nav class="menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="manage_users.php" class="nav-item">Manage Users</a>
            <a href="manage_services.php" class="nav-item">Manage Services</a>
            <a href="manage_appointments.php" class="nav-item">Appointments</a>
            <a href="queue_overview.php" class="nav-item active">Queue Overview</a>
            <a href="reports.php" class="nav-item">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <h2>Queue Overview</h2>

        <div class="content-card">
            <h3>Select Date</h3>
            <form method="GET" class="crud-form">
                <div class="form-group">
                    <label for="date">Queue Date</label>
                    <input type="date" name="date" id="date" value="<?= htmlspecialchars($date) ?>">
                </div>
                <button type="submit" class="btn-primary">View Queue</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Active Queue</span>
                    <span class="value"><?= $active_count ?></span>
                    <span class="subtext">Pending or ongoing</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Completed</span>
                    <span class="value"><?= $completed_count ?></span>
                    <span class="subtext">Finished consultations</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Missed</span>
                    <span class="value"><?= $missed_count ?></span>
                    <span class="subtext">Patients not served</span>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h3>Daily Queue</h3>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Appointment Time</th>
                        <th>Status</th>
                        <th>Estimated Wait</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $queue->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['queue_number'] ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['service_name']) ?></td>
                            <td><?= date('g:i A', strtotime($row['appointment_time'])) ?></td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td><?= $row['estimated_wait'] ?> mins</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>