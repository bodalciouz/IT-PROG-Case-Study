<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

// UPDATE STATUS
if(isset($_POST['update_status'])){
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

$query = "
SELECT a.*, u.first_name, u.last_name, s.service_name
FROM appointments a
JOIN users u ON a.patient_id = u.user_id
JOIN services s ON a.service_id = s.service_id
ORDER BY a.appointment_date ASC
";

$result = $conn->query($query);
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
                <p>View and update patient appointment statuses.</p>
            </div>

            <div class="content-card">
                <?php if($result->num_rows > 0): ?>
                <div class="table-wrapper">
                    <table class="styled-table">
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
                                <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['service_name']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <form method="POST" class="update-form">
                                        <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                        <select name="status" required>
                                            <option <?= $row['status'] === 'pending' ? 'selected' : '' ?>>pending</option>
                                            <option <?= $row['status'] === 'confirmed' ? 'selected' : '' ?>>confirmed</option>
                                            <option <?= $row['status'] === 'completed' ? 'selected' : '' ?>>completed</option>
                                            <option <?= $row['status'] === 'missed' ? 'selected' : '' ?>>missed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="update-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="no-data"><p>No appointments found.</p></div>
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