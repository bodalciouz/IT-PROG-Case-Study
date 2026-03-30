<?php
session_start();
require_once "config/database.php";
require_once __DIR__ . '/includes/functions.php';

$error = "";
$success = "";

if(isset($_POST["register"])){

    $first_name = sanitizeInput($_POST["first_name"] ?? '');
    $last_name = sanitizeInput($_POST["last_name"] ?? '');
    $email = sanitizeInput($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';
    $contact = sanitizeInput($_POST["contact"] ?? '');
    $address = sanitizeInput($_POST["address"] ?? '');

    if(empty($first_name) || empty($last_name) || empty($email) || empty($password)){
        $error = "All required fields must be filled.";
    }elseif ($password != $confirm_password){
        $error = "Passwords do not match.";
    }else{
        $email = mysqli_real_escape_string($conn, $email);

        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if(mysqli_num_rows($check) > 0){
            $error = "Email already exists.";
        }else{
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 3;

            $insert = "INSERT INTO users 
                (role_id, first_name, last_name, email, password, contact_number, address)
                VALUES 
                ('$role_id', '$first_name', '$last_name', '$email', '$hashed_password', '$contact', '$address')";

            if(mysqli_query($conn, $insert)){
                $success = "Registration successful! You can now login.";
            }else{
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SmartClinic - Register</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="register-page">

<div class="container">

    <section class="login-section">
        <p class="welcome-text">WELCOME</p>
        <h1>Create your account</h1>

        <?php if(!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form class="login-form" method="POST" autocomplete="off">

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Enter First Name" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Enter Last Name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" placeholder="Optional">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Optional">
            </div>

            <button type="submit" name="register" class="login-btn">Register</button>

            <p class="signup-text">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </form>
    </section>

    <section class="brand-section">
        <div class="brand-text">
            <h2 class="brand-name">SmartClinic</h2>
            <p class="brand-subtitle">Clinic Appointment System</p>
        </div>
    </section>

</div>

</body>
</html>