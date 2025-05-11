<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'login':
        include 'views/auth/login.php';
        break;
    case 'register':
        include 'views/auth/register.php';
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
        include 'controllers/AuthController.php';
        // logout();  
        break;
    default:
        include 'views/user/product_catalog.php';
}