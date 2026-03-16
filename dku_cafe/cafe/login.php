<?php
/**
 * Login System for DKU Smart Cafe
 * Handles user authentication and session management
 */
require_once __DIR__ . '/config.php';

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student'; // Get the selected role

    // Search for the user in the database by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify user existence, password match, AND role match
    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] !== $role) {
             $error = "Access denied. You are not registered as a " . ucfirst($role) . ".";
        } else {
            // Store user details in session variables for global access
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: my_qr.php');
            }
            exit;
        }
    } else {
        // Set error message for invalid credentials
        $error = "Invalid email or password.";
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="form-container">
    <h2><?= __('login_title') ?></h2>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
            <?= __('register_success') ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="error" style="color: red; margin-bottom: 15px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email"><?= __('email') ?>:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password"><?= __('password') ?>:</label>
            <input type="password" id="password" name="password" required>
            <label style="font-size: 0.9em; display: inline-flex; align-items: center; margin-top: 5px; cursor: pointer;">
                <input type="checkbox" onclick="togglePassword()" style="width: auto; margin-right: 5px;"> Show Password
            </label>
        </div>
        
        <script>
        function togglePassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
        </script>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label><?= __('role') ?>:</label>
            <div style="display: flex; gap: 20px; margin-top: 5px;">
                <label style="cursor: pointer;">
                    <input type="radio" name="role" value="student" checked> <?= __('student') ?>
                </label>
                <label style="cursor: pointer;">
                    <input type="radio" name="role" value="admin"> <?= __('admin') ?>
                </label>
            </div>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn"><?= __('login') ?></button>
        </div>
    </form>
    <p style="margin-top: 15px;">
        <?= __('no_account') ?> <a href="register.php"><?= __('register') ?></a>
    </p>
</div>

<?php include __DIR__ . '/footer.php'; ?>