<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$totals = [
    'appointments_today' => 0,
    'completed_today' => 0,
    'missed_today' => 0,
    'pending_queue' => 0,
    'total_patients' => 0,
];

$queries = [
    'appointments_today' => "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()",
    'completed_today' => "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE() AND status = 'completed'",
    'missed_today' => "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE() AND status = 'missed'",
    'pending_queue' => "SELECT COUNT(*) AS total FROM queue WHERE status IN ('pending','ongoing')",
    'total_patients' => "SELECT COUNT(*) AS total FROM users WHERE role_id = 3"
];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    if ($result) {
        $totals[$key] = (int)$result->fetch_assoc()['total'];
    }
}

$service_stats = $conn->query("SELECT s.service_name, COUNT(a.appointment_id) AS total_appointments
FROM services s
LEFT JOIN appointments a ON s.service_id = a.service_id
GROUP BY s.service_id, s.service_name
ORDER BY total_appointments DESC, s.service_name ASC");

$status_stats = $conn->query("SELECT status, COUNT(*) AS total
FROM appointments
GROUP BY status
ORDER BY total DESC, status ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="logo"><h1>SmartClinic</h1><span>Admin Panel</span></div>
        <nav class="menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="manage_users.php" class="nav-item">Manage Users</a>
            <a href="manage_services.php" class="nav-item">Manage Services</a>
            <a href="reports.php" class="nav-item active">Reports</a>
        </nav>
        <div class="logout"><a href="../logout.php" class="nav-item">Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="top-bar"><h2>Reports</h2></header>
        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Clinic Operations Report</h3>
                <p>View daily statistics and appointment trends from the current system data.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card"><div class="stat-info"><span class="label">Appointments Today</span><span class="value"><?= $totals['appointments_today'] ?></span><span class="subtext">Booked for today</span></div><div class="icon-box blue">📅</div></div>
                <div class="stat-card"><div class="stat-info"><span class="label">Completed Today</span><span class="value"><?= $totals['completed_today'] ?></span><span class="subtext">Finished consultations</span></div><div class="icon-box green">✅</div></div>
                <div class="stat-card"><div class="stat-info"><span class="label">Missed Today</span><span class="value"><?= $totals['missed_today'] ?></span><span class="subtext">No-show appointments</span></div><div class="icon-box blue">⚠️</div></div>
                <div class="stat-card"><div class="stat-info"><span class="label">Active Queue</span><span class="value"><?= $totals['pending_queue'] ?></span><span class="subtext">Pending or ongoing</span></div><div class="icon-box green">⏳</div></div>
            </div>

            <div class="report-grid">
                <div class="content-card">
                    <h3 class="section-title">Appointments per Service</h3>
                    <div class="table-wrapper">
                        <table class="data-table simple-table">
                            <thead><tr><th>Service</th><th>Total Appointments</th></tr></thead>
                            <tbody>
                            <?php while ($row = $service_stats->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                                    <td><?= (int)$row['total_appointments'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="section-title">Appointment Status Summary</h3>
                    <div class="table-wrapper">
                        <table class="data-table simple-table">
                            <thead><tr><th>Status</th><th>Total</th></tr></thead>
                            <tbody>
                            <?php while ($row = $status_stats->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                                    <td><?= (int)$row['total'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <footer class="footer">SmartClinic © 2026. All Rights Reserved</footer>
    </main>
</div>
</body>
</html>
