<?php
session_start();

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
    case 'admin_products':
        include 'views/admin/product_list.php';
        break;
    case 'add_product':
        include 'views/admin/add_product.php';
        break;
    case 'cart':
        include 'views/user/cart.php';
        break;
    case 'catalog':
        include 'views/user/product_catalog.php';
        break;
    case 'checkout':
        include 'views/user/checkout.php';
        break;
    case 'logout':
        session_destroy();
        header("Location: index.php");
        exit;
    default:
        include 'views/home.php';
        break;
}