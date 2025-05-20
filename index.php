<?php
// Initialize the application
require_once 'init.php';

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
        redirect('login');
        break;
    case 'admin':
        include 'views/admin/dashboard.php';
        break;
    case 'products':
        include 'views/user/products.php';
        break;
    case 'add_product':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->showAddForm();
        break;
    case 'process_product':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->processAddForm();
        break;
    default:
        include 'views/home.php';
        break;
}