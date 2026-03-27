<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];

// If student tries to access dashboard, redirect to My QR
if ($role !== 'admin') {
    header('Location: my_qr.php');
    exit;
}

// Fetch students for Admin Dashboard
$search = $_GET['search'] ?? '';
$query = "SELECT id, fullname, email, phone, national_id, created_at FROM users WHERE role = 'student'";
$params = [];

if ($search) {
    $query .= " AND (fullname LIKE ? OR email LIKE ? OR national_id LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2><?= __('dashboard') ?></h2>
            <p><?= __('welcome_user', $_SESSION['fullname']) ?> (<?= $role ?>)</p>
        </div>
        <div>
            <a href="manage_menu.php" class="btn" style="background-color: #007bff;">📋 Manage Menu</a>
            <a href="test_schedule.php" class="btn" style="background-color: #ffc107; color: #000;">⚙️ Test Schedule</a>
            <a href="add_admin.php" class="btn" style="background-color: #28a745;">+ Add New Admin</a>
        </div>
    </div>

    <div class="students-list">
        <div class="search-section">
            <form method="GET" action="dashboard.php" class="search-form">
                <input type="text" name="search" placeholder="Search students by name, email, or ID..." value="<?= htmlspecialchars($search) ?>" class="search-input">
                <button type="submit" class="btn">Search</button>
                <?php if ($search): ?>
                    <a href="dashboard.php" class="btn reset">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <h3><?= __('registered_students') ?> (<?= count($students) ?>)</h3>
        <?php if (count($students) > 0): ?>
            <div style="overflow-x:auto;">
                <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse; margin-top: 10px;">
                    <thead style="background-color: #f2f2f2;">
                        <tr>
                            <th>ID</th>
                            <th><?= __('fullname') ?></th>
                            <th><?= __('email') ?></th>
                            <th><?= __('phone') ?></th>
                            <th><?= __('national_id') ?></th>
                            <th><?= __('registered_on') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id']) ?></td>
                            <td><?= htmlspecialchars($student['fullname']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= htmlspecialchars($student['phone']) ?></td>
                            <td><?= htmlspecialchars($student['national_id']) ?></td>
                            <td><?= htmlspecialchars($student['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p><?= __('no_students') ?></p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
