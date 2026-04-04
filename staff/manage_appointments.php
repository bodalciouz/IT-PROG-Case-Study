<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

if(isset($_POST['update_status'])){
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();
}

$query = "
SELECT a.*, u.first_name, u.last_name, s.service_name
FROM appointments a
JOIN users u ON a.patient_id = u.user_id
JOIN services s ON a.service_id = s.service_id
ORDER BY appointment_date ASC
";

$result = $conn->query($query);
?>

<body class="dashboard-page">

<div class="app-container">

<div class="sidebar">
    <div class="logo">
        <h1>SmartClinic</h1>
        <span>Staff Panel</span>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="nav-item">Dashboard</a>
        <a href="manage_schedule.php" class="nav-item">Manage Schedule</a>
        <a href="manage_appointments.php" class="nav-item active">Appointments</a>
        <a href="manage_queue.php" class="nav-item">Queue</a>
    </div>

    <div class="logout">
        <a href="../logout.php" class="nav-item">Logout</a>
    </div>
</div>

<div class="main-content">

<div class="top-bar">
    <h2>Manage Appointments</h2>
</div>

<div class="dashboard">

<div class="content-card">
    <h3 class="section-title">Appointments List</h3>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>

            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['first_name'] ?> <?= $row['last_name'] ?></td>
                    <td><?= $row['service_name'] ?></td>
                    <td><?= $row['appointment_date'] ?></td>
                    <td><?= $row['status'] ?></td>

                    <td class="action-cell">
                        <form method="POST">
                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">

                            <select name="status">
                                <option>pending</option>
                                <option>confirmed</option>
                                <option>ongoing</option>
                                <option>completed</option>
                                <option>missed</option>
                            </select>

                            <button class="table-btn" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>
</div>

</div>
</div>
</div>
</body>