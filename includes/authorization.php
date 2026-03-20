<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

function isLoggedIn(){
    return isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
}

function requireLogin(){
    if(!isLoggedIn()){
        header("Location: /login.php");
        exit();
    }
}

function checkRole($requiredRoleID){
    if(!isset($_SESSION['role_id'])){
        header("Location: /login.php");
        exit();
    }

    if($_SESSION['role_id'] != $requiredRoleID){
        redirectToDashboard($_SESSION['role_id']);
        exit();
    }
}

function redirectToDashboard($role_id){
    switch($role_id){
        case 1:
            header("Location: ./admin/dashboard.php");
            break;
        case 2:
            header("Location: ./staff/dashboard.php");
            break;
        case 3:
            header("Location: ./patients/dashboard.php");
            break;
        default:
            header("Location: /login.php");
    }
    exit();
}