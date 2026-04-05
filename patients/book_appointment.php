<?php
session_start();
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$patient_id = $_SESSION['user_id'];

$name_result = mysqli_query($conn, "SELECT first_name FROM users WHERE user_id = $patient_id");
$user = mysqli_fetch_assoc($name_result);

$message = "";

$clinic_start = '08:00';
$clinic_end   = '17:00';
$clinic_step  = 30;
$allowed_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $service_id = (int)$_POST['service_id'];
    $date       = $_POST['appointment_date'];
    $time       = $_POST['appointment_time'];

    $errors = [];

    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        $errors[] = "Invalid date format.";
    }

    $time_obj = DateTime::createFromFormat('H:i', $time);
    if (!$time_obj || $time_obj->format('H:i') !== $time) {
        $errors[] = "Invalid time format.";
    }

    $day_of_week = date('l', strtotime($date));
    if (!in_array($day_of_week, $allowed_days)) {
        $errors[] = "Appointments allowed only Monday to Friday.";
    }

    $time_ts  = strtotime($time);
    if ($time_ts < strtotime($clinic_start) || $time_ts > strtotime($clinic_end)) {
        $errors[] = "Time must be within clinic hours ($clinic_start - $clinic_end).";
    }

    if ((int)date('i', $time_ts) % $clinic_step !== 0) {
        $errors[] = "Time must follow {$clinic_step}-minute intervals.";
    }

    $svc = mysqli_query($conn, "SELECT * FROM services WHERE service_id = $service_id");
    if (mysqli_num_rows($svc) === 0) {
        $errors[] = "Invalid service.";
    } else {
        $svc_row = mysqli_fetch_assoc($svc);
        $service_name = $svc_row['service_name'];
        $duration = $svc_row['estimated_duration'];
    }

    $dup = mysqli_query($conn, "
        SELECT * FROM appointments 
        WHERE patient_id = $patient_id
        AND appointment_date = '$date'
        AND appointment_time = '$time'
    ");
    if (mysqli_num_rows($dup) > 0) {
        $errors[] = "You already booked this time.";
    }

    if (empty($errors)) {

        $schedule_query = mysqli_query($conn, "
            SELECT * FROM schedules
            WHERE day_of_week = '$day_of_week'
            AND start_time <= '$time'
            AND end_time >= '$time'
        ");

        if (mysqli_num_rows($schedule_query) === 0) {
            $errors[] = "No staff available at selected time.";
        } else {

            $schedule_row = mysqli_fetch_assoc($schedule_query);
            $max_patients = $schedule_row['max_patients'];

            $count_query = mysqli_query($conn, "
                SELECT COUNT(*) as total
                FROM appointments
                WHERE appointment_date = '$date'
                AND appointment_time = '$time'
                AND status != 'cancelled'
            ");

            $count_row = mysqli_fetch_assoc($count_query);

            if ($count_row['total'] >= $max_patients) {
                $errors[] = "This time slot is already full.";
            }
        }
    }

    if (empty($errors)) {

        $stmt = mysqli_prepare($conn, "
            INSERT INTO appointments (patient_id, service_id, appointment_date, appointment_time, status)
            VALUES (?, ?, ?, ?, 'pending')
        ");
        mysqli_stmt_bind_param($stmt, "iiss", $patient_id, $service_id, $date, $time);

        if (mysqli_stmt_execute($stmt)) {

            $appointment_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Insert queue
            $q_stmt = mysqli_prepare($conn, "
                INSERT INTO queue (appointment_id, queue_number, status, estimated_wait, created_at)
                VALUES (?, 0, 'pending', 0, NOW())
            ");
            mysqli_stmt_bind_param($q_stmt, "i", $appointment_id);
            mysqli_stmt_execute($q_stmt);
            mysqli_stmt_close($q_stmt);

            $queues = mysqli_query($conn, "
                SELECT q.queue_id, a.service_id
                FROM queue q
                JOIN appointments a ON q.appointment_id = a.appointment_id
                WHERE a.appointment_date = '$date'
                ORDER BY a.appointment_time ASC, a.appointment_id ASC
            ");

            $pos = 1;
            while ($row = mysqli_fetch_assoc($queues)) {

                $qid = $row['queue_id'];
                $sid = $row['service_id'];

                $dur_res = mysqli_query($conn, "
                    SELECT estimated_duration FROM services WHERE service_id = $sid
                ");
                $dur_row = mysqli_fetch_assoc($dur_res);
                $dur = $dur_row['estimated_duration'] ?? 30;

                $wait = ($pos - 1) * $dur;

                mysqli_query($conn, "
                    UPDATE queue 
                    SET queue_number = $pos, estimated_wait = $wait
                    WHERE queue_id = $qid
                ");

                $pos++;
            }

            $formatted_date = date('F j, Y', strtotime($date));
            $formatted_time = date('g:i A', strtotime($time));

            $msg = "Your appointment for {$service_name} on {$formatted_date} at {$formatted_time} has been submitted.";

            $notif = mysqli_prepare($conn, "
                INSERT INTO notifications (user_id, appointment_id, type, message)
                VALUES (?, ?, 'appointment', ?)
            ");
            mysqli_stmt_bind_param($notif, "iis", $patient_id, $appointment_id, $msg);
            mysqli_stmt_execute($notif);
            mysqli_stmt_close($notif);

            $message = "<div class='alert success'>Appointment booked successfully!</div>";

        } else {
            $message = "<div class='alert error'>Booking failed.</div>";
        }

    } else {
        $message = "<div class='alert error'>" . implode("<br>", $errors) . "</div>";
    }
}

$services_result = mysqli_query($conn, "SELECT * FROM services ORDER BY service_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="dashboard-page">
<div class="app-container">

    <aside class="sidebar">
        <div class="logo">
            <h1>SmartClinic</h1>
            <span>Patient Portal</span>
        </div>
        <nav class="menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="book_appointment.php" class="nav-item active">Book Appointment</a>
            <a href="my_appointments.php" class="nav-item">My Appointments</a>
            <a href="queue_status.php" class="nav-item">Queue Status</a>
            <a href="profile.php" class="nav-item">My Profile</a>
            <a href="notifications.php" class="nav-item">Notifications</a>
        </nav>
        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($user['first_name'] ?? '') ?></h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Book an Appointment</h3>
                <p>Fill in the details below to schedule your visit</p>
            </div>

            <?= $message ?>

            <form method="POST" class="crud-form">
                <div class="form-group-profile">
                    <label>Select Service</label>
                    <select name="service_id" required>
                        <option value="" disabled selected>-- Choose a Service --</option>
                        <?php while ($row = mysqli_fetch_assoc($services_result)): ?>
                            <option value="<?= $row['service_id'] ?>">
                                <?= htmlspecialchars($row['service_name']) ?> (<?= $row['estimated_duration'] ?> mins)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group-profile">
                    <label>Preferred Date</label>
                    <input type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group-profile">
                    <label>Preferred Time</label>
                    <input type="time" name="appointment_time"
                           min="<?= $clinic_start ?>"
                           max="<?= $clinic_end ?>"
                           step="<?= $clinic_step * 60 ?>"
                           required>
                </div>

                <div style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" class="update-btn">Confirm Booking</button>
                    <a href="dashboard.php" class="delete-btn" style="display:inline-block; text-align:center; line-height:normal; padding:10px 20px;">Cancel</a>
                </div>
            </form>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>
</div>
</body>
</html> 