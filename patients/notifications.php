<?php
session_start();
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$patient_id = $_SESSION['user_id'];

// Fetch user first name
$name_result = mysqli_query($conn, "SELECT first_name FROM users WHERE user_id = $patient_id");
$user = mysqli_fetch_assoc($name_result);

// Fetch all notifications for this patient, newest first
$query = "
    SELECT 
        n.notification_id,
        n.type,
        n.message,
        n.created_at,
        n.appointment_id,
        a.appointment_date,
        a.appointment_time,
        s.service_name
    FROM notifications n
    LEFT JOIN appointments a ON n.appointment_id = a.appointment_id
    LEFT JOIN services s ON a.service_id = s.service_id
    WHERE n.user_id = $patient_id
    ORDER BY n.created_at DESC
";

$result = mysqli_query($conn, $query);
$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - SmartClinic</title>
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
            <a href="book_appointment.php" class="nav-item">Book Appointment</a>
            <a href="my_appointments.php" class="nav-item">My Appointments</a>
            <a href="queue_status.php" class="nav-item">Queue Status</a>
            <a href="profile.php" class="nav-item">My Profile</a>
            <a href="notifications.php" class="nav-item active">Notifications</a>
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
                <h3>Notifications</h3>
                <p>Updates on your appointments and queue activity</p>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="no-data">
                    <p>🔔 You have no notifications yet.</p>
                </div>
            <?php else: ?>
                <div class="notif-list">
                <?php foreach ($notifications as $n):

                    // Icon and badge per type
                    $icon  = '🔔';
                    $badge = '<span class="notif-badge badge-gray">' . htmlspecialchars($n['type']) . '</span>';

                    switch ($n['type']) {
                        case 'booking_submitted':
                            $icon  = '📋';
                            $badge = '<span class="notif-badge badge-blue">Submitted</span>';
                            break;
                        case 'booking_confirmed':
                        case 'appointment_confirmed':
                            $icon  = '✅';
                            $badge = '<span class="notif-badge badge-green">Confirmed</span>';
                            break;
                        case 'appointment_rejected':
                            $icon  = '❌';
                            $badge = '<span class="notif-badge badge-red">Rejected</span>';
                            break;
                        case 'appointment_cancelled':
                            $icon  = '🚫';
                            $badge = '<span class="notif-badge badge-gray">Cancelled</span>';
                            break;
                        case 'appointment_completed':
                            $icon  = '🎉';
                            $badge = '<span class="notif-badge badge-purple">Completed</span>';
                            break;
                        case 'appointment_ongoing':
                            $icon  = '⏳';
                            $badge = '<span class="notif-badge badge-cyan">Ongoing</span>';
                            break;
                        case 'appointment_missed':
                            $icon  = '⚠️';
                            $badge = '<span class="notif-badge badge-yellow">Missed</span>';
                            break;
                    }

                    $type_class = 'type-' . preg_replace('/[^a-z0-9_]/', '_', strtolower($n['type']));
                ?>
                    <div class="notif-card <?= $type_class ?>">
                        <div class="notif-icon"><?= $icon ?></div>
                        <div class="notif-body">
                            <p class="notif-message"><?= htmlspecialchars($n['message']) ?></p>
                            <div class="notif-meta">
                                <?= $badge ?>
                                <?php if (!empty($n['service_name'])): ?>
                                    <span>🏥 <?= htmlspecialchars($n['service_name']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($n['appointment_date'])): ?>
                                    <span>📅 <?= date('M j, Y', strtotime($n['appointment_date'])) ?>
                                    <?php if (!empty($n['appointment_time'])): ?>
                                        at <?= date('g:i A', strtotime($n['appointment_time'])) ?>
                                    <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <span>🕐 <?= date('M j, Y g:i A', strtotime($n['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>
</body>
</html>