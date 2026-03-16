<?php
session_start();

$host = 'localhost';
$db   = 'dku_cafe';
$user = 'root';          // your MySQL username
$pass = '';              // your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Set Timezone to Ethiopia
date_default_timezone_set('Africa/Addis_Ababa');

// Language setup (default English)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'am'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Translations
$lang = [
    'en' => [
        'welcome' => 'Welcome to DKU Cafe',
        'login_title' => 'Sign in to your CafeConnect account',
        'email' => 'Email',
        'password' => 'Password',
        'login' => 'Login',
        'no_account' => "Don't have an account?",
        'register' => 'Register',
        'fullname' => 'Full Name',
        'confirm_password' => 'Confirm Password',
        'phone' => 'Phone Number',
        'national_id' => 'National ID (FAN)',
        'role' => 'Role',
        'student' => 'Student',
        'admin' => 'Administrator',
        'submit' => 'Submit',
        'reset' => 'Reset',
        'dashboard' => 'Dashboard',
        'scan_qr' => 'Scan QR',
        'my_qr' => 'My QR Code',
        'logout' => 'Logout',
        'language' => 'Language',
        'amharic' => 'አማርኛ',
        'english' => 'English',
        'home' => 'Home',
        'smart_cafe' => 'Smart Café Management System',
        'description' => 'Efficient, fast, and secure meal verification for Debark University.',
        'all_rights' => 'All rights reserved.',
        'registered_students' => 'Registered Students',
        'view_students' => 'View Students',
        'welcome_user' => 'Welcome, %s',
        'name_letters_only' => 'Name must contain only letters and spaces.',
        'invalid_email' => 'Please enter a valid email address.',
        'password_requirements' => 'Password must be at least 8 chars, include a number and symbol.',
        'passwords_mismatch' => 'Passwords do not match.',
        'invalid_phone' => 'Phone must start with 09 or 07 and have 10 digits.',
        'national_id_short' => 'National ID must be at least 10 characters.',
        'email_or_id_exists' => 'Email or national ID already exists.',
        'no_students' => 'No students registered yet.',
        'register_success' => 'Registration successful! Please login.',
        'logout_confirm' => 'Are you sure you want to logout?',
        'cancel' => 'Cancel',
        'double_meal_error' => 'Double meal not allowed!',
        'enjoy_meal' => 'ENJOY YOUR MEAL!',
        // add more as needed
    ],
    'am' => [
        'welcome' => 'እንኳን ወደ DKU ካፌ በደህና መጡ!!!',
        'login_title' => 'ወደ ካፌኮኔክት መለያዎ ይግቡ',
        'email' => 'ኢሜይል',
        'password' => 'የይለፍ ቃል',
        'login' => 'ግባ',
        'no_account' => 'መለያ የለዎትም?',
        'register' => 'መዝገብ',
        'fullname' => 'ሙሉ ስም',
        'confirm_password' => 'የይለፍ ቃል አረጋግጥ',
        'phone' => 'ስልክ ቁጥር',
        'national_id' => 'ብሔራዊ መታወቂያ (ፋን)',
        'role' => 'ሚና',
        'student' => 'ተማሪ',
        'admin' => 'አስተዳዳሪ',
        'submit' => 'አስገባ',
        'reset' => 'አጽዳ',
        'dashboard' => 'ዳሽቦርድ',
        'scan_qr' => 'QR ቅኝት',
        'my_qr' => 'የእኔ QR ኮድ',
        'logout' => 'ውጣ',
        'language' => 'ቋንቋ',
        'amharic' => 'Amharic',
        'english' => 'English',
        'register_success' => 'ምዝገባው ተሳክቷል! እባክዎ ይግቡ።',
        'logout_confirm' => 'እርግጠኛ ነዎት መውጣት ይፈልጋሉ?',
        'cancel' => 'ሰርዝ',
        'double_meal_error' => 'ድግም ቁርስ/ምሳ/እራት አይቻልም!',
        'enjoy_meal' => 'መልካም ምግብ!',
    ]
];

function __($key) {
    global $lang;
    $lang_code = $_SESSION['lang'];
    return $lang[$lang_code][$key] ?? $key;
}
?>
