<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT national_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$national_id = $user['national_id'];
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="qr-display">
    <h2><?= __('my_qr') ?></h2>
    <p><?= __('your_national_id') ?>: <?= htmlspecialchars($national_id) ?></p>
    <div id="qrcode"></div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "<?= $national_id ?>",
        width: 256,
        height: 256
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
