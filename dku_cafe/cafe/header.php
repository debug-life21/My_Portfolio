<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DKU Smart Cafe</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/html5-qrcode.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        // Notification functionality
        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notification-dropdown');
            const btn = event.target.closest('.notification-btn');
            if (!btn && dropdown) {
                dropdown.style.display = 'none';
            }
        });

        // Load notifications (mock data for now)
        function loadNotifications() {
            const notifications = [
                {
                    title: 'Meal Session Starting',
                    message: 'Breakfast session starts in 30 minutes',
                    time: '5 min ago',
                    unread: true
                },
                {
                    title: 'Menu Updated',
                    message: 'Today\'s lunch menu has been updated',
                    time: '1 hour ago',
                    unread: false
                }
            ];

            const list = document.getElementById('notification-list');
            const count = document.getElementById('notification-count');
            const badge = document.querySelector('.notification-badge');

            if (notifications.length > 0) {
                list.innerHTML = notifications.map(n => `
                    <div class="notification-item ${n.unread ? 'unread' : ''}">
                        <div class="title">${n.title}</div>
                        <div class="message">${n.message}</div>
                        <div class="time">${n.time}</div>
                    </div>
                `).join('');

                const unreadCount = notifications.filter(n => n.unread).length;
                if (unreadCount > 0) {
                    count.textContent = unreadCount;
                    badge.style.display = 'block';
                }
            }
        }

        // Load notifications on page load
        if (document.querySelector('.notifications')) {
            loadNotifications();
        }

        // Theme toggle
        function toggleTheme() {
            const html = document.documentElement;
            const theme = html.getAttribute('data-theme');
            const newTheme = theme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const theme = document.documentElement.getAttribute('data-theme');
            const toggleBtn = document.querySelector('.theme-toggle');
            toggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon();
    </script>
</head>
<body>
<div class="container">
    <header>
        <div class="logo">
            <h1><?= __('welcome') ?></h1>
        </div>
        <div class="header-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        🔔 <span class="notification-badge" id="notification-count">0</span>
                    </button>
                    <div class="notification-dropdown" id="notification-dropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                        </div>
                        <div class="notification-list" id="notification-list">
                            <p class="no-notifications">No new notifications</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <button class="theme-toggle" onclick="toggleTheme()">🌙</button>
            <div class="lang-switcher">
                <a href="?lang=en"><?= __('english') ?></a> | <a href="?lang=am"><?= __('amharic') ?></a>
            </div>
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
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                    <li><a href="my_qr.php"><?= __('my_qr') ?></a></li>
                <?php endif; ?>
                <li><a href="#" onclick="showLogoutModal(); return false;"><?= __('logout') ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
