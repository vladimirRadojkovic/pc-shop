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
        <h2>Izmena proizvoda</h2>
        <div>
            <a href="index.php?page=admin" class="btn btn-secondary me-2">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a href="index.php?page=list_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na listu
            </a>
        </div>
    </div>

    <?php if (!isset($product) || empty($product)): ?>
        <div class="alert alert-danger">Proizvod nije pronađen.</div>
    <?php else: ?>
        <form method="POST" action="index.php?page=update_product" enctype="multipart/form-data" class="shadow p-4 bg-light rounded">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Naziv proizvoda</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Cena (RSD)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategorija</label>
                <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($product['category']) ?>">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Slika</label>
                <?php if (!empty($product['image_path'])): ?>
                    <div class="mb-2">
                        <img src="<?= htmlspecialchars($product['image_path']) ?>" class="img-thumbnail" style="max-height: 100px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image">
                            <label class="form-check-label" for="delete_image">Obriši postojeću sliku</label>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Ostavite prazno da zadržite postojeću sliku</small>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Sačuvaj izmene</button>
                <a href="index.php?page=list_products" class="btn btn-outline-secondary">Odustani</a>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php require_once 'views/layout/footer.php'; ?>