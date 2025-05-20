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
        <h2 class="mb-4 text-center">Dodaj novi proizvod</h2>
        <div>
            <a href="index.php?page=admin" class="btn btn-secondary me-2">
                <i class="bi bi-house"></i> Nazad na Dashboard
            </a>
            <a href="index.php?page=list_products" class="btn btn-secondary">
                <i class="bi bi-list"></i> Lista proizvoda
            </a>
        </div>
    </div>

    <form action="index.php?page=process_product" method="POST" enctype="multipart/form-data" class="shadow p-4 bg-light rounded">
        <div class="mb-3">
            <label for="name" class="form-label">Naziv proizvoda</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Opis</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Cena (RSD)</label>
            <input type="number" step="0.01" class="form-control" name="price" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Kategorija</label>
            <input type="text" class="form-control" name="category">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Slika</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Sačuvaj proizvod</button>
            <a href="index.php?page=list_products" class="btn btn-outline-secondary">Odustani</a>
        </div>
    </form>
</div>

<?php require_once 'views/layout/footer.php'; ?>
