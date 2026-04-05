<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(2);

require_once '../config/database.php';

// Handle queue status
if(isset($_POST['action'])){
    $id = $_POST['queue_id'];
    $status = $_POST['action'];

    $stmt = $conn->prepare("UPDATE queue SET status=? WHERE queue_id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

$query = "
    SELECT 
        q.queue_id,
        q.status AS queue_status,
        q.estimated_wait,
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status AS appointment_status,
        u.first_name,
        u.last_name,
        s.service_name
    FROM queue q
    JOIN appointments a ON q.appointment_id = a.appointment_id
    JOIN users u ON a.patient_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    ORDER BY a.appointment_date ASC, a.appointment_time ASC, a.appointment_id ASC
";

$result = mysqli_query($conn, $query); 
$queues = [];
while ($row = mysqli_fetch_assoc($result)) {
    $queues[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Queue Overview - SmartClinic</title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="dashboard-page">

<div class="app-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <h2>Queue Overview</h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>Queue Status</h3>
                <p>Live status of all appointments and queues</p>
            </div>

            <?php if(empty($queues)): ?>
                <div class="no-data">
                    <p>There are no active queue entries.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive content-card">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Service</th>
                                <th>Queue #</th>
                                <th>Est. Wait</th>
                                <th>Appointment Status</th>
                                <th>Queue Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $current_date = '';
                            $queue_pos = 0;
                            foreach($queues as $q):
                                if($current_date !== $q['appointment_date']){
                                    $current_date = $q['appointment_date'];
                                    $queue_pos = 1;
                                } else {
                                    $queue_pos++;
                                }

                                $appt_badge = match($q['appointment_status']){
                                    'pending'   => 'bg-warning',
                                    'confirmed' => 'bg-primary',
                                    'ongoing'   => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                    'missed'    => 'bg-danger',
                                    default     => 'bg-secondary'
                                };
                                $queue_badge = match($q['queue_status']){
                                    'pending'   => 'bg-warning',
                                    'ongoing'   => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                    'missed'    => 'bg-danger',
                                    default     => 'bg-secondary'
                                };
                            ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($q['appointment_date'])) ?></td>
                                    <td><?= date('g:i A', strtotime($q['appointment_time'])) ?></td>
                                    <td><?= htmlspecialchars($q['first_name']." ".$q['last_name']) ?></td>
                                    <td><?= htmlspecialchars($q['service_name']) ?></td>
                                    <td><strong>#<?= $queue_pos ?></strong></td>
                                    <td><?= $q['estimated_wait'] ?> mins</td>
                                    <td><span class="badge <?= $appt_badge ?>"><?= ucfirst($q['appointment_status']) ?></span></td>
                                    <td><span class="badge <?= $queue_badge ?>"><?= ucfirst($q['queue_status']) ?></span></td>
                                    <td>
                                        <?php if($q['queue_status'] === 'pending'): ?>
                                            <form method="POST" style="display:flex; gap:5px;">
                                                <input type="hidden" name="queue_id" value="<?= $q['queue_id'] ?>">
                                                <button name="action" value="ongoing" class="update-btn">Start</button>
                                                <button name="action" value="completed" class="update-btn">Done</button>
                                                <button name="action" value="missed" class="delete-btn">Miss</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

<script>
    setTimeout(function(){ location.reload(); }, 30000);
</script>

</body>
</html>