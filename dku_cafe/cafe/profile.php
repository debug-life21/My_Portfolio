<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get meal history
$stmt = $pdo->prepare("
    SELECT ml.scan_time, ms.session_name, ms.start_time, ms.end_time, DATE(ml.scan_time) as scan_date
    FROM meal_logs ml
    JOIN meal_sessions ms ON ml.meal_session_id = ms.id
    WHERE ml.student_id = ?
    ORDER BY ml.scan_time DESC
    LIMIT 20
");
$stmt->execute([$user_id]);
$meal_history = $stmt->fetchAll();

// Get total meals this month
$this_month = date('Y-m');
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total_meals
    FROM meal_logs
    WHERE student_id = ? AND DATE_FORMAT(scan_time, '%Y-%m') = ?
");
$stmt->execute([$user_id, $this_month]);
$monthly_stats = $stmt->fetch();

include __DIR__ . '/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <div class="avatar-circle">
                <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
            </div>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['fullname']) ?></h1>
            <p class="profile-role">Student</p>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <h3>Total Meals This Month</h3>
            <div class="stat-number"><?= $monthly_stats['total_meals'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <h3>National ID</h3>
            <div class="stat-text"><?= htmlspecialchars($user['national_id']) ?></div>
        </div>
        <div class="stat-card">
            <h3>Phone</h3>
            <div class="stat-text"><?= htmlspecialchars($user['phone']) ?></div>
        </div>
    </div>

    <div class="meal-history">
        <h2>Recent Meal History</h2>
        <?php if (count($meal_history) > 0): ?>
            <div class="history-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meal_history as $meal): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($meal['scan_date'])) ?></td>
                            <td><?= htmlspecialchars($meal['session_name']) ?></td>
                            <td><?= date('H:i', strtotime($meal['scan_time'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-history">No meal history yet. Visit the cafe to get started!</p>
        <?php endif; ?>
    </div>

    <div class="profile-actions">
        <a href="my_qr.php" class="btn">View My QR Code</a>
        <a href="logout.php" class="btn danger">Logout</a>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    display: flex;
    align-items: center;
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 2rem;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    margin-right: 1.5rem;
}

.profile-info h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.profile-role {
    color: var(--secondary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.profile-email {
    color: var(--text-light);
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    text-align: center;
}

.stat-card h3 {
    color: var(--text-light);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-text {
    font-size: 1.2rem;
    color: var(--text-color);
    font-weight: 600;
}

.meal-history {
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 2rem;
}

.meal-history h2 {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.history-table table {
    width: 100%;
    border-collapse: collapse;
}

.history-table th,
.history-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.history-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: var(--primary-color);
}

.history-table tr:hover {
    background: #f8f9fa;
}

.no-history {
    text-align: center;
    color: var(--text-light);
    font-style: italic;
    padding: 2rem;
}

.profile-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }

    .avatar-circle {
        margin-right: 0;
        margin-bottom: 1rem;
    }

    .profile-stats {
        grid-template-columns: 1fr;
    }

    .profile-actions {
        flex-direction: column;
    }
}
</style>

<?php include __DIR__ . '/footer.php'; ?>