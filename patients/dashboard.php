<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT first_name, last_name FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$appointments_today_query = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE patient_id=$user_id AND appointment_date=CURDATE()");
$appointments_today = $appointments_today_query->fetch_assoc()['total'];

$pending_queue_query = $conn->query("SELECT COUNT(*) AS total FROM queue q JOIN appointments a ON q.appointment_id=a.appointment_id WHERE a.patient_id=$user_id AND q.status='pending'");
$pending_queue = $pending_queue_query->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard - SmartClinic</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<div class="app-container">

    <aside class="sidebar">
        <div class="logo">
            <h1>SmartClinic</h1>
            <span>Patient Portal</span>
        </div>

        <nav class="menu">
            <a href="dashboard.php" class="nav-item active">Dashboard</a>
            <a href="book_appointment.php" class="nav-item">Book Appointment</a>
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
            <h2>Welcome, <?= htmlspecialchars($user['first_name']); ?></h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Patient Dashboard</h3>
                <p>Manage your appointments and monitor your queue status easily</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Appointments Today</span>
                        <span class="value"><?= $appointments_today ?></span>
                        <span class="subtext">Scheduled for today</span>
                    </div>
                    <div class="icon-box blue"></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Pending Queue</span>
                        <span class="value"><?= $pending_queue ?></span>
                        <span class="subtext">Waiting for consultation</span>
                    </div>
                    <div class="icon-box green"></div>
                </div>
            </div>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

</body>
</html>