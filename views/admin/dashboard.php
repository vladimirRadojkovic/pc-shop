<?php
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Admin Panel</h2>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="list-group">
                <a href="index.php?page=add_product" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Dodaj novi proizvod</span>
                    <i class="bi bi-plus-circle"></i>
                </a>
                <a href="index.php?page=list_products" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Upravljanje proizvodima</span>
                    <i class="bi bi-grid"></i>
                </a>
                <a href="index.php?page=admin_orders" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Pregled porudžbina</span>
                    <i class="bi bi-box"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layout/footer.php'; ?>