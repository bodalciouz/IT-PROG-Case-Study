<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$firstName = $_SESSION['first_name'] ?? 'Staff';

$q1 = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()");
$appointments_today = $q1->fetch_assoc()['total'];

$q2 = $conn->query("SELECT COUNT(*) AS total FROM queue WHERE status='pending'");
$pending_queue = $q2->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body class="dashboard-page">

<div class="app-container">

<div class="sidebar">
    <div class="logo">
        <h1>SmartClinic</h1>
        <span>Staff Panel</span>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="nav-item active">Dashboard</a>
        <a href="manage_schedule.php" class="nav-item">Manage Schedule</a>
        <a href="manage_appointments.php" class="nav-item">Appointments</a>
        <a href="manage_queue.php" class="nav-item">Queue</a>
    </div>

    <div class="logout">
        <a href="../logout.php" class="nav-item">Logout</a>
    </div>
</div>

<div class="main-content">

<div class="top-bar">
    <h2>Staff Dashboard</h2>
</div>

<div class="dashboard">

<div class="dashboard-header">
    <h3>Welcome, <?= htmlspecialchars($firstName) ?></h3>
</div>

<div class="stats-grid">

<div class="stat-card">
    <div class="stat-info">
        <div class="label">Appointments Today</div>
        <div class="value"><?= $appointments_today ?></div>
    </div>
</div>

<div class="stat-card">
    <div class="stat-info">
        <div class="label">Pending Queue</div>
        <div class="value"><?= $pending_queue ?></div>
    </div>
</div>

</div>

</div>
</div>
</div>
</body>
</html>