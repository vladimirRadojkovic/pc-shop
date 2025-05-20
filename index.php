<?php
// Initialize the application
require_once 'setup.php';

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
    case 'admin':
        include 'views/admin/dashboard.php';
        break;
    case 'admin_orders':
        include 'views/admin/admin_orders.php';
        break;
    case 'admin_order_details':
        include 'views/admin/admin_order_details.php';
        break;
    case 'all_products':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->listProducts();
        break;
    case 'product_details':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->showProductDetails($id);
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
    case 'list_products':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->listProductsAdmin();
        break;
    case 'edit_product':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->showEditForm($id);
        break;
    case 'update_product':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->updateProduct();
        break;
    case 'delete_product':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->deleteProduct($id);
        break;
    // Cart functionality
    case 'add_to_cart':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->addToCart();
        break;
    case 'cart':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->viewCart();
        break;
    case 'update_cart_item':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->updateCartItem();
        break;
    case 'remove_cart_item':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->removeCartItem();
        break;
    case 'clear_cart':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->clearCart();
        break;
    case 'checkout':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->checkout();
        break;
    case 'order_history':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->orderHistory();
        break;
    case 'order_details':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->orderDetails($id);
        break;
    case 'logout':
        session_destroy();
        redirect('login');
        break;
    default:
        include 'views/home.php';
        break;
}