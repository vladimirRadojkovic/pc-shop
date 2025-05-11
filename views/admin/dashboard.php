<?php
require_once '../../config/config.php';
require_once '../layout/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>
<h2>Admin Panel</h2>
<hr>
<div class="list-group">
    <a href="index.php?page=add_product" class="list-group-item list-group-item-action">Dodaj novi proizvod</a>
    <a href="index.php?page=edit_product" class="list-group-item list-group-item-action">Izmeni postojeći proizvod</a>
    <a href="index.php?page=delete_product" class="list-group-item list-group-item-action">Obriši proizvod</a>
    <a href="index.php?page=orders" class="list-group-item list-group-item-action">Pregled porudžbina</a>
</div>
