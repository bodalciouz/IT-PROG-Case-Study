<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(1);
require_once '../config/database.php';

$message = "";

/* CREATE USER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $role_id = (int) $_POST['role_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (role_id, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $role_id, $first_name, $last_name, $email, $password);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Error adding user. Email may already exist.</p>";
    }
}

/* UPDATE USER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = (int) $_POST['user_id'];
    $role_id = (int) $_POST['role_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['password']);

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET role_id = ?, first_name = ?, last_name = ?, email = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("issssi", $role_id, $first_name, $last_name, $email, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET role_id = ?, first_name = ?, last_name = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("isssi", $role_id, $first_name, $last_name, $email, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Error updating user.</p>";
    }
}

/* DELETE USER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int) $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Cannot delete this user because it is linked to other records.</p>";
    }
}

/* FETCH USERS WITH ROLE NAMES */
$users = $conn->query("
    SELECT users.*, roles.role_name
    FROM users
    JOIN roles ON users.role_id = roles.role_id
    ORDER BY users.user_id ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - SmartClinic</title>
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
            <a href="manage_users.php" class="nav-item active">Manage Users</a>
            <a href="manage_services.php" class="nav-item">Manage Services</a>
            <a href="manage_appointments.php" class="nav-item">Appointments</a>
            <a href="queue_overview.php" class="nav-item">Queue Overview</a>
            <a href="reports.php" class="nav-item">Reports</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <h2>Manage Users</h2>

        <div class="content-card">
            <h3>Add New User</h3>

            <?= $message ?>

            <form method="POST" class="crud-form">
                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select name="role_id" id="role_id" required>
                        <option value="">Select role</option>
                        <option value="1">Admin</option>
                        <option value="2">Staff</option>
                        <option value="3">Patient</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit" name="add_user" class="btn-primary">Add User</button>
            </form>
        </div>

        <div class="content-card">
            <h3>All Users</h3>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>New Password</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <?= $row['user_id'] ?>
                                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" required>
                                </td>
                                <td>
                                    <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
                                </td>
                                <td>
                                    <select name="role_id" required>
                                        <option value="1" <?= $row['role_id'] == 1 ? 'selected' : '' ?>>Admin</option>
                                        <option value="2" <?= $row['role_id'] == 2 ? 'selected' : '' ?>>Staff</option>
                                        <option value="3" <?= $row['role_id'] == 3 ? 'selected' : '' ?>>Patient</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="password" name="password" placeholder="Leave blank">
                                </td>
                                <td>
                                    <button type="submit" name="update_user" class="btn-primary">Update</button>
                                </td>
                                <td>
                                    <button type="submit" name="delete_user" class="btn-danger" onclick="return confirm('Delete this user?');">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

</div>

</body>
</html>