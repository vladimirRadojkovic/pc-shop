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
        <h2>Istorija porudžbina</h2>
        <div>
            <a href="index.php?page=all_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na proizvode
            </a>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <p>Nemate još nijednu porudžbinu.</p>
            <a href="index.php?page=all_products" class="btn btn-primary">Pogledaj proizvode</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Broj porudžbine</th>
                        <th>Datum</th>
                        <th>Broj proizvoda</th>
                        <th>Ukupan iznos</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['total_items'] ?></td>
                            <td><?= number_format($order['total_amount'], 2) ?> RSD</td>
                            <td>
                                <a href="index.php?page=order_details&id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detalji
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