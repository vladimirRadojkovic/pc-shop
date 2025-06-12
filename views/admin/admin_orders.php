<?php
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

global $pdo;

try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.created_at, u.username, 
               COUNT(oi.id) as item_count,
               SUM(oi.quantity * p.price) as total_amount
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => "Greška pri učitavanju porudžbina: " . $e->getMessage()
    ];
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pregled svih porudžbina</h2>
        <div>
            <a href="index.php?page=admin" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na Dashboard
            </a>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Nema dostupnih porudžbina.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Korisnik</th>
                        <th>Datum</th>
                        <th>Broj stavki</th>
                        <th>Ukupan iznos</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['username']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['item_count'] ?></td>
                            <td><?= number_format($order['total_amount'], 2) ?> RSD</td>
                            <td>
                                <a href="index.php?page=admin_order_details&id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
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