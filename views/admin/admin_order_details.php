<?php
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
global $pdo;

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$orderId) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => "Neispravan ID porudžbine."
    ];
    header("Location: index.php?page=admin_orders");
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.username 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => "Porudžbina nije pronađena."
        ];
        header("Location: index.php?page=admin_orders");
        exit;
    }
    
    $stmt = $pdo->prepare("
        SELECT oi.quantity, p.id as product_id, p.name, p.price, 
               (oi.quantity * p.price) as subtotal
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = 0;
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    
} catch (PDOException $e) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => "Greška: " . $e->getMessage()
    ];
    header("Location: index.php?page=admin_orders");
    exit;
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalji porudžbine #<?= $order['id'] ?></h2>
        <div>
            <a href="index.php?page=admin_orders" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na porudžbine
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
                    <p><strong>Korisnik:</strong> <?= htmlspecialchars($order['username']) ?></p>
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
                            <th>ID</th>
                            <th>Proizvod</th>
                            <th>Cena</th>
                            <th>Količina</th>
                            <th>Ukupno</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $item['product_id'] ?></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= number_format($item['price'], 2) ?> RSD</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['subtotal'], 2) ?> RSD</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Ukupno za plaćanje:</td>
                            <td class="fw-bold"><?= number_format($total, 2) ?> RSD</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 