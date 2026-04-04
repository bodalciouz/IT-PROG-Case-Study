<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$message = "";

/* FETCH STAFF USERS ONLY */
$staff_result = $conn->query("
    SELECT user_id, first_name, last_name
    FROM users
    WHERE role_id = 2
    ORDER BY first_name, last_name
");

/* ADD SCHEDULE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $user_id = (int) $_POST['user_id'];
    $day_of_week = trim($_POST['day_of_week']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_patients = (int) $_POST['max_patients'];

    if ($start_time >= $end_time) {
        $message = "<p style='color:red;'>Start time must be earlier than end time.</p>";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO schedules (user_id, day_of_week, start_time, end_time, max_patients)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssi", $user_id, $day_of_week, $start_time, $end_time, $max_patients);

        if ($stmt->execute()) {
            header("Location: manage_schedules.php");
            exit();
        } else {
            $message = "<p style='color:red;'>Failed to add schedule.</p>";
        }
        $stmt->close();
    }
}

/* UPDATE SCHEDULE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    $schedule_id = (int) $_POST['schedule_id'];
    $user_id = (int) $_POST['user_id'];
    $day_of_week = trim($_POST['day_of_week']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_patients = (int) $_POST['max_patients'];

    if ($start_time >= $end_time) {
        $message = "<p style='color:red;'>Start time must be earlier than end time.</p>";
    } else {
        $stmt = $conn->prepare("
            UPDATE schedules
            SET user_id = ?, day_of_week = ?, start_time = ?, end_time = ?, max_patients = ?
            WHERE schedule_id = ?
        ");
        $stmt->bind_param("isssii", $user_id, $day_of_week, $start_time, $end_time, $max_patients, $schedule_id);

        if ($stmt->execute()) {
            header("Location: manage_schedules.php");
            exit();
        } else {
            $message = "<p style='color:red;'>Failed to update schedule.</p>";
        }
        $stmt->close();
    }
}

/* DELETE SCHEDULE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $schedule_id = (int) $_POST['schedule_id'];

    $stmt = $conn->prepare("DELETE FROM schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $schedule_id);

    if ($stmt->execute()) {
        header("Location: manage_schedules.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Failed to delete schedule.</p>";
    }
    $stmt->close();
}

/* FETCH ALL SCHEDULES */
$schedules = $conn->query("
    SELECT 
        s.schedule_id,
        s.user_id,
        s.day_of_week,
        s.start_time,
        s.end_time,
        s.max_patients,
        u.first_name,
        u.last_name
    FROM schedules s
    JOIN users u ON s.user_id = u.user_id
    ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
             s.start_time ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules - SmartClinic</title>
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
            <a href="manage_schedules.php" class="nav-item active">Manage Schedules</a>
            <a href="manage_appointments.php" class="nav-item">Appointments</a>
            <a href="queue_overview.php" class="nav-item">Queue Overview</a>
            <a href="reports.php" class="nav-item">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <h2>Manage Staff Schedules</h2>

        <div class="content-card">
            <h3>Add Schedule</h3>
            <?= $message ?>

            <form method="POST" class="crud-form">
                <div class="form-group">
                    <label for="user_id">Staff Member</label>
                    <select name="user_id" id="user_id" required>
                        <option value="">Select staff</option>
                        <?php
                        $staff_result->data_seek(0);
                        while ($staff = $staff_result->fetch_assoc()):
                        ?>
                            <option value="<?= $staff['user_id'] ?>">
                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="day_of_week">Day of Week</label>
                    <select name="day_of_week" id="day_of_week" required>
                        <option value="">Select day</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" required>
                </div>

                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" required>
                </div>

                <div class="form-group">
                    <label for="max_patients">Max Patients</label>
                    <input type="number" name="max_patients" id="max_patients" min="1" required>
                </div>

                <button type="submit" name="add_schedule" class="btn-primary">Add Schedule</button>
            </form>
        </div>

        <div class="content-card">
            <h3>All Staff Schedules</h3>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Staff</th>
                        <th>Day</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Max Patients</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $schedules->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <?= $row['schedule_id'] ?>
                                    <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
                                </td>

                                <td>
                                    <select name="user_id" required>
                                        <?php
                                        $staff_result->data_seek(0);
                                        while ($staff = $staff_result->fetch_assoc()):
                                        ?>
                                            <option value="<?= $staff['user_id'] ?>" <?= $row['user_id'] == $staff['user_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>

                                <td>
                                    <select name="day_of_week" required>
                                        <?php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        foreach ($days as $day):
                                        ?>
                                            <option value="<?= $day ?>" <?= $row['day_of_week'] === $day ? 'selected' : '' ?>>
                                                <?= $day ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td>
                                    <input type="time" name="start_time" value="<?= $row['start_time'] ?>" required>
                                </td>

                                <td>
                                    <input type="time" name="end_time" value="<?= $row['end_time'] ?>" required>
                                </td>

                                <td>
                                    <input type="number" name="max_patients" value="<?= $row['max_patients'] ?>" min="1" required>
                                </td>

                                <td>
                                    <button type="submit" name="update_schedule" class="btn-primary">Update</button>
                                </td>

                                <td>
                                    <button type="submit" name="delete_schedule" class="btn-danger" onclick="return confirm('Delete this schedule?');">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>