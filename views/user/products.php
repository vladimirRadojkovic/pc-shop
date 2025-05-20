<?php
require_once '../../config/config.php';
require_once '../layout/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: /pc-shop/index.php");
    exit;
}

// Učitavanje proizvoda iz baze
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Lista proizvoda</h2>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                    <?php else: ?>
                        <img src="/pc-shop/assets/img/no-image.png" class="card-img-top" alt="Bez slike">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                        <p class="card-text"><strong>Cena:</strong> <?= number_format($row['price'], 2) ?> RSD</p>
                        <p class="card-text"><strong>Kategorija:</strong> <?= htmlspecialchars($row['category']) ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once '../layout/footer.php'; ?>
