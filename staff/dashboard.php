<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

// Queries
$result = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()");
$appointments_today = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM queue WHERE status='pending'");
$pending_queue = $result->fetch_assoc()['total'];
?>

<body class="dashboard-page">

<div class="app-container">

    <!-- SIDEBAR -->
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

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOP BAR -->
        <div class="top-bar">
            <h2>Staff Dashboard</h2>
        </div>

        <!-- CONTENT -->
        <div class="dashboard">

            <div class="dashboard-header">
                <h3>Welcome, <?= $_SESSION['first_name'] ?></h3>
                <p>Overview of today’s clinic activity</p>
            </div>

            <!-- STATS -->
            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-info">
                        <div class="label">Appointments Today</div>
                        <div class="value"><?= $appointments_today ?></div>
                        <div class="subtext">Scheduled patients</div>
                    </div>
                    <div class="icon-box green">A</div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <div class="label">Pending Queue</div>
                        <div class="value"><?= $pending_queue ?></div>
                        <div class="subtext">Waiting patients</div>
                    </div>
                    <div class="icon-box blue">Q</div>
                </div>

            </div>

            <!-- QUICK ACTIONS -->
            <div class="content-card">
                <h3 class="section-title">Quick Actions</h3>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="manage_schedule.php">
                        <button class="action-btn">Manage Schedule</button>
                    </a>

                    <a href="manage_appointments.php">
                        <button class="action-btn">Manage Appointments</button>
                    </a>

                    <a href="manage_queue.php">
                        <button class="action-btn">Manage Queue</button>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

</body>