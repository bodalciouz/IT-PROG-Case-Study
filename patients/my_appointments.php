<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

// Fetch user information
$query = "SELECT first_name, last_name FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Fetch appointments for today (excluding cancelled)
$appointments_today_query = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE patient_id=$user_id AND appointment_date=CURDATE() AND status != 'cancelled'");
$appointments_today = $appointments_today_query->fetch_assoc()['total'];

// Fetch pending queue status (excluding cancelled appointments)
$pending_queue_query = $conn->query("SELECT COUNT(*) AS total FROM queue q JOIN appointments a ON q.appointment_id=a.appointment_id WHERE a.patient_id=$user_id AND q.status='pending'");
$pending_queue = $pending_queue_query->fetch_assoc()['total'];

// Handle appointment cancellation
if (isset($_GET['cancel_appointment_id'])) {
    $appointment_id = (int)$_GET['cancel_appointment_id'];

    // Start a transaction to ensure both appointments and queue are updated together
    mysqli_begin_transaction($conn);
    try {
        // Mark the appointment as cancelled
        $cancel_appointment_query = "UPDATE appointments SET status='cancelled' WHERE appointment_id = ?";
        $stmt = mysqli_prepare($conn, $cancel_appointment_query);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);
        
        // Remove the corresponding queue entry
        $cancel_queue_query = "DELETE FROM queue WHERE appointment_id = ?";
        $stmt = mysqli_prepare($conn, $cancel_queue_query);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);

        // Commit the transaction
        mysqli_commit($conn);

        // Redirect back to the appointments page after cancellation
        header('Location: my_appointments.php');
        exit();
    } catch (Exception $e) {
        // If something fails, roll back the transaction
        mysqli_roll_back($conn);
        echo "<pre>Error: " . $e->getMessage() . "</pre>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - SmartClinic</title>
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
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="book_appointment.php" class="nav-item">Book Appointment</a>
            <a href="my_appointments.php" class="nav-item active">My Appointments</a>
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
                <h3>My Appointments</h3>
                <p>Manage your appointments and cancel as necessary</p>
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

            <!-- List of appointments -->
            <h3>Appointments</h3>
            <div class="content-card">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $appointments_query = "SELECT appointment_id, appointment_date, appointment_time, service_id, status FROM appointments WHERE patient_id = $user_id ORDER BY appointment_date, appointment_time";
                        $appointments_result = mysqli_query($conn, $appointments_query);

                        while ($appointment = mysqli_fetch_assoc($appointments_result)) {
                            $service_query = "SELECT service_name FROM services WHERE service_id = " . $appointment['service_id'];
                            $service_result = mysqli_query($conn, $service_query);
                            $service = mysqli_fetch_assoc($service_result);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($service['service_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($appointment['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></td>
                            <td>
                                <span class="badge <?= match($appointment['status']) {
                                    'pending'   => 'bg-warning',
                                    'confirmed' => 'bg-primary',
                                    'ongoing'   => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                    'missed'    => 'bg-danger',
                                    default     => 'bg-secondary'
                                } ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($appointment['status'] === 'pending'): ?>
                                    <a href="?cancel_appointment_id=<?= $appointment['appointment_id'] ?>" class="action-btn danger-btn">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

</body>
</html>