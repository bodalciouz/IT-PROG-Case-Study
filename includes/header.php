<?php
require_once __DIR__ . '/authorization.php';
requireLogin(); // ensure user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SmartClinic</title>
<link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<header>
    <div class="header-container">
        <div class="header-logo">
            <img src="/assets/images/login_logo.svg" alt="SmartClinic Logo" width="100">
            <span class="brand-name">SmartClinic</span>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="/dashboard.php">Dashboard</a></li>

                <?php if($_SESSION['role_id'] == 3): // Patient ?>
                    <li><a href="/patient/appointments.php">My Appointments</a></li>
                    <li><a href="/patient/queue.php">Queue Status</a></li>

                <?php elseif($_SESSION['role_id'] == 2): // Staff ?>
                    <li><a href="/staff/appointments.php">Appointments</a></li>
                    <li><a href="/staff/queue.php">Queue Monitoring</a></li>
                    <li><a href="/staff/reports.php">Reports</a></li>

                <?php elseif($_SESSION['role_id'] == 1): // Admin ?>
                    <li><a href="/admin/users.php">Users</a></li>
                    <li><a href="/admin/appointments.php">Appointments</a></li>
                    <li><a href="/admin/reports.php">Reports</a></li>
                    <li><a href="/admin/settings.php">Settings</a></li>
                <?php endif; ?>

                <li><a href="/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>
<main>