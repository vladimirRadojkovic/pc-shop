<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>IT Prodavnica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/pc-shop/index.php">IT Prodavnica</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Zdravo, <?= htmlspecialchars($_SESSION['user']['username']) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pc-shop/index.php?page=logout">Odjava</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/pc-shop/index.php?page=login">Prijava</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pc-shop/index.php?page=register">Registracija</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
