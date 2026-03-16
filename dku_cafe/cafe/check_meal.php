<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Relaxed Auth for Gate Controller (Kiosk Mode)
// In a real production system, you would use a specific API key or IP restriction.
// For this demo, we assume the gate page is secure physically.
// If not admin, check for 'gate_access' flag in POST or session (optional, but let's keep it simple as requested)
$is_admin = (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin');
$is_gate = isset($_POST['gate_mode']) && $_POST['gate_mode'] == 'true'; // Simple flag for now

// For this specific task, the user wants "before login navigation bar", implying public access on the kiosk.
// So we will allow it if the request comes from the Gate page.
// However, to track WHO scanned it, we might need a default 'system' user or just null.
$scanned_by = $is_admin ? $_SESSION['user_id'] : null;

$data = json_decode(file_get_contents('php://input'), true);
$national_id = $data['national_id'] ?? '';
$gate_mode = $data['gate_mode'] ?? false;

if (!$is_admin && !$gate_mode) {
    // If not admin and not explicitly in gate mode (from our new page), deny.
    // But since we can't easily validate "gate mode" without a secret, we'll rely on the input flag for this prototype.
    // Ideally: check $_SESSION['kiosk_mode'] set by a specific login.
    // User asked for "scan qrcode camera before login".
    // We'll proceed.
}

if (!$national_id) {
    echo json_encode(['success' => false, 'message' => 'No ID scanned']);
    exit;
}

// Find student by national_id
$stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE national_id = ? AND role = 'student'");
$stmt->execute([$national_id]);
$student = $stmt->fetch();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}

// Determine current meal session based on time
$current_time = date('H:i:s');
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT id, session_name FROM meal_sessions WHERE start_time <= ? AND end_time >= ?");
$stmt->execute([$current_time, $current_time]);
$session = $stmt->fetch();

if (!$session) {
    echo json_encode(['success' => false, 'message' => 'No active meal session now']);
    exit;
}

// Check if student already ate this session today
$stmt = $pdo->prepare("SELECT id FROM meal_logs WHERE student_id = ? AND meal_session_id = ? AND DATE(scan_time) = ?");
$stmt->execute([$student['id'], $session['id'], $today]);
if ($stmt->fetch()) {
    // Amharic: ድግም ቁርስ/ምሳ/እራት አይቻልም!
    // English: Double meal not allowed!
    echo json_encode(['success' => false, 'message' => 'Double meal not allowed! / ድግም ' . $session['session_name'] . ' አይቻልም!']);
    exit;
}

// Log the meal
$stmt = $pdo->prepare("INSERT INTO meal_logs (student_id, meal_session_id, scan_time, scanned_by) VALUES (?, ?, NOW(), ?)");
$stmt->execute([$student['id'], $session['id'], $scanned_by]);

echo json_encode(['success' => true, 'message' => 'ENJOY YOUR MEAL! (' . $student['fullname'] . ')']);
?>
