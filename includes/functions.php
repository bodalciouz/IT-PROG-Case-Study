<?php
require_once __DIR__ . '/../config/database.php';

function sanitizeInput($data){
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url){
    header("Location: $url");
    exit();
}

function getUserFullName($user_id){
    global $conn;

    $user_id = (int)$user_id;

    $query = "SELECT first_name, last_name FROM users WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row['first_name'] . ' ' . $row['last_name'];
    }

    return '';
}
?>