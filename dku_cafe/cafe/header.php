<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DKU Smart Cafe</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/html5-qrcode.min.js"></script>
    <script src="js/qrcode.min.js"></script>
</head>
<body>
<div class="container">
    <header>
        <div class="logo">
            <h1><?= __('welcome') ?></h1>
        </div>
        <div class="lang-switcher">
            <a href="?lang=en"><?= __('english') ?></a> | <a href="?lang=am"><?= __('amharic') ?></a>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="index.php"><?= __('home') ?></a></li>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="login.php"><?= __('login') ?></a></li>
                <li><a href="register.php"><?= __('register') ?></a></li>
            <?php else: ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="dashboard.php"><?= __('dashboard') ?></a></li>
                <?php else: ?>
                    <li><a href="my_qr.php"><?= __('my_qr') ?></a></li>
                <?php endif; ?>
                <li><a href="#" onclick="showLogoutModal(); return false;"><?= __('logout') ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
