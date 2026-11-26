<?php
require_once 'config/config.php';

class ReportController {

    private function isValidDate($date) {
        if (empty($date)) {
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function showReports() {
        global $pdo;

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('login');
        }

        $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
        $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

        if (!$this->isValidDate($dateFrom)) {
            $dateFrom = '';
        }
        if (!$this->isValidDate($dateTo)) {
            $dateTo = '';
        }

        if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
            $tmp = $dateFrom;
            $dateFrom = $dateTo;
            $dateTo = $tmp;
        }

        $whereClauses = [];
        $params = [];

        if ($dateFrom !== '') {
            $whereClauses[] = "o.created_at >= :date_from";
            $params[':date_from'] = $dateFrom . ' 00:00:00';
        }
        if ($dateTo !== '') {
            $whereClauses[] = "o.created_at <= :date_to";
            $params[':date_to'] = $dateTo . ' 23:59:59';
        }

        $whereSql = !empty($whereClauses) ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

        try {
            // 1. Sažetak (KPI kartice)
            $summaryStmt = $pdo->prepare("
                SELECT
                    COUNT(DISTINCT o.id) AS total_orders,
                    COALESCE(SUM(oi.quantity), 0) AS total_items,
                    COALESCE(SUM(oi.quantity * oi.price), 0) AS total_revenue
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                $whereSql
            ");
            $summaryStmt->execute($params);
            $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

            $totalOrders = (int)($summary['total_orders'] ?? 0);
            $totalItems = (int)($summary['total_items'] ?? 0);
            $totalRevenue = (float)($summary['total_revenue'] ?? 0);
            $avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0.0;

            // 2. Tok novca (prihod po danima)
            $cashFlowStmt = $pdo->prepare("
                SELECT
                    DATE(o.created_at) AS sale_date,
                    SUM(oi.quantity * oi.price) AS daily_revenue,
                    COUNT(DISTINCT o.id) AS order_count
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                $whereSql
                GROUP BY DATE(o.created_at)
                ORDER BY sale_date ASC
            ");
            $cashFlowStmt->execute($params);
            $cashFlowData = $cashFlowStmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Najprodavaniji proizvodi (top 10 po prodatoj količini)
            $bestSellersStmt = $pdo->prepare("
                SELECT
                    p.id,
                    p.name,
                    SUM(oi.quantity) AS total_quantity,
                    SUM(oi.quantity * oi.price) AS total_revenue
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                $whereSql
                GROUP BY p.id, p.name
                ORDER BY total_quantity DESC, total_revenue DESC
                LIMIT 10
            ");
            $bestSellersStmt->execute($params);
            $bestSellersData = $bestSellersStmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Top 5 kupaca (po ukupnoj potrošnji)
            $topCustomersStmt = $pdo->prepare("
                SELECT
                    u.id,
                    u.username,
                    COUNT(DISTINCT o.id) AS order_count,
                    SUM(oi.quantity) AS total_items,
                    SUM(oi.quantity * oi.price) AS total_spent
                FROM orders o
                JOIN users u ON o.user_id = u.id
                JOIN order_items oi ON o.id = oi.order_id
                $whereSql
                GROUP BY u.id, u.username
                ORDER BY total_spent DESC
                LIMIT 5
            ");
            $topCustomersStmt->execute($params);
            $topCustomersData = $topCustomersStmt->fetchAll(PDO::FETCH_ASSOC);

            require 'views/admin/reports.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri generisanju izveštaja: " . $e->getMessage()
            ];
            redirect('admin');
        }
    }
}
