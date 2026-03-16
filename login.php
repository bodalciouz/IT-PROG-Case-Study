<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/authorization.php';

if(isLoggedIn()){
    redirectToDashboard($_SESSION['role_id']);
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(!empty($email) && !empty($password)){

        $email = mysqli_real_escape_string($conn, $email);

        $query = "SELECT user_id, password, role_id FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if($result && mysqli_num_rows($result) === 1){
            $user = mysqli_fetch_assoc($result);

            $user_id = $user['user_id'];
            $hashed_password = $user['password'];
            $role_id = $user['role_id'];

            if(password_verify($password, $hashed_password)){
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role_id'] = $role_id;

                redirectToDashboard($role_id);

            }else{
                $error = "Incorrect password.";
            }

        }else{
            $error = "Email not registered.";
        }

    }else{
        $error = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SmartClinic - Login</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>

<div class="container">

<div class="login-section">

<p class="welcome-text">WELCOME BACK</p>
<h1>Sign in to your account</h1>

<?php if(!empty($error)): ?>
<p style="color:#ff6b6b;font-weight:600;margin-bottom:10px;">
<?php echo $error; ?>
</p>
<?php endif; ?>

<form class="login-form" method="POST">

<div class="form-group">
<label>Email</label>
<input type="email" name="email" placeholder="Enter Email" required>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" placeholder="Enter Password" required>
</div>

<button type="submit" class="login-btn">Log In</button>

<p class="signup-text">
New User? <a href="register.php">Sign up</a>
</p>

</form>

</div>

<div class="brand-section">

<div class="logo-container">
<img src="assets/images/login_logo.svg" alt="SmartClinic Logo" style="width:150px;">
</div>

<div class="brand-text">
<h2 class="brand-name">SmartClinic</h2>
<p class="brand-subtitle">Clinic Appointment System</p>
</div>

</div>

</div>

</body>
</html>