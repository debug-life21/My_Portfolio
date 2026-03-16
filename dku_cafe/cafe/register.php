<?php
/**
 * Registration Script for DKU Smart Cafe
 * Full validation for Ethiopia (Ethio Telecom/Safaricom)
 */
require_once __DIR__ . '/config.php';

$errors = []; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $national_id = trim($_POST['national_id']);
    $role = 'student'; // Default role

    // 1. Name: No numbers, letters/spaces only, min 3 chars
    if (!preg_match("/^[a-zA-Z\s]{3,}$/", $fullname)) {
        $errors[] = "Full name must be at least 3 characters and contain only letters.";
    }

    // 2. Email Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // 3. Ethiopian Phone: 09 (Ethio Tel) or 07 (Safaricom)
    $phone_pattern = "/^(?:\+251|0)[79]\d{8}$/";
    if (!preg_match($phone_pattern, $phone)) {
        $errors[] = "Invalid phone number. Use 09... or 07...";
    }

    // 4. Password Match
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match!";
    }

    // 5. Database Check
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR national_id = ?");
    $check->execute([$email, $national_id]);
    if ($check->fetch()) {
        $errors[] = "Email or National ID is already registered.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (fullname, email, password, phone, national_id, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$fullname, $email, $hashed_password, $phone, $national_id, $role])) {
            header('Location: login.php?msg=success');
            exit;
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<?php include __DIR__ . '/header.php'; ?>

<div class="form-container" style="max-width: 500px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
    <h2>User Registration</h2>

    <?php if (!empty($errors)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?php foreach ($errors as $error): ?>
                <p style="margin: 0;">⚠️ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" id="regForm">
        <div style="margin-bottom: 15px;">
            <label>Full Name:</label>
            <input type="text" name="fullname" required style="width:100%; padding:8px;" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Email:</label>
            <input type="email" name="email" required style="width:100%; padding:8px;" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Password:</label>
            <input type="password" name="password" id="p1" required style="width:100%; padding:8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" id="p2" required style="width:100%; padding:8px;">
            

        <div style="margin-bottom: 15px;">
            <label>Phone Number:</label>
            <input type="text" name="phone" placeholder="09... or 07..." required style="width:100%; padding:8px;" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
        </div>

        <div style="margin-bottom: 20px;">
            <label>National ID:</label>
            <input type="text" name="national_id" required style="width:100%; padding:8px;" value="<?= isset($_POST['national_id']) ? htmlspecialchars($_POST['national_id']) : '' ?>">
        </div>

        <div class="form-buttons">
            <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Create Account</button>
            <button type="button" onclick="clearForm()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Clear</button>
        </div>
    </form>
</div>

<script>
function togglePass() {
    var p1 = document.getElementById("p1");
    var p2 = document.getElementById("p2");
    p1.type = p1.type === "password" ? "text" : "password";
    p2.type = p2.type === "password" ? "text" : "password";
}

// Custom clear function to handle PHP-sticky values
function clearForm() {
    if(confirm("Are you sure you want to clear the form?")) {
        window.location.href = "register.php";
    }
}
</script>

<?php include __DIR__ . '/footer.php'; ?>