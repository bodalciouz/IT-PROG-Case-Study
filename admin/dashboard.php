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

$result = $conn->query("SELECT COUNT(*) AS total FROM queue WHERE status IN ('pending', 'ongoing')");
$queue_today = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

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
                <p>Monitor clinic activity and manage core system records</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Total Patients</span>
                        <span class="value"><?= $total_patients; ?></span>
                        <span class="subtext">Registered patient accounts</span>
                    </div>
                    <div class="icon-box blue"></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Total Staff</span>
                        <span class="value"><?= $total_staff; ?></span>
                        <span class="subtext">Registered staff accounts</span>
                    </div>
                    <div class="icon-box green"></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Appointments Today</span>
                        <span class="value"><?= $appointments_today; ?></span>
                        <span class="subtext">Scheduled for today</span>
                    </div>
                    <div class="icon-box blue"></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Active Queue</span>
                        <span class="value"><?= $queue_today; ?></span>
                        <span class="subtext">Pending or ongoing queue</span>
                    </div>
                    <div class="icon-box green"></div>
                </div>
            </div>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

</body>
</html>