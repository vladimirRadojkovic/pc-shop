<?php
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <div>
            <a href="index.php?page=all_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na proizvode
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <?php if (!empty($product['image_path'])): ?>
                <img src="<?= htmlspecialchars($product['image_path']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <img src="assets/img/no-image.png" class="img-fluid rounded" alt="Bez slike">
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <h4 class="text-primary"><?= number_format($product['price'], 2) ?> RSD</h4>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h5>Opis proizvoda</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'] ?: 'Nema dostupnog opisa.')) ?></p>
                    </div>
                    
                    <?php if (!empty($product['category'])): ?>
                    <div class="mb-3">
                        <h5>Kategorija</h5>
                        <p><span class="badge bg-secondary"><?= htmlspecialchars($product['category']) ?></span></p>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <form action="index.php?page=add_to_cart" method="POST" class="mt-3">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="col-form-label">Količina:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="99">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-cart-plus"></i> Dodaj u korpu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 