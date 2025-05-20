<?php
// require_once '../../config/config.php';
require_once '../layout/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /pc-shop/index.php");
    exit;
}
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Dodaj novi proizvod</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Proizvod uspešno dodat!</div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="shadow p-4 bg-light rounded">
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

        <button type="submit" class="btn btn-primary">Dodaj proizvod</button>
    </form>
</div>

<?php require_once '../layout/footer.php'; ?>
