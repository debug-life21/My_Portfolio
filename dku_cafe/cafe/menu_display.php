<?php
/**
 * Menu Display Page
 * Shows the food photo and menu for the current session
 */
require_once __DIR__ . '/config.php';

// Determine current meal session based on time
$current_time = date('H:i:s');
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT id, session_name, start_time, end_time FROM meal_sessions WHERE start_time <= ? AND end_time >= ?");
$stmt->execute([$current_time, $current_time]);
$session = $stmt->fetch();

$menu_items = [];
$menu_image = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'; // Default placeholder

if ($session) {
    $stmt_menu = $pdo->prepare("SELECT menu_items, menu_image_url FROM daily_menu WHERE meal_session_id = ? AND menu_date = ?");
    $stmt_menu->execute([$session['id'], $today]);
    $menu_data = $stmt_menu->fetch();
    
    if ($menu_data) {
        if (!empty($menu_data['menu_items'])) {
            $menu_items = explode(',', $menu_data['menu_items']);
        }
        if (!empty($menu_data['menu_image_url'])) {
            $menu_image = $menu_data['menu_image_url'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Menu - DKU Cafe</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .menu-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            text-align: center;
        }
        .menu-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        .menu-content {
            padding: 40px;
        }
        .session-title {
            font-size: 2rem;
            color: #6f4e37;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .menu-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        .menu-list li {
            font-size: 1.5rem;
            padding: 10px;
            border-bottom: 1px solid #eee;
            color: #444;
        }
        .menu-list li:last-child {
            border-bottom: none;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 40px;
            background-color: #6f4e37;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2rem;
            transition: transform 0.2s;
        }
        .back-btn:hover {
            transform: scale(1.05);
            background-color: #5a3e2b;
        }
    </style>
</head>
<body>

<div class="menu-container">
    <img src="<?= htmlspecialchars($menu_image) ?>" alt="Today's Meal" class="menu-image">
    
    <div class="menu-content">
        <?php if ($session): ?>
            <h1 class="session-title"><?= htmlspecialchars($session['session_name']) ?> Menu</h1>
            <p style="color: #777; font-size: 1.1rem;"><?= date('l, F j, Y') ?></p>
            
            <?php if (!empty($menu_items)): ?>
                <ul class="menu-list">
                    <?php foreach ($menu_items as $item): ?>
                        <li><?= htmlspecialchars(trim($item)) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="font-size: 1.5rem; color: #999; margin: 40px 0;">Menu details not yet available.</p>
            <?php endif; ?>
            
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; font-weight: bold; margin-bottom: 20px;">
                ✅ Entry Granted. Enjoy your meal!
            </div>
            
        <?php else: ?>
            <h1 class="session-title">No Active Session</h1>
            <p>Please check back later.</p>
        <?php endif; ?>

        <a href="gate.php" class="back-btn">Return to Gate</a>
    </div>
</div>

<script>
    // Auto-redirect back to gate after 10 seconds
    setTimeout(function() {
        window.location.href = 'gate.php';
    }, 10000);
</script>

</body>
</html>