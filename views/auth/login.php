<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/alert.php'; ?>

<h2>Prijava</h2>

<form action="index.php?page=login" method="POST" class="mt-4" style="max-width: 400px;">
    <div class="mb-3">
        <label class="form-label">Korisničko ime</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Lozinka</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" name="login" class="btn btn-success">Prijavi se</button>
</form>

<?php include 'views/layout/footer.php'; ?>
