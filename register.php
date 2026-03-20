<?php
session_start();
require_once "config/database.php";

if(isset($_POST["register"])){

    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];

    if(empty($first_name) || empty($last_name) || empty($email) || empty($password)){
        echo "All required fields must be filled.<br>";
    }
    else{
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if(mysqli_num_rows($check) > 0){
            echo "Email already exists.<br>";
        }
        else{
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 3;

            $insert = "INSERT INTO users 
            (role_id, first_name, last_name, email, password, contact_number, address)
            VALUES 
            ('$role_id', '$first_name', '$last_name', '$email', '$hashed_password', '$contact', '$address')";

            if(mysqli_query($conn, $insert)){
                echo "Registration successful! <a href='login.php'>Login here</a>";
            }
            else{
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - SmartClinic</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-card">
    <div class="login-side">
        <h1>Create Account</h1>

        <form method="POST">

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address">
            </div>

            <button type="submit" name="register" class="login-btn">Register</button>
        </form>

        <p class="signup-text">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>

    <div class="brand-side">
        <div class="logo-box">
            <h2 class="brand-name">SmartClinic</h2>
            <div class="brand-sub">APPOINTMENT SYSTEM</div>
            <p class="tagline">Smarter healthcare scheduling</p>
        </div>
    </div>
</div>

</body>
</html>