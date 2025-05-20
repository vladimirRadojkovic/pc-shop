<?php 
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Upravljanje proizvodima</h2>
        <div>
            <a href="index.php?page=admin" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Nazad na Dashboard
            </a>
            <a href="index.php?page=add_product" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Dodaj novi proizvod
            </a>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">Nema dostupnih proizvoda.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Slika</th>
                        <th>Naziv</th>
                        <th>Cena</th>
                        <th>Kategorija</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <?php if (!empty($product['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($product['image_path']) ?>" class="img-thumbnail" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 50px;">
                                <?php else: ?>
                                    <img src="assets/img/no-image.png" class="img-thumbnail" alt="Bez slike" style="max-width: 50px;">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= number_format($product['price'], 2) ?> RSD</td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td>
                                <a href="index.php?page=edit_product&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Izmeni
                                </a>
                                <a href="index.php?page=delete_product&id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?')">
                                    <i class="bi bi-trash"></i> Obriši
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require_once 'views/layout/footer.php'; ?> 