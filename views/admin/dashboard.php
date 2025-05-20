<?php
require_once 'config/config.php';
require_once 'views/layout/header.php';

// Session should already be started in header.php, but check just in case
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check for status message (for add product confirmation)
$status_message = '';
if (isset($_GET['status']) && $_GET['status'] === 'product_added') {
    $status_message = 'Proizvod je uspešno dodat!';
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Admin Panel</h2>
    
    <?php if (!empty($status_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($status_message) ?></div>
    <?php endif; ?>
    
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="list-group">
                <a href="index.php?page=add_product" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Dodaj novi proizvod</span>
                    <i class="bi bi-plus-circle"></i>
                </a>
                <a href="index.php?page=edit_product" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Izmeni postojeći proizvod</span>
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="index.php?page=delete_product" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Obriši proizvod</span>
                    <i class="bi bi-trash"></i>
                </a>
                <a href="index.php?page=orders" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-light shadow-sm">
                    <span>Pregled porudžbina</span>
                    <i class="bi bi-box"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>