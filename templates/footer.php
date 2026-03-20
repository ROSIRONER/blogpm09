</main>
<script>window.APP_BASE_URL = <?= json_encode(appBaseUrl(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="<?= e(function_exists('assetUrl') ? assetUrl('assets/js/main.js') : appUrl('assets/js/main.js')) ?>"></script>
</body>
</html>
