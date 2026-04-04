<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$message = "";

/* UPDATE APPOINTMENT STATUS */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appointment'])) {
    $appointment_id = (int) $_POST['appointment_id'];
    $status = $_POST['status'];

    $allowed_statuses = ['pending', 'confirmed', 'ongoing', 'completed', 'missed', 'cancelled'];

    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $status, $appointment_id);

        if ($stmt->execute()) {
            if (in_array($status, ['pending', 'ongoing', 'completed', 'missed', 'cancelled'])) {
                $qstmt = $conn->prepare("UPDATE queue SET status = ? WHERE appointment_id = ?");
                $qstmt->bind_param("si", $status, $appointment_id);
                $qstmt->execute();
                $qstmt->close();
            }

            $message = "<p style='color:green;'>Appointment updated successfully.</p>";
        } else {
            $message = "<p style='color:red;'>Failed to update appointment.</p>";
        }
        $stmt->close();
    }
}

$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

$sql = "
    SELECT 
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        u.first_name,
        u.last_name,
        s.service_name,
        q.queue_number,
        q.estimated_wait,
        q.status AS queue_status
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    LEFT JOIN queue q ON a.appointment_id = q.appointment_id
";

if (!empty($filter_date)) {
    $sql .= " WHERE a.appointment_date = ?";
    $sql .= " ORDER BY a.appointment_time ASC, a.appointment_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter_date);
    $stmt->execute();
    $appointments = $stmt->get_result();
} else {
    $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time ASC, a.appointment_id ASC";
    $appointments = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - SmartClinic</title>
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
            <a href="manage_schedules.php" class="nav-item">Manage Schedules</a>
            <a href="manage_appointments.php" class="nav-item active">Appointments</a>
            <a href="queue_overview.php" class="nav-item">Queue Overview</a>
            <a href="reports.php" class="nav-item">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <h2>Manage Appointments</h2>

        <div class="content-card">
            <h3>Filter by Date</h3>
            <form method="GET" class="crud-form">
                <div class="form-group">
                    <label for="filter_date">Appointment Date</label>
                    <input type="date" name="filter_date" id="filter_date" value="<?= htmlspecialchars($filter_date) ?>">
                </div>
                <button type="submit" class="btn-primary">Apply Filter</button>
            </form>
            <?= $message ?>
        </div>

        <div class="content-card">
            <h3>Appointments List</h3>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Queue #</th>
                        <th>Queue Status</th>
                        <th>Est. Wait</th>
                        <th>Appointment Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['appointment_id'] ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['service_name']) ?></td>
                            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                            <td><?= date('g:i A', strtotime($row['appointment_time'])) ?></td>
                            <td><?= $row['queue_number'] ? $row['queue_number'] : '-' ?></td>
                            <td><?= $row['queue_status'] ? ucfirst($row['queue_status']) : '-' ?></td>
                            <td><?= $row['estimated_wait'] !== null ? $row['estimated_wait'] . ' mins' : '-' ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                    <select name="status" required>
                                        <?php
                                        $statuses = ['pending', 'confirmed', 'ongoing', 'completed', 'missed', 'cancelled'];
                                        foreach ($statuses as $status):
                                        ?>
                                            <option value="<?= $status ?>" <?= $row['status'] === $status ? 'selected' : '' ?>>
                                                <?= ucfirst($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                            </td>
                            <td>
                                    <button type="submit" name="update_appointment" class="btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>