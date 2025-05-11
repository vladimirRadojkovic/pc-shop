</div> <!-- zatvaranje .container -->
<footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">&copy; <?= date("Y") ?> IT Prodavnica. Sva prava zadržana.</p>
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