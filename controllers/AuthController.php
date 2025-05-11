<?php
require_once 'config/config.php';

function registerUser() {
    global $pdo;

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Lozinke se ne poklapaju.'
        ];
        return;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Korisničko ime je zauzeto.'
        ];
        return;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$username, $hashed]);
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Registracija uspešna. Možete se prijaviti.'
    ];
}

function loginUser() {
    global $pdo;

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'admin') {
            header('Location: /pc-shop/views/admin/dashboard.php');
            exit;
        } else {
            header('Location: /pc-shop/index.php');
            exit;
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Pogrešan username ili lozinka.'
        ];
    }
}