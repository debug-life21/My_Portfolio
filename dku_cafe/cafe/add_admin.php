<?php
/**
 * Add Admin Script for DKU Smart Cafe
 * Allows existing Admins to add new Admins
 */
require_once __DIR__ . '/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Initialize errors array
$errors = []; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $national_id = $_POST['national_id'];
    $role = 'admin'; // Forced role

    // 1. Validation: Name should only contain letters
    if (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors[] = "Full name must contain only letters and spaces.";
    }

    // 2. Validation: Password match
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match!";
    }

    // 3. Validation: Check if email or national ID already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR national_id = ?");
    $check->execute([$email, $national_id]);
    if ($check->fetch()) {
        $errors[] = "Email or National ID is already registered.";
    }

    // If no errors, save to database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (fullname, email, password, phone, national_id, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$fullname, $email, $hashed_password, $phone, $national_id, $role])) {
            // Success: Redirect to dashboard with message
            header('Location: dashboard.php?msg=admin_added');
            exit;
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<?php include __DIR__ . '/header.php'; ?>

<div class="form-container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Add New Admin</h2>
        <a href="dashboard.php" class="btn" style="background-color: #6c757d;">Back to Dashboard</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-box" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php foreach ($errors as $error): ?>
                <p style="margin: 0;">⚠️ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_admin.php">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="fullname" required value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
        </div>
        
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" id="admin_password" required>
        </div>

        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" id="admin_confirm" required>
            <label style="font-size: 0.9em; display: inline-flex; align-items: center; margin-top: 5px; cursor: pointer;">
                <input type="checkbox" onclick="toggleAdminPasswords()" style="width: auto; margin-right: 5px;"> Show Passwords
            </label>
        </div>

        <script>
        function toggleAdminPasswords() {
            var p = document.getElementById("admin_password");
            var c = document.getElementById("admin_confirm");
            if (p.type === "password") {
                p.type = "text";
                c.type = "text";
            } else {
                p.type = "password";
                c.type = "password";
            }
        }
        </script>

        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone" required placeholder="09..." value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
        </div>

        <div class="form-group">
            <label>National ID / Staff ID:</label>
            <input type="text" name="national_id" required value="<?= isset($_POST['national_id']) ? htmlspecialchars($_POST['national_id']) : '' ?>">
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn">Create Admin</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>