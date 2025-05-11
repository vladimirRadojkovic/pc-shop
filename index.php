<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'register':
        include 'controllers/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') registerUser();
        include 'views/auth/register.php';
        break;
    case 'login':
        include 'controllers/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') loginUser();
        include 'views/auth/login.php';
        break;
    case 'logout':
        session_destroy();
        include 'views/auth/login.php';
        exit;
    case 'admin':
        include 'views/admin/dashboard.php';
        break;
    default:
        include 'views/home.php';
        break;
}