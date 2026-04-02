<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

// Set the timezone to Philippine Standard Time (Asia/Manila)
date_default_timezone_set('Asia/Manila');

$user_id = $_SESSION['user_id'];

// Fetch upcoming appointments (excluding cancelled ones)
$appointments_query = $conn->query("SELECT appointment_id, appointment_date, appointment_time, service_id, status FROM appointments WHERE patient_id=$user_id AND appointment_date > CURDATE() AND status != 'cancelled' ORDER BY appointment_date LIMIT 3");
$upcoming_appointments = [];
while ($row = mysqli_fetch_assoc($appointments_query)) {
    $service_query = "SELECT service_name FROM services WHERE service_id = " . $row['service_id'];
    $service_result = mysqli_query($conn, $service_query);
    $service = mysqli_fetch_assoc($service_result);
    $row['service_name'] = $service['service_name'];
    $upcoming_appointments[] = $row;
}

// Get current date and time for display
$current_date = date("l, F j, Y");
$current_time = date("h:i A");
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
            <h2>Welcome to Your Dashboard</h2>
        </header>

        <section class="patient-dashboard">
            <div class="dashboard-header">
                <h3>Healthcare Dashboard</h3>
                <p>Monitor your appointments, upcoming consultations, and take control of your healthcare.</p>
            </div>

            <!-- Date and Time Section -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="label">Today's Date</span>
                        <span class="value"><?= $current_date ?></span>
                    </div>
                    <div class="stat-info">
                        <span class="label">Current Time</span>
                        <span class="value"><?= $current_time ?></span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments Section -->
            <div class="appointment-card">
                <h4>Upcoming Appointments</h4>
                <ul class="appointment-list">
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <li>
                            <div class="details">
                                <?= htmlspecialchars($appointment['service_name']) ?> - <?= date('M j, Y', strtotime($appointment['appointment_date'])) ?> at <?= date('g:i A', strtotime($appointment['appointment_time'])) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

</body>
</html>