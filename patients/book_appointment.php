<?php
session_start();
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$patient_id = $_SESSION['user_id'];

// Fetch the first name of the user
$name_result = mysqli_query($conn, "SELECT first_name FROM users WHERE user_id = $patient_id");
$user = mysqli_fetch_assoc($name_result);

$message = "";

// Handle Appointment Booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = (int)$_POST['service_id'];
    $date       = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $time       = mysqli_real_escape_string($conn, $_POST['appointment_time']);

    // Step 1: Insert the appointment
    $stmt = mysqli_prepare($conn, "
        INSERT INTO appointments (patient_id, service_id, appointment_date, appointment_time, status)
        VALUES (?, ?, ?, ?, 'pending')
    ");
    mysqli_stmt_bind_param($stmt, "iiss", $patient_id, $service_id, $date, $time);

    if (mysqli_stmt_execute($stmt)) {
        $appointment_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Step 2: Count existing queue entries for that date to determine queue_number
        $count_result = mysqli_query($conn, "
            SELECT COUNT(*) AS count
            FROM queue q
            JOIN appointments a ON q.appointment_id = a.appointment_id
            WHERE a.appointment_date = '$date'
        ");
        $count_row    = mysqli_fetch_assoc($count_result);
        $queue_number = (int)$count_row['count'] + 1;

        // Step 3: Get estimated duration for the service
        $svc_result = mysqli_query($conn, "
            SELECT service_name, estimated_duration FROM services WHERE service_id = $service_id
        ");
        $svc_row      = mysqli_fetch_assoc($svc_result);
        $duration     = isset($svc_row['estimated_duration']) ? (int)$svc_row['estimated_duration'] : 30;
        $est_wait     = $queue_number * $duration;
        $service_name = $svc_row['service_name'] ?? 'your service';

        // Step 4: Insert into queue
        $q_stmt = mysqli_prepare($conn, "
            INSERT INTO queue (appointment_id, queue_number, status, estimated_wait, created_at)
            VALUES (?, ?, 'pending', ?, NOW())
        ");
        mysqli_stmt_bind_param($q_stmt, "iii", $appointment_id, $queue_number, $est_wait);

        if (mysqli_stmt_execute($q_stmt)) {
            mysqli_stmt_close($q_stmt);

            // Step 5: Insert booking submission notification
            $formatted_date = date('F j, Y', strtotime($date));
            $formatted_time = date('g:i A', strtotime($time));
            $notif_message  = "Your appointment request for {$service_name} on {$formatted_date} at {$formatted_time} has been submitted and is awaiting confirmation.";
            $notif_type     = "booking_submitted";

            $n_stmt = mysqli_prepare($conn, "
                INSERT INTO notifications (user_id, appointment_id, type, message, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            mysqli_stmt_bind_param($n_stmt, "iiss", $patient_id, $appointment_id, $notif_type, $notif_message);
            mysqli_stmt_execute($n_stmt);
            mysqli_stmt_close($n_stmt);

            $message = '<div class="alert success">Appointment booked successfully! You are #' . $queue_number . ' in the queue.</div>';
        } else {
            $message = '<div class="alert error">Appointment saved but queue entry failed: ' . htmlspecialchars(mysqli_stmt_error($q_stmt)) . '</div>';
            mysqli_stmt_close($q_stmt);
        }

    } else {
        $message = '<div class="alert error">Failed to book appointment: ' . htmlspecialchars(mysqli_stmt_error($stmt)) . '</div>';
        mysqli_stmt_close($stmt);
    }
}

// Fetch services
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
                    <input type="time" name="appointment_time" required>
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