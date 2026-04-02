<?php
require_once '../includes/authorization.php';
requireLogin();
checkRole(3);
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT first_name, last_name, email, contact_number, address FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$success = '';
$error = '';

if(isset($_POST['update_profile'])){
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');

    if(!empty($first_name) && !empty($last_name) && !empty($email)){

        $update_query = "
            UPDATE users 
            SET first_name='$first_name',
                last_name='$last_name',
                email='$email',
                contact_number='$contact_number',
                address='$address'
            WHERE user_id=$user_id
        ";

        if(mysqli_query($conn, $update_query)){
            $success = "Profile updated successfully!";

            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['email'] = $email;
            $user['contact_number'] = $contact_number;
            $user['address'] = $address;

        } else {
            $error = "Failed to update profile.";
        }

    } else {
        $error = "First name, last name, and email are required.";
    }
}

if(isset($_POST['delete_account'])){
    $delete_query = "DELETE FROM users WHERE user_id=$user_id";

    if(mysqli_query($conn, $delete_query)){
        session_destroy();

        echo "<script>
            alert('Account deleted successfully.');
            window.location.href = '../login.php';
        </script>";
        exit();

    } else {
        $error = "Failed to delete account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - SmartClinic</title>
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
            <a href="profile.php" class="nav-item active">My Profile</a>
            <a href="notifications.php" class="nav-item">Notifications</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($user['first_name'] ?? ''); ?></h2>
        </header>

        <section class="dashboard">
            <div class="dashboard-header">
                <h3>My Profile</h3>
                <p>Update your personal information</p>
            </div>

            <?php if($success): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php elseif($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form class="crud-form" method="POST">

                <div class="form-group-profile">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                </div>

                <div class="form-group-profile">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                </div>

                <div class="form-group-profile">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>

                <div class="form-group-profile">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>">
                </div>

                <div class="form-group-profile">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                </div>

                <button type="submit" name="update_profile" class="update-btn">
                    Update Profile
                </button>

            </form>

            <form method="POST" id="deleteForm">
                <button type="submit" name="delete_account" class="delete-btn">
                    Delete Account
                </button>
            </form>

            <script type="text/javascript">
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function(e) {
                const confirmDelete = confirm("Are you sure you want to delete your account? This action cannot be undone!");
                if(!confirmDelete){
                    e.preventDefault();
                }
            });
            </script>

        </section>

        <footer class="footer">
            SmartClinic © 2026. All Rights Reserved
        </footer>
    </main>

</div>

</body>
</html>