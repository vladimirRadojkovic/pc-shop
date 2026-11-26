<?php
require_once __DIR__ . '/setup.php';

$isCli = (php_sapi_name() === 'cli');
$force = $isCli
    ? in_array('--force', $argv ?? [], true)
    : (isset($_GET['force']) && $_GET['force'] === '1');

function seedOutput($message, $isCli) {
    if ($isCli) {
        echo $message . PHP_EOL;
    }
}

try {
    $pdo->beginTransaction();

    // 1. Test korisnici (kupci)
    $sampleUsers = [
        'marko_p',
        'jelena_s',
        'nikola_m',
        'ana_k',
        'stefan_d',
        'milica_j',
        'luka_v',
        'jovana_t',
    ];

    $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);
    $findUserStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $insertUserStmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");

    $userIds = [];
    $createdUsers = 0;

    foreach ($sampleUsers as $username) {
        $findUserStmt->execute([$username]);
        $existingId = $findUserStmt->fetchColumn();
        if ($existingId) {
            $userIds[] = (int)$existingId;
        } else {
            $insertUserStmt->execute([$username, $defaultPassword]);
            $userIds[] = (int)$pdo->lastInsertId();
            $createdUsers++;
        }
    }

    // 2. Test proizvodi (IT oprema sa realističnim cenama u RSD)
    $sampleProducts = [
        ['AMD Ryzen 7 7800X3D', '8-jezgarni procesor za gejming sa 3D V-Cache tehnologijom', 48990.00, 'Procesori'],
        ['Intel Core i5-14600K', '14-jezgarni desktop procesor nove generacije', 36990.00, 'Procesori'],
        ['NVIDIA GeForce RTX 4070 Super', '12GB GDDR6X grafička kartica za 1440p i 4K gejming', 79990.00, 'Grafičke kartice'],
        ['AMD Radeon RX 7800 XT', '16GB GDDR6 grafička kartica visokih performansi', 65990.00, 'Grafičke kartice'],
        ['Kingston Fury Beast 32GB DDR5', '2x16GB 6000MHz CL36 memorijski kit', 14990.00, 'RAM memorije'],
        ['Samsung 990 PRO 2TB NVMe', 'M.2 PCIe 4.0 SSD sa brzinom čitanja do 7450 MB/s', 21990.00, 'SSD diskovi'],
        ['WD Black SN850X 1TB NVMe', 'Brzi M.2 NVMe SSD disk za gejming', 11990.00, 'SSD diskovi'],
        ['LG UltraGear 27" QHD 165Hz', 'IPS gejming monitor 2560x1440, 1ms odziv', 34990.00, 'Monitori'],
        ['Logitech G Pro X Superlight', 'Ultralaški bežični gejming miš (63g)', 15490.00, 'Periferije'],
        ['Keychron K2 Pro Mehanička tastatura', 'Bežična mehanička tastatura sa Gateron prekidačima', 13990.00, 'Periferije'],
        ['HyperX Cloud III Slušalice', 'Gejming slušalice sa mikrofonom i prostornim zvukom', 11490.00, 'Periferije'],
        ['Corsair RM850e 850W Gold', 'Potpuno modularno 80+ Gold ATX 3.0 napajanje', 16490.00, 'Napajanja'],
    ];

    $findProductStmt = $pdo->prepare("SELECT id, price FROM products WHERE name = ?");
    $insertProductStmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_path) VALUES (?, ?, ?, ?, '')");

    $products = [];
    $createdProducts = 0;

    foreach ($sampleProducts as [$name, $description, $price, $category]) {
        $findProductStmt->execute([$name]);
        $existing = $findProductStmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            $products[] = [
                'id' => (int)$existing['id'],
                'price' => (float)$existing['price'],
            ];
        } else {
            $insertProductStmt->execute([$name, $description, $price, $category]);
            $products[] = [
                'id' => (int)$pdo->lastInsertId(),
                'price' => (float)$price,
            ];
            $createdProducts++;
        }
    }

    // 3. Test porudžbine raspoređene u poslednjih 60 dana
    $existingOrdersCount = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $createdOrders = 0;
    $createdOrderItems = 0;

    if ($existingOrdersCount < 5 || $force) {
        // Težinska raspodela kupaca kako bi se jasno izdvojili Top 5 kupci
        $weightedUsers = [
            $userIds[0], $userIds[0], $userIds[0], $userIds[0], // marko_p
            $userIds[1], $userIds[1], $userIds[1], $userIds[1], // jelena_s
            $userIds[2], $userIds[2], $userIds[2],              // nikola_m
            $userIds[3], $userIds[3], $userIds[3],              // ana_k
            $userIds[4], $userIds[4],                           // stefan_d
            $userIds[5], $userIds[5],                           // milica_j
            $userIds[6],                                        // luka_v
            $userIds[7],                                        // jovana_t
        ];

        // Težinska raspodela proizvoda za jasnije najprodavanije proizvode
        $weightedProductIndexes = [
            4, 4, 4, 4, // Kingston RAM
            8, 8, 8, 8, // Logitech miš
            5, 5, 5,    // Samsung SSD
            6, 6, 6,    // WD SSD
            0, 0, 0,    // Ryzen 7
            2, 2,       // RTX 4070 Super
            7, 7,       // LG Monitor
            9, 9,       // Keychron tastatura
            10, 10,     // HyperX slušalice
            1,          // Intel i5
            3,          // RX 7800 XT
            11,         // Corsair napajanje
        ];

        $insertOrderStmt = $pdo->prepare("INSERT INTO orders (user_id, created_at) VALUES (?, ?)");
        $insertOrderItemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        $ordersToCreate = 45;

        for ($i = 0; $i < $ordersToCreate; $i++) {
            $userId = $weightedUsers[array_rand($weightedUsers)];
            $daysAgo = rand(0, 59);
            $hour = rand(9, 21);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            $orderDate = date('Y-m-d', strtotime("-{$daysAgo} days")) . sprintf(' %02d:%02d:%02d', $hour, $minute, $second);

            $insertOrderStmt->execute([$userId, $orderDate]);
            $orderId = (int)$pdo->lastInsertId();
            $createdOrders++;

            $itemsInOrder = rand(1, 3);
            $usedProductIds = [];

            for ($j = 0; $j < $itemsInOrder; $j++) {
                $prodIndex = $weightedProductIndexes[array_rand($weightedProductIndexes)];
                $product = $products[$prodIndex];

                if (in_array($product['id'], $usedProductIds, true)) {
                    continue;
                }
                $usedProductIds[] = $product['id'];

                $quantity = rand(1, 3);
                $insertOrderItemStmt->execute([
                    $orderId,
                    $product['id'],
                    $quantity,
                    $product['price'],
                ]);
                $createdOrderItems++;
            }
        }
    }

    $pdo->commit();

    $summaryMsg = "Seed uspešno završen! Dodato korisnika: {$createdUsers}, proizvoda: {$createdProducts}, porudžbina: {$createdOrders}, stavki porudžbina: {$createdOrderItems}.";
    seedOutput($summaryMsg, $isCli);
    seedOutput("Test lozinka za sve generisane kupce: password123", $isCli);

    if (!$isCli) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => $summaryMsg
        ];
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
            header('Location: index.php?page=admin_reports');
        } else {
            header('Location: index.php?page=login');
        }
        exit;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $errorMsg = "Greška prilikom seedovanja baze: " . $e->getMessage();
    seedOutput($errorMsg, $isCli);

    if (!$isCli) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => $errorMsg
        ];
        header('Location: index.php?page=home');
        exit;
    }
    exit(1);
}
