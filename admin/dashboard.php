<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 3");
$total_patients = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 2");
$total_staff = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()");
$appointments_today = $result->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    WHERE a.appointment_date = CURDATE()
    AND q.status IN ('pending', 'ongoing')
");
$queue_today = $result->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM appointments
    WHERE appointment_date = CURDATE()
    AND status = 'missed'
");
$missed_today = $result->fetch_assoc()['total'];

$recent_appointments = $conn->query("
    SELECT 
        a.appointment_id,
        u.first_name,
        u.last_name,
        s.service_name,
        a.appointment_time,
        a.status
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    WHERE a.appointment_date = CURDATE()
    ORDER BY a.appointment_time ASC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SmartClinic</title>
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
            <a href="dashboard.php" class="nav-item active">Dashboard</a>
            <a href="manage_users.php" class="nav-item">Manage Users</a>
            <a href="manage_services.php" class="nav-item">Manage Services</a>
            <a href="manage_schedules.php" class="nav-item">Manage Schedules</a>
            <a href="manage_appointments.php" class="nav-item">Appointments</a>
            <a href="queue_overview.php" class="nav-item">Queue Overview</a>
            <a href="reports.php" class="nav-item">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h2>Welcome, Admin</h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Admin Dashboard</h3>
                <p>Monitor clinic operations and system records</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Total Patients</span>
                        <span class="value"><?= $total_patients ?></span>
                        <span class="subtext">Registered patient accounts</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Total Staff</span>
                        <span class="value"><?= $total_staff ?></span>
                        <span class="subtext">Registered staff accounts</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Appointments Today</span>
                        <span class="value"><?= $appointments_today ?></span>
                        <span class="subtext">Scheduled for today</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Active Queue</span>
                        <span class="value"><?= $queue_today ?></span>
                        <span class="subtext">Pending or ongoing</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Missed Today</span>
                        <span class="value"><?= $missed_today ?></span>
                        <span class="subtext">Unserved appointments</span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h3>Today’s Upcoming Appointments</h3>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent_appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['appointment_id'] ?></td>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['service_name']) ?></td>
                                <td><?= date('g:i A', strtotime($row['appointment_time'])) ?></td>
                                <td><?= ucfirst($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>
</div>

</body>
</html>