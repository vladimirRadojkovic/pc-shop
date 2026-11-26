<?php
require_once 'views/layout/header.php';
require_once 'views/layout/alert.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$today = date('Y-m-d');
$last7 = date('Y-m-d', strtotime('-6 days'));
$last30 = date('Y-m-d', strtotime('-29 days'));
$last90 = date('Y-m-d', strtotime('-89 days'));
$thisMonthStart = date('Y-m-01');
?>

<div class="container mt-4 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Izveštaji i analitika</h2>
        <div>
            <a href="index.php?page=admin" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Nazad na Dashboard
            </a>
        </div>
    </div>

    <!-- Filter po datumu -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin_reports">
                <div class="col-md-3">
                    <label for="date_from" class="form-label fw-semibold">Datum od</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label fw-semibold">Datum do</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
                </div>
                <div class="col-md-6 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Primeni filter
                    </button>
                    <a href="index.php?page=admin_reports" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Resetuj
                    </a>
                </div>
            </form>

            <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted me-1">Brzi period:</small>
                <a href="index.php?page=admin_reports&date_from=<?= $last7 ?>&date_to=<?= $today ?>" class="btn btn-sm btn-outline-dark">Poslednjih 7 dana</a>
                <a href="index.php?page=admin_reports&date_from=<?= $last30 ?>&date_to=<?= $today ?>" class="btn btn-sm btn-outline-dark">Poslednjih 30 dana</a>
                <a href="index.php?page=admin_reports&date_from=<?= $last90 ?>&date_to=<?= $today ?>" class="btn btn-sm btn-outline-dark">Poslednjih 90 dana</a>
                <a href="index.php?page=admin_reports&date_from=<?= $thisMonthStart ?>&date_to=<?= $today ?>" class="btn btn-sm btn-outline-dark">Ovaj mesec</a>
                <a href="index.php?page=admin_reports" class="btn btn-sm btn-outline-dark">Svi podaci</a>
            </div>
        </div>
    </div>

    <!-- KPI kartice -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Ukupan prihod</div>
                    <div class="fs-4 fw-bold text-primary mt-1"><?= number_format($totalRevenue, 2, ',', '.') ?> RSD</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Broj porudžbina</div>
                    <div class="fs-4 fw-bold text-success mt-1"><?= $totalOrders ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Prodatih artikala</div>
                    <div class="fs-4 fw-bold text-info mt-1"><?= $totalItems ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Prosečna porudžbina</div>
                    <div class="fs-4 fw-bold text-warning mt-1"><?= number_format($avgOrderValue, 2, ',', '.') ?> RSD</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Tok novca -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Tok novca (Prihod po danima)</h5>
            <?php if ($dateFrom !== '' || $dateTo !== ''): ?>
                <small class="text-light">
                    Period: <?= $dateFrom !== '' ? date('d.m.Y', strtotime($dateFrom)) : 'Početak' ?>
                    &ndash;
                    <?= $dateTo !== '' ? date('d.m.Y', strtotime($dateTo)) : 'Danas' ?>
                </small>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($cashFlowData)): ?>
                <div class="alert alert-info mb-0">Nema podataka o prodaji za izabrani period.</div>
            <?php else: ?>
                <div style="position: relative; height: 340px;">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Najprodavaniji proizvodi i 3. Top 5 kupaca -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Najprodavaniji proizvodi</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($bestSellersData)): ?>
                        <div class="alert alert-info mb-0">Nema prodatih proizvoda u izabranom periodu.</div>
                    <?php else: ?>
                        <div style="position: relative; height: 360px;">
                            <canvas id="bestSellersChart"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Top 5 kupaca</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($topCustomersData)): ?>
                        <div class="alert alert-info mb-0">Nema podataka o kupcima u izabranom periodu.</div>
                    <?php else: ?>
                        <div style="position: relative; height: 360px;">
                            <canvas id="topCustomersChart"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const cashFlowRaw = <?= json_encode($cashFlowData, JSON_UNESCAPED_UNICODE) ?>;
    const bestSellersRaw = <?= json_encode($bestSellersData, JSON_UNESCAPED_UNICODE) ?>;
    const topCustomersRaw = <?= json_encode($topCustomersData, JSON_UNESCAPED_UNICODE) ?>;

    function formatRsd(value) {
        return new Intl.NumberFormat('sr-RS', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value) + ' RSD';
    }

    function formatDateSr(isoDate) {
        const parts = isoDate.split('-');
        if (parts.length !== 3) return isoDate;
        return `${parts[2]}.${parts[1]}.${parts[0]}`;
    }

    // 1. Tok novca (Line Chart)
    if (cashFlowRaw.length > 0) {
        const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(cashFlowCtx, {
            type: 'line',
            data: {
                labels: cashFlowRaw.map(row => formatDateSr(row.sale_date)),
                datasets: [{
                    label: 'Dnevni prihod (RSD)',
                    data: cashFlowRaw.map(row => Number(row.daily_revenue)),
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.15)',
                    pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const row = cashFlowRaw[context.dataIndex];
                                return [
                                    'Prihod: ' + formatRsd(Number(row.daily_revenue)),
                                    'Broj porudžbina: ' + row.order_count
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('sr-RS').format(value) + ' RSD';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Najprodavaniji proizvodi (Horizontal Bar Chart)
    if (bestSellersRaw.length > 0) {
        const bestSellersCtx = document.getElementById('bestSellersChart').getContext('2d');
        new Chart(bestSellersCtx, {
            type: 'bar',
            data: {
                labels: bestSellersRaw.map(row => row.name),
                datasets: [{
                    label: 'Prodata količina (kom)',
                    data: bestSellersRaw.map(row => Number(row.total_quantity)),
                    backgroundColor: 'rgba(25, 135, 84, 0.75)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const row = bestSellersRaw[context.dataIndex];
                                return [
                                    'Prodato: ' + row.total_quantity + ' kom',
                                    'Ukupan prihod: ' + formatRsd(Number(row.total_revenue))
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // 3. Top 5 kupaca (Bar Chart)
    if (topCustomersRaw.length > 0) {
        const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');
        const customerColors = [
            'rgba(13, 110, 253, 0.8)',
            'rgba(111, 66, 193, 0.8)',
            'rgba(32, 201, 151, 0.8)',
            'rgba(253, 126, 20, 0.8)',
            'rgba(220, 53, 69, 0.8)'
        ];
        new Chart(topCustomersCtx, {
            type: 'bar',
            data: {
                labels: topCustomersRaw.map(row => row.username),
                datasets: [{
                    label: 'Ukupno potrošeno (RSD)',
                    data: topCustomersRaw.map(row => Number(row.total_spent)),
                    backgroundColor: topCustomersRaw.map((_, i) => customerColors[i % customerColors.length]),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const row = topCustomersRaw[context.dataIndex];
                                return [
                                    'Potrošeno: ' + formatRsd(Number(row.total_spent)),
                                    'Broj porudžbina: ' + row.order_count,
                                    'Kupljeno artikala: ' + row.total_items
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('sr-RS').format(value) + ' RSD';
                            }
                        }
                    }
                }
            }
        });
    }
</script>

<?php require_once 'views/layout/footer.php'; ?>
