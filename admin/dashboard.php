<?php
session_start();
include '../config/database.php'; // DB connection
include '../includes/authorization.php'; // For authentication



if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}


// Fetch summary data
$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 3");
$total_patients = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 2");
$total_staff = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()");
$appointments_today = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM queue WHERE status='pending' OR status='ongoing'");
$queue_today = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <!-- Sidebar / Info Section -->
    <div class="login-section">
        <h1>Admin Dashboard</h1>
        <p class="welcome-text">Welcome, Admin!</p>

        <div class="login-form">
            <div class="form-group">
                <label>Total Patients</label>
                <input type="text" value="<?php echo $total_patients; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Total Staff</label>
                <input type="text" value="<?php echo $total_staff; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Appointments Today</label>
                <input type="text" value="<?php echo $appointments_today; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Patients in Queue</label>
                <input type="text" value="<?php echo $queue_today; ?>" readonly>
            </div>

            <div class="form-options">
                <a href="manage_users.php" class="login-btn">Manage Users</a>
                <a href="manage_services.php" class="login-btn">Manage Services</a>
                <a href="reports.php" class="login-btn">Reports</a>
                <a href="../logout.php" class="login-btn">Logout</a>
            </div>
        </div>
    </div>

    <!-- Brand Section -->
    <div class="brand-section">
        <div class="brand-text">
            <h2 class="brand-name">SmartClinic</h2>
            <h3 class="brand-subtitle">Admin Panel</h3>
            <p class="brand-tagline">Streamline appointments & monitor queues</p>
        </div>
    </div>
</div>
</body>
</html>

