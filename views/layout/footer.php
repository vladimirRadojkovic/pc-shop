</div> <!-- zatvaranje .container -->
<footer class="mt-auto bg-dark text-white py-3">
    <div class="container text-center">
        <p class="mb-0">IT Prodavnica &copy; <?= date('Y') ?></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 5000);
</script>