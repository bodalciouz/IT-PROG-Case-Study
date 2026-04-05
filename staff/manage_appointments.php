<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

$staff_id = $_SESSION['user_id'];
$selected_date = $_GET['filter_date'] ?? date('Y-m-d');
$selected_day = date('l', strtotime($selected_date));
$message = "";

/* Update appointment status */
if (isset($_POST['update_status'])) {
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    $allowed_statuses = ['pending', 'confirmed', 'ongoing', 'completed', 'missed', 'cancelled'];

    if ($appointment_id > 0 && in_array($status, $allowed_statuses, true)) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $status, $appointment_id);

        if ($stmt->execute()) {
            $qstmt = $conn->prepare("UPDATE queue SET status = ? WHERE appointment_id = ?");
            $qstmt->bind_param("si", $status, $appointment_id);
            $qstmt->execute();
            $qstmt->close();

            $message = "Appointment status updated successfully.";
        } else {
            $message = "Failed to update appointment status.";
        }

        $stmt->close();
    }
}

/* get the logged-on staff's schedule for the selected day */
$schedules = [];
$schedule_stmt = $conn->prepare("
    SELECT schedule_id, day_of_week, start_time, end_time, max_patients
    FROM schedules
    WHERE user_id = ? AND day_of_week = ?
    ORDER BY start_time ASC
");
$schedule_stmt->bind_param("is", $staff_id, $selected_day);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();

while ($row = $schedule_result->fetch_assoc()) {
    $row['displayed_count'] = 0;
    $schedules[] = $row;
}
$schedule_stmt->close();

/* get appointments for the selected date only */
$appointments = [];
if (!empty($schedules)) {
    $appt_stmt = $conn->prepare("
        SELECT 
            a.appointment_id,
            a.patient_id,
            a.service_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.created_at,
            u.first_name,
            u.last_name,
            s.service_name
        FROM appointments a
        JOIN users u ON a.patient_id = u.user_id
        JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_date = ?
        ORDER BY a.appointment_time ASC, a.created_at ASC, a.appointment_id ASC
    ");
    $appt_stmt->bind_param("s", $selected_date);
    $appt_stmt->execute();
    $appt_result = $appt_stmt->get_result();

    while ($row = $appt_result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $appt_stmt->close();
}

/*
    filters:
    1. Only selected date
    2. Only appointments within this staff's schedule time range
    3. Display only up to max_patients per schedule
*/
$display_rows = [];
$assigned_appointments = [];

foreach ($appointments as $appointment) {
    $appt_time = $appointment['appointment_time'];

    foreach ($schedules as $index => $schedule) {
        $within_schedule = ($appt_time >= $schedule['start_time'] && $appt_time < $schedule['end_time']);
        $has_capacity = ($schedules[$index]['displayed_count'] < (int)$schedule['max_patients']);
        $not_already_assigned = !isset($assigned_appointments[$appointment['appointment_id']]);

        if ($within_schedule && $has_capacity && $not_already_assigned) {
            $appointment['schedule_label'] =
                $schedule['day_of_week'] . ' ' .
                date('g:i A', strtotime($schedule['start_time'])) . ' - ' .
                date('g:i A', strtotime($schedule['end_time']));

            $display_rows[] = $appointment;
            $assigned_appointments[$appointment['appointment_id']] = true;
            $schedules[$index]['displayed_count']++;
            break;
        }
    }
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
<body class="dashboard-page">

<div class="app-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <h2>Manage Appointments</h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Appointments</h3>
                <p>View and update only the appointments that fall within your schedule.</p>
            </div>

            <div class="content-card">
                <form method="GET" class="crud-form" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="filter_date">Filter by Date</label>
                        <input
                            type="date"
                            name="filter_date"
                            id="filter_date"
                            value="<?= htmlspecialchars($selected_date) ?>"
                            required
                        >
                    </div>
                    <button type="submit" class="update-btn">Apply Filter</button>
                </form>

                <?php if (!empty($message)): ?>
                    <p class="success-message"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

                <?php if (empty($schedules)): ?>
                    <div class="no-data">
                        <p>No schedule found for <?= htmlspecialchars($selected_day) ?>.</p>
                    </div>
                <?php else: ?>

                    <div class="content-card" style="margin-bottom: 20px;">
                        <h3>Your Schedules for <?= htmlspecialchars($selected_day) ?></h3>
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Max Patients</th>
                                    <th>Displayed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($schedule['day_of_week']) ?></td>
                                        <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                                        <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                                        <td><?= (int)$schedule['max_patients'] ?></td>
                                        <td><?= (int)$schedule['displayed_count'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($display_rows)): ?>
                        <div class="table-wrapper">
                            <table class="styled-table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Schedule Window</th>
                                        <th>Status</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($display_rows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></td>
                                            <td><?= htmlspecialchars($row['service_name']) ?></td>
                                            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                            <td><?= date('g:i A', strtotime($row['appointment_time'])) ?></td>
                                            <td><?= htmlspecialchars($row['schedule_label']) ?></td>
                                            <td><?= htmlspecialchars($row['status']) ?></td>
                                            <td>
                                                <form method="POST" class="update-form">
                                                    <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                                    <select name="status" required>
                                                        <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>pending</option>
                                                        <option value="confirmed" <?= $row['status'] === 'confirmed' ? 'selected' : '' ?>>confirmed</option>
                                                        <option value="ongoing" <?= $row['status'] === 'ongoing' ? 'selected' : '' ?>>ongoing</option>
                                                        <option value="completed" <?= $row['status'] === 'completed' ? 'selected' : '' ?>>completed</option>
                                                        <option value="missed" <?= $row['status'] === 'missed' ? 'selected' : '' ?>>missed</option>
                                                        <option value="cancelled" <?= $row['status'] === 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="update-btn">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <p>No appointments found within your schedule for this date.</p>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>
</div>

</body>
</html>
