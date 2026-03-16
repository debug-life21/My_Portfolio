<?php
/**
 * Manage Daily Menu
 * Allows Admins to set the menu for today's sessions
 */
require_once __DIR__ . '/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$today = date('Y-m-d');
$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['menu'] as $session_id => $items) {
        $items = trim($items);
        $image_url = trim($_POST['image_url'][$session_id] ?? '');
        
        // Always try to update if either items or image is present
        if (!empty($items) || !empty($image_url)) {
            // Check if menu exists for this session today
            $stmt = $pdo->prepare("SELECT id FROM daily_menu WHERE meal_session_id = ? AND menu_date = ?");
            $stmt->execute([$session_id, $today]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update
                $update = $pdo->prepare("UPDATE daily_menu SET menu_items = ?, menu_image_url = ? WHERE id = ?");
                $update->execute([$items, $image_url, $existing['id']]);
            } else {
                // Insert
                $insert = $pdo->prepare("INSERT INTO daily_menu (meal_session_id, menu_items, menu_date, menu_image_url) VALUES (?, ?, ?, ?)");
                $insert->execute([$session_id, $items, $today, $image_url]);
            }
        }
    }
    $message = "Menu updated successfully!";
}

// Fetch Sessions
$sessions = $pdo->query("SELECT * FROM meal_sessions ORDER BY start_time")->fetchAll();

// Fetch Existing Menu for Today
$menus = [];
$images = [];
$stmt = $pdo->prepare("SELECT * FROM daily_menu WHERE menu_date = ?");
$stmt->execute([$today]);
while ($row = $stmt->fetch()) {
    $menus[$row['meal_session_id']] = $row['menu_items'];
    $images[$row['meal_session_id']] = $row['menu_image_url'];
}
?>

<?php include __DIR__ . '/header.php'; ?>

<div class="container" style="max-width: 800px; margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Manage Today's Menu (<?= $today ?>)</h2>
        <a href="dashboard.php" class="btn" style="background-color: #6c757d;">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="manage_menu.php">
        <?php foreach ($sessions as $session): ?>
            <div class="card" style="margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <h3><?= $session['session_name'] ?></h3>
                <p style="color: #666; font-size: 0.9rem;">
                    Time: <?= $session['start_time'] ?> - <?= $session['end_time'] ?>
                </p>
                <div class="form-group">
                    <label>Menu Items (comma separated or new lines):</label>
                    <textarea name="menu[<?= $session['id'] ?>]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="e.g. Rice, Bread, Salad"><?= htmlspecialchars($menus[$session['id']] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Food Image URL (Optional):</label>
                    <input type="text" name="image_url[<?= $session['id'] ?>]" value="<?= htmlspecialchars($images[$session['id']] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="https://example.com/food.jpg">
                    <p style="font-size: 0.8rem; color: #888; margin-top: 5px;">Paste a link to an image of the meal.</p>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="form-buttons">
            <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem;">Save Menu</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>