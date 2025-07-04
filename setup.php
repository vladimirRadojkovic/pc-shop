<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';

function redirect($page = 'home') {
    header("Location: index.php?page=$page");
    exit();
}

function setAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

$is_standalone = (basename($_SERVER['SCRIPT_FILENAME']) === 'setup.php');

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
";

try {
    $pdo->exec($sql);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch();
    
    if (!$adminExists) {
        $adminPassword = password_hash('admin', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')");
        $insertAdmin->execute([$adminPassword]);
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Admin user created successfully.'
        ];
    }
    
    if ($is_standalone) {
        header('Location: index.php?page=home');
        exit();
    }
    
} catch (PDOException $e) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => "Database error: " . $e->getMessage()
    ];
    
    if ($is_standalone) {
        header('Location: index.php?page=home');
        exit();
    }
}