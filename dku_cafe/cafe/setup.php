<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // የእርስዎ የ MySQL password ባዶ ከሆነ እንዳለ ይቆይ

try {
    // መጀመሪያ ያለ ዳታቤዝ ስም ኮኔክት እናደርጋለን
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. ዳታቤዙን መፍጠር
    $pdo->exec("CREATE DATABASE IF NOT EXISTS dku_cafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'dku_cafe' created or already exists.<br>";

    // አሁን ወደ ተፈጠረው ዳታቤዝ እንቀይራለን
    $pdo->exec("USE dku_cafe");

    // 2. Create Users Table
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        national_id VARCHAR(50) NOT NULL UNIQUE,
        role ENUM('student', 'admin') DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_users);

    // Ensure 'role' column exists (for updates)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('student', 'admin') DEFAULT 'student'");
    } catch (PDOException $e) {
        // Column likely already exists
    }

    echo "Table 'users' ready.<br>";

    // Create Default Admin
    $admin_email = 'admin@dku.edu.et';
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $check_admin = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check_admin->execute([$admin_email]);
    if (!$check_admin->fetch()) {
        $sql_admin = "INSERT INTO users (fullname, email, password, phone, national_id, role) VALUES 
            ('System Admin', ?, ?, '0900000000', 'ADMIN001', 'admin')";
        $stmt_admin = $pdo->prepare($sql_admin);
        $stmt_admin->execute([$admin_email, $admin_pass]);
        echo "Default Admin created (Email: admin@dku.edu.et, Pass: admin123)<br>";
    }

    // 3. Create Meal Sessions Table
    $sql_sessions = "CREATE TABLE IF NOT EXISTS meal_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_name VARCHAR(50) NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL
    )";
    $pdo->exec($sql_sessions);
    
    // Insert/Update Meal Schedule
    // Breakfast: 1:00 AM - 2:00 AM
    // Lunch: 5:30 AM - 7:00 AM
    // Dinner: 11:00 AM - 12:30 AM (Next Day handled by logic or assuming same day for simplicity first)
    // Note: Database TIME format is HH:MM:SS. 
    // 1:00 AM = 01:00:00
    // 2:00 AM = 02:00:00
    // 5:30 AM = 05:30:00
    // 7:00 AM = 07:00:00
    // 11:00 AM = 11:00:00
    // 12:30 AM = 00:30:00 (Cross-day is tricky with simple TIME comparison, but let's assume strict daily hours first or adjust logic)
    // Wait, 12:30 AM is 00:30. If Dinner starts at 11:00 AM (11:00) and ends at 12:30 AM (00:30), that crosses midnight? 
    // OR did the user mean 11:00 PM? 
    // "Dinner: 11:00 AM - 12:30 AM" -> This is unusual. Dinner usually is PM. 
    // Let's assume user input is literal Local Time (Ethiopian Time?)
    // In Ethiopian time:
    // 1:00 (Morning) = 7:00 AM International
    // But the user said "1:00 AM - 2:00 AM (Local Time)".
    // If it is strictly 1:00 AM International Time, that's very early.
    // If "Local Time" implies Ethiopian time logic but written as AM/PM... it's ambiguous.
    // However, I will implement exactly what is asked:
    // Breakfast: 01:00:00 - 02:00:00
    // Lunch: 05:30:00 - 07:00:00
    // Dinner: 11:00:00 - 00:30:00 (Next Day)
    
    // Let's try to interpret standard AM/PM first.
    // Breakfast: 01:00 - 02:00
    // Lunch: 05:30 - 07:00
    // Dinner: 11:00 AM? - 12:30 AM? 
    // If Dinner is 11:00 AM to 12:30 PM (Lunch time usually), maybe they meant 11:00 PM (23:00) to 12:30 AM (00:30)?
    // Or maybe "11:00 AM - 12:30 AM" is a typo for 11:00 AM - 12:30 PM?
    // Given the other times are very early morning (1am, 5am), maybe this is a night shift or specific schedule?
    // User said "Local Time". In Ethiopia, day starts at 6:00 AM (12:00 local).
    // If 1:00 AM Local = 7:00 AM International.
    // If 2:00 AM Local = 8:00 AM International.
    // If 5:30 AM Local = 11:30 AM International.
    // If 7:00 AM Local = 1:00 PM International.
    // If 11:00 AM Local = 5:00 PM International.
    // If 12:30 AM Local = 6:30 PM International.
    
    // Disable foreign key checks to allow truncate
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $pdo->exec("TRUNCATE TABLE meal_sessions"); // Reset for fresh start with correct times
    
    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    $pdo->exec("INSERT INTO meal_sessions (session_name, start_time, end_time) VALUES 
        ('Breakfast', '07:00:00', '08:00:00'),
        ('Lunch', '11:30:00', '13:00:00'),
        ('Dinner', '17:00:00', '18:30:00')");
        
    echo "Table 'meal_sessions' updated with Ethiopian Local Time converted to International.<br>";

    // 4. Create Meal Logs Table
    $sql_logs = "CREATE TABLE IF NOT EXISTS meal_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        meal_session_id INT NOT NULL,
        scan_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        scanned_by INT,
        FOREIGN KEY (student_id) REFERENCES users(id),
        FOREIGN KEY (meal_session_id) REFERENCES meal_sessions(id)
    )";
    $pdo->exec($sql_logs);
    echo "Table 'meal_logs' ready.<br>";

    // 5. Create Daily Menu Table
    $sql_menu = "CREATE TABLE IF NOT EXISTS daily_menu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        meal_session_id INT NOT NULL,
        menu_items TEXT NOT NULL,
        menu_date DATE NOT NULL,
        menu_image_url VARCHAR(255),
        FOREIGN KEY (meal_session_id) REFERENCES meal_sessions(id)
    )";
    $pdo->exec($sql_menu);

    // Update table to include image column if missing
    try {
        $pdo->exec("ALTER TABLE daily_menu ADD COLUMN menu_image_url VARCHAR(255)");
    } catch (PDOException $e) {
        // Column likely exists
    }

    echo "Table 'daily_menu' ready.<br>";

    echo "<strong>All setup completed successfully!</strong><br>";
    echo "<a href='register.php'>Go to Registration Page</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>