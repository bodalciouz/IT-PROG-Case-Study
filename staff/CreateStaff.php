<?php
// Run only once (creates a staff account)

include '../config/database.php';

$first_name = 'Staff';
$last_name = 'User';
$email = 'staff@smartclinic.com';
$password = password_hash('staff123', PASSWORD_DEFAULT);
$contact_number = '09987654321';
$address = 'Clinic Staff Room';
$role_id = 2; // Staff

$stmt = $conn->prepare("
    INSERT INTO users 
    (role_id, first_name, last_name, email, password, contact_number, address) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "issssss",
    $role_id,
    $first_name,
    $last_name,
    $email,
    $password,
    $contact_number,
    $address
);

if($stmt->execute()){
    echo "Staff account created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>