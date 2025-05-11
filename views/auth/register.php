<?php include 'views/layout/header.php'; ?>

<h2>Registracija korisnika</h2>

<form action="index.php?page=register" method="POST" class="mt-4" style="max-width: 400px;">
    <div class="mb-3">
        <label class="form-label">Korisničko ime</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Lozinka</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Ponovi lozinku</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>

    <button type="submit" name="register" class="btn btn-primary">Registruj se</button>
</form>

<?php include 'views/layout/footer.php'; ?>
