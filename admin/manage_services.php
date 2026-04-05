<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$message = "";

/* ADD SERVICE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = trim($_POST['service_name']);
    $desc = trim($_POST['description']);
    $duration = (int) $_POST['estimated_duration'];

    $stmt = $conn->prepare("INSERT INTO services (service_name, description, estimated_duration) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $desc, $duration);

    if ($stmt->execute()) {
        header("Location: manage_services.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Error adding service. Service name may already exist.</p>";
    }
}

/* UPDATE SERVICE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $service_id = (int) $_POST['service_id'];
    $name = trim($_POST['service_name']);
    $desc = trim($_POST['description']);
    $duration = (int) $_POST['estimated_duration'];

    $stmt = $conn->prepare("UPDATE services SET service_name = ?, description = ?, estimated_duration = ? WHERE service_id = ?");
    $stmt->bind_param("ssii", $name, $desc, $duration, $service_id);

    if ($stmt->execute()) {
        header("Location: manage_services.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Error updating service.</p>";
    }
}

/* DELETE SERVICE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $service_id = (int) $_POST['service_id'];

    $stmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        header("Location: manage_services.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Cannot delete service because it is already used in appointments.</p>";
    }
}

$services = $conn->query("SELECT * FROM services ORDER BY service_id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - SmartClinic</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="dashboard-page admin-page">

<div class="app-container">
    <aside class="sidebar">
        <div class="logo">
            <h1>SmartClinic</h1>
            <span>Admin Panel</span>
        </div>

        <nav class="menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="manage_users.php" class="nav-item">Manage Users</a>
            <a href="manage_services.php" class="nav-item active">Manage Services</a>
            <a href="manage_schedules.php" class="nav-item">Manage Schedules</a>
            <a href="manage_appointments.php" class="nav-item">Appointments</a>
            <a href="queue_overview.php" class="nav-item">Queue Overview</a>
            <a href="reports.php" class="nav-item">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h2>Manage Services</h2>
        </header>

        <div class="content-card">
            <h3>Add Service</h3>
            <?= $message ?>

            <form method="POST" class="crud-form">
                <div class="form-group">
                    <label for="service_name">Service Name</label>
                    <input type="text" name="service_name" id="service_name" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="estimated_duration">Estimated Duration (minutes)</label>
                    <input type="number" name="estimated_duration" id="estimated_duration" min="1" required>
                </div>

                <button type="submit" name="add_service" class="btn-primary">Add Service</button>
            </form>
        </div>

        <div class="content-card">
            <h3>All Services</h3>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $services->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <?= $row['service_id'] ?>
                                    <input type="hidden" name="service_id" value="<?= $row['service_id'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="service_name" value="<?= htmlspecialchars($row['service_name']) ?>" required>
                                </td>
                                <td>
                                    <textarea name="description" rows="2"><?= htmlspecialchars($row['description']) ?></textarea>
                                </td>
                                <td>
                                    <input type="number" name="estimated_duration" value="<?= $row['estimated_duration'] ?>" min="1" required>
                                </td>
                                <td>
                                    <button type="submit" name="update_service" class="btn-primary">Update</button>
                                </td>
                                <td>
                                    <button type="submit" name="delete_service" class="btn-danger" onclick="return confirm('Delete this service?');">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>
</div>

</body>
</html>
