    </main>

    <!-- Bootstrap 5 JS Bundle (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js (CDN) - hanya dimuat di halaman dashboard -->
    <?php if (($currentPage ?? '') === 'dashboard'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php endif; ?>

    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>

    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>

