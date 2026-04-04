<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to   = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

$total_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM appointments
    WHERE appointment_date BETWEEN ? AND ?
");
$total_stmt->bind_param("ss", $date_from, $date_to);
$total_stmt->execute();
$total_appointments = $total_stmt->get_result()->fetch_assoc()['total'];

$completed_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM appointments
    WHERE appointment_date BETWEEN ? AND ?
    AND status = 'completed'
");
$completed_stmt->bind_param("ss", $date_from, $date_to);
$completed_stmt->execute();
$completed = $completed_stmt->get_result()->fetch_assoc()['total'];

$missed_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM appointments
    WHERE appointment_date BETWEEN ? AND ?
    AND status = 'missed'
");
$missed_stmt->bind_param("ss", $date_from, $date_to);
$missed_stmt->execute();
$missed = $missed_stmt->get_result()->fetch_assoc()['total'];

$cancelled_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM appointments
    WHERE appointment_date BETWEEN ? AND ?
    AND status = 'cancelled'
");
$cancelled_stmt->bind_param("ss", $date_from, $date_to);
$cancelled_stmt->execute();
$cancelled = $cancelled_stmt->get_result()->fetch_assoc()['total'];

$pending_queue_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    WHERE a.appointment_date BETWEEN ? AND ?
    AND q.status IN ('pending', 'ongoing')
");
$pending_queue_stmt->bind_param("ss", $date_from, $date_to);
$pending_queue_stmt->execute();
$pending_queue = $pending_queue_stmt->get_result()->fetch_assoc()['total'];

$service_report_stmt = $conn->prepare("
    SELECT s.service_name, COUNT(*) AS total
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    WHERE a.appointment_date BETWEEN ? AND ?
    GROUP BY s.service_id, s.service_name
    ORDER BY total DESC
");
$service_report_stmt->bind_param("ss", $date_from, $date_to);
$service_report_stmt->execute();
$service_report = $service_report_stmt->get_result();

$status_report_stmt = $conn->prepare("
    SELECT status, COUNT(*) AS total
    FROM appointments
    WHERE appointment_date BETWEEN ? AND ?
    GROUP BY status
    ORDER BY total DESC
");
$status_report_stmt->bind_param("ss", $date_from, $date_to);
$status_report_stmt->execute();
$status_report = $status_report_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - SmartClinic</title>
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
            <a href="queue_overview.php" class="nav-item">Queue Overview</a>
            <a href="reports.php" class="nav-item active">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <h2>Operational Reports</h2>

        <div class="content-card">
            <h3>Filter Report Range</h3>
            <form method="GET" class="crud-form">
                <div class="form-group">
                    <label for="date_from">From</label>
                    <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($date_from) ?>" required>
                </div>

                <div class="form-group">
                    <label for="date_to">To</label>
                    <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($date_to) ?>" required>
                </div>

                <button type="submit" class="btn-primary">Generate</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Total Appointments</span>
                    <span class="value"><?= $total_appointments ?></span>
                    <span class="subtext">Within selected range</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Completed</span>
                    <span class="value"><?= $completed ?></span>
                    <span class="subtext">Finished consultations</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Missed</span>
                    <span class="value"><?= $missed ?></span>
                    <span class="subtext">Not served</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Cancelled</span>
                    <span class="value"><?= $cancelled ?></span>
                    <span class="subtext">Cancelled bookings</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <span class="label">Active Queue</span>
                    <span class="value"><?= $pending_queue ?></span>
                    <span class="subtext">Pending or ongoing</span>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h3>Appointments per Service</h3>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Total Appointments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $service_report->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['service_name']) ?></td>
                            <td><?= $row['total'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="content-card">
            <h3>Appointments by Status</h3>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $status_report->fetch_assoc()): ?>
                        <tr>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td><?= $row['total'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>