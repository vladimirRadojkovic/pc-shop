<?php if (isset($_SESSION['alert'])): ?>
    <div class="alert alert-<?= $_SESSION['alert']['type']; ?> alert-dismissible fade show text-center"
         style="position: fixed; top: 60px; left: 50%; transform: translateX(-50%); z-index: 1000; width: 80%;">
        <?= $_SESSION['alert']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zatvori"></button>
    </div>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>
