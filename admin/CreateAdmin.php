<?php
//run only once (creates the admin locally)
include '../config/database.php';

$first_name = 'Admin';
$last_name = 'User';
$email = 'admin@smartclinic.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$contact_number = '09123456789';
$address = 'Clinic Address';
$role_id = 1; // Admin

$stmt = $conn->prepare("INSERT INTO users (role_id, first_name, last_name, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $role_id, $first_name, $last_name, $email, $password, $contact_number, $address);
$stmt->execute();
$stmt->close();

echo "Admin account created successfully!";
?>