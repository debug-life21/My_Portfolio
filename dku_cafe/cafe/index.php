<?php require_once __DIR__ . '/config.php'; ?>
<?php include __DIR__ . '/header.php'; ?>

<div class="welcome-box">
    <h2><?= __('smart_cafe') ?></h2>
    <p><?= __('description') ?></p>
    <a href="login.php" class="btn"><?= __('login') ?></a>
    <a href="register.php" class="btn"><?= __('register') ?></a>
    <br><br>
    <a href="gate.php" class="btn btn-gate">🚪 ENTER CAFE (SCAN QR)</a>
</div>

<?php include __DIR__ . '/footer.php'; ?>
