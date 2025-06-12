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
        <h2>Proizvodi</h2>
        <div>
            <a href="index.php?page=cart" class="btn btn-primary">
                <i class="bi bi-cart"></i> 
                Korpa 
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <span class="badge bg-danger"><?= count($_SESSION['cart']) ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">Nema dostupnih proizvoda.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-top text-center pt-3">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= htmlspecialchars('/assets/img/' . $product['image_path']) ?>" class="img-fluid" style="height: 150px; object-fit: contain;" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <img src="assets/img/no-image.png" class="img-fluid" style="height: 150px; object-fit: contain;" alt="Bez slike">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-truncate"><?= htmlspecialchars($product['description'] ?: 'Nema opisa') ?></p>
                            <p class="card-text fw-bold text-primary"><?= number_format($product['price'], 2) ?> RSD</p>
                            <?php if (!empty($product['category'])): ?>
                                <p class="card-text"><span class="badge bg-secondary"><?= htmlspecialchars($product['category']) ?></span></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between">
                            <a href="index.php?page=product_details&id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Detalji
                            </a>
                            <form action="index.php?page=add_to_cart" method="POST" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-cart-plus"></i> Dodaj u korpu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>
