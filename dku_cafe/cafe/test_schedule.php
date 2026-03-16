<?php
/**
 * Test Schedule Manager
 * Allows Admins to force-open meal sessions for testing purposes
 */
require_once __DIR__ . '/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$current_time = date('H:i:s');

if (isset($_POST['reset'])) {
    // Disable foreign key checks before truncate
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE meal_sessions");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Explicitly listing columns to ensure safety, although not strictly necessary if schema matches
    $pdo->exec("INSERT INTO meal_sessions (id, session_name, start_time, end_time) VALUES 
        (1, 'Breakfast', '07:00:00', '08:00:00'),
        (2, 'Lunch', '11:30:00', '13:00:00'),
        (3, 'Dinner', '17:00:00', '18:30:00')");
    $message = "Schedule reset to defaults.";
} elseif (isset($_POST['force_session'])) {
    $session_name = $_POST['session_name'];
    
    // Set start time to 1 hour ago and end time to 1 hour ahead
    $start = date('H:i:s', strtotime('-1 hour'));
    $end = date('H:i:s', strtotime('+1 hour'));
    
    // Update the specific session to be active NOW
    $stmt = $pdo->prepare("UPDATE meal_sessions SET start_time = ?, end_time = ? WHERE session_name = ?");
    $stmt->execute([$start, $end, $session_name]);
    
    $message = "$session_name is now ACTIVE (forced open from $start to $end).";
}

$sessions = $pdo->query("SELECT * FROM meal_sessions")->fetchAll();
?>

<?php include __DIR__ . '/header.php'; ?>

<div class="container" style="max-width: 800px; margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Test Schedule Manager</h2>
        <a href="dashboard.php" class="btn" style="background-color: #6c757d;">Back to Dashboard</a>
    </div>

    <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffeeba;">
        <strong></strong> This tool modifies the database schedule. Use for testing only.
        <br>Current Server Time: <strong><?= $current_time ?></strong>
    </div>

    <?php if ($message): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="card" style="margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h3>Current Schedule</h3>
        <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse; margin-top: 10px;">
            <tr>
                <th>Session</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
            <?php foreach ($sessions as $session): ?>
            <?php 
                $isActive = ($current_time >= $session['start_time'] && $current_time <= $session['end_time']);
            ?>
            <tr style="<?= $isActive ? 'background-color: #d4edda;' : '' ?>">
                <td><?= $session['session_name'] ?></td>
                <td><?= $session['start_time'] ?></td>
                <td><?= $session['end_time'] ?></td>
                <td><?= $isActive ? 'ACTIVE' : 'Inactive' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h3>Actions</h3>
        <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" name="force_session" value="1" class="btn" style="background-color: #ffc107; color: #000;">
                <input type="hidden" name="session_name" value="Breakfast">
                Force Open Breakfast
            </button>
        </form>
        <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
            <button type="submit" name="force_session" value="1" class="btn" style="background-color: #ffc107; color: #000;">
                <input type="hidden" name="session_name" value="Lunch">
                Force Open Lunch
            </button>
        </form>
        <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
            <button type="submit" name="force_session" value="1" class="btn" style="background-color: #ffc107; color: #000;">
                <input type="hidden" name="session_name" value="Dinner">
                Force Open Dinner
            </button>
        </form>
        
        <hr style="margin: 20px 0;">
        
        <form method="POST">
            <button type="submit" name="reset" class="btn" style="background-color: #dc3545;">Reset Schedule to Defaults</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>