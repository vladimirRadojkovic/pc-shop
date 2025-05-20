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
        <h2>Detalji porudžbine #<?= $order['id'] ?></h2>
        <div>
            <a href="index.php?page=order_history" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na istoriju
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Informacije o porudžbini</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Broj porudžbine:</strong> #<?= $order['id'] ?></p>
                    <p><strong>Korisnik:</strong> <?= $_SESSION['user']['username'] ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Datum:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>Ukupan iznos:</strong> <?= number_format($total, 2) ?> RSD</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Proizvodi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Proizvod</th>
                            <th>Cena</th>
                            <th>Količina</th>
                            <th>Ukupno</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= number_format($item['price'], 2) ?> RSD</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['subtotal'], 2) ?> RSD</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Ukupno za plaćanje:</td>
                            <td class="fw-bold"><?= number_format($total, 2) ?> RSD</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 