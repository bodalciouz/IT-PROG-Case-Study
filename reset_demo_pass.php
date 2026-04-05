<?php
require_once "config/database.php";

$newPassword = "demo123";
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$emails = [
    'admin@smartclinic.com',
    'staff@smartclinic.com',
    'maria.santos@smartclinic.com',
    'paolo.reyes@smartclinic.com',
    'carl_crespo@dlsu.edu.ph',
    'oleg@gmail.com',
    'akolangba@gmail.com',
    'john@yahoo.com',
    'jane@gmail.com',
    'mark.delacruz@gmail.com',
    'angela.torres@gmail.com',
    'kevin.lim@yahoo.com',
    'sofia.navarro@gmail.com',
    'luis.garcia@gmail.com',
    'bea.fernandez@yahoo.com'
];

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");

foreach ($emails as $email) {
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();
    echo "Password reset for: " . htmlspecialchars($email) . "<br>";
}

$stmt->close();

echo "<hr>";
echo "All listed accounts now use password: <strong>demo123</strong>";
?>