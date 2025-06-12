<?php 
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

$cartTotal = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Vaša korpa</h2>
        <div>
            <a href="index.php?page=all_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nastavi kupovinu
            </a>
        </div>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">
            <p>Vaša korpa je prazna.</p>
            <a href="index.php?page=all_products" class="btn btn-primary">Pogledaj proizvode</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Proizvod</th>
                        <th>Cena</th>
                        <th>Količina</th>
                        <th>Ukupno</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= number_format($item['price'], 2) ?> RSD</td>
                            <td>
                                <form method="POST" action="index.php?page=update_cart_item" class="d-flex align-items-center">
                                    <input type="hidden" name="product_id" value="<?= $productId ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" class="form-control" style="width: 70px">
                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                            </td>
                            <td><?= number_format($item['price'] * $item['quantity'], 2) ?> RSD</td>
                            <td>
                                <a href="index.php?page=remove_cart_item&id=<?= $productId ?>" class="btn btn-sm btn-danger" onclick="return confirm('Da li ste sigurni da želite da uklonite ovaj proizvod iz korpe?')">
                                    <i class="bi bi-trash"></i> Ukloni
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Ukupno za plaćanje:</td>
                        <td class="fw-bold"><?= number_format($cartTotal, 2) ?> RSD</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?page=clear_cart" class="btn btn-outline-danger" onclick="return confirm('Da li ste sigurni da želite da ispraznite korpu?')">
                <i class="bi bi-x-circle"></i> Isprazni korpu
            </a>
            <a href="index.php?page=checkout" class="btn btn-success">
                <i class="bi bi-credit-card"></i> Plaćanje
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?> 