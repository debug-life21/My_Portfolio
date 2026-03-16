<?php
/**
 * Gate Controller / Kiosk Page
 * Allows public/student scanning and FAN input without login
 */
require_once __DIR__ . '/config.php';

// Determine current meal session based on time
$current_time = date('H:i:s');
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT id, session_name, start_time, end_time FROM meal_sessions WHERE start_time <= ? AND end_time >= ?");
$stmt->execute([$current_time, $current_time]);
$session = $stmt->fetch();

// Fetch today's menu for the current session
$menu_display = "<li>Standard Meal</li><li>Vegetarian Option</li>"; // Default
if ($session) {
    $stmt_menu = $pdo->prepare("SELECT menu_items FROM daily_menu WHERE meal_session_id = ? AND menu_date = ?");
    $stmt_menu->execute([$session['id'], $today]);
    $menu_data = $stmt_menu->fetch();
    if ($menu_data) {
        $items = explode(',', $menu_data['menu_items']);
        $menu_display = "";
        foreach ($items as $item) {
            $menu_display .= "<li>" . htmlspecialchars(trim($item)) . "</li>";
        }
    }
}

$session_info = $session 
    ? "Current Session: " . $session['session_name'] . " (" . substr($session['start_time'], 0, 5) . " - " . substr($session['end_time'], 0, 5) . ")"
    : "No Active Meal Session";
$session_class = $session ? "status-active" : "status-inactive";

// Determine greeting based on time of day (just for UI nicety)
$hour = date('H');
if ($hour < 12) $greeting = "Good Morning!";
elseif ($hour < 18) $greeting = "Good Afternoon!";
else $greeting = "Good Evening!";

// Calculate Ethiopian Local Time (Habesha Time)
// Logic: 
// 06:00 International = 12:00 Local (Morning)
// 07:00 International = 01:00 Local
// Formula: ($hour + 6) % 12. If result is 0, make it 12.
$eth_hour = ($hour + 6) % 12;
if ($eth_hour == 0) $eth_hour = 12;
$eth_time_str = $eth_hour . ':' . date('i');
// Determine period (Morning/Afternoon/Evening/Night in local context)
$period = ($hour >= 6 && $hour < 18) ? "Day" : "Night"; // Simplified
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DKU Cafe - Gate Controller</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/html5-qrcode.min.js"></script>
    <style>
        body {
            background-image: none;
            background-color: #f4f4f4;
            display: block; /* Override flex from style.css if needed */
        }
        .gate-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 8px solid #00796b;
        }
        .gate-header {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .session-info {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        
        .gate-content {
            display: flex;
            width: 100%;
            gap: 20px;
            flex-wrap: wrap;
        }
        .scan-section, .input-section {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            text-align: center;
        }
        .scan-section h3, .input-section h3 {
            margin-bottom: 15px;
            color: #00796b;
        }
        
        #qr-reader {
            width: 100%;
            min-height: 300px;
            background-color: #e0f2f1;
            border-radius: 4px;
        }
        
        .manual-input {
            width: 80%;
            padding: 15px;
            font-size: 1.2rem;
            margin-bottom: 15px;
            border: 2px solid #b2dfdb;
            border-radius: 5px;
            text-align: center;
            letter-spacing: 2px;
            transition: border-color 0.3s;
        }
        .manual-input:focus {
            outline: none;
            border-color: #00796b;
        }

        .btn-large {
            padding: 15px 30px;
            font-size: 1.2rem;
            background-color: #ff6f00; /* Amber */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 80%;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s, background 0.2s;
        }
        .btn-large:hover { 
            background-color: #e65100; 
            transform: translateY(-2px);
        }
        
        #gate-result {
            margin-top: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            min-height: 50px;
            width: 100%;
        }
        
        .back-link {
            margin-top: 20px;
            display: block;
            text-align: center;
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="gate-container">
    <div class="gate-header">
        <h1>DKU Smart Cafe - Gate Controller</h1>
        <p><?= $greeting ?></p>
        <p style="font-size: 1.2rem; font-weight: bold; color: #555;">
            🕒 Local Time: <?= $eth_time_str ?> 
        </p>
        <div class="session-info <?= $session_class ?>">
            <?= $session_info ?>
        </div>
    </div>

    <div id="gate-result"></div>

    <div class="gate-content">
        <!-- QR Scanner Section -->
        <div class="scan-section">
            <h3>📷 Scan QR Code</h3>
            
            <!-- HTTPS Warning -->
            <div id="https-warning" style="display:none; color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 10px; border-radius: 5px; font-size: 0.9rem;">
                ⚠️ Camera access requires <strong>HTTPS</strong> or <strong>localhost</strong>. If you are accessing via IP address, please switch to HTTPS or use the Manual Input.
            </div>

            <div id="qr-reader"></div>
            <p style="margin-top: 10px; color: #666;">Point your camera at the QR code</p>
        </div>

        <!-- Manual Input Section -->
        <div class="input-section">
            <h3>⌨️ Enter FAN / ID Number</h3>
            <p>If QR scan fails, type your ID below:</p>
            <input type="text" id="fan-input" class="manual-input" placeholder="e.g. DKU12345" autocomplete="off">
            <button onclick="submitFan()" class="btn-large">ENTER CAFE</button>
            <div style="margin-top: 20px; text-align: left; font-size: 0.9rem; color: #555;">
                <strong>Today's Menu:</strong><br>
                <ul>
                    <?= $menu_display ?>
                </ul>
            </div>
        </div>
    </div>
    
    <a href="index.php" class="back-link">← Back to Home</a>
</div>

<script>
    // Check for secure context (HTTPS or localhost)
    if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1' && window.location.protocol !== 'https:') {
        document.getElementById('https-warning').style.display = 'block';
    }

    // QR Scanner Logic
    let isProcessing = false;

    function handleScan(decodedText) {
        if (isProcessing) return;
        processEntry(decodedText);
    }

    function onScanFailure(error) {
        // console.warn(`QR error = ${error}`);
    }

    if (typeof Html5QrcodeScanner !== 'undefined') {
        let html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(handleScan, onScanFailure);
    } else {
        document.getElementById('qr-reader').innerHTML = '<p style="color:red; padding:20px;">Scanner Library failed to load.<br>Please use Manual Input.</p>';
    }

    // Manual Input Logic
    function submitFan() {
        const input = document.getElementById('fan-input');
        const fan = input.value.trim();
        if (fan) {
            processEntry(fan);
            input.value = ''; // Clear input
            input.focus();
        } else {
            const resultDiv = document.getElementById('gate-result');
            resultDiv.innerHTML = '<div style="color: orange; font-size: 1.5rem;">⚠️ Please enter a valid FAN/ID</div>';
            setTimeout(() => { resultDiv.innerHTML = ''; }, 2000);
        }
    }

    // Allow Enter key
    document.getElementById('fan-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            submitFan();
        }
    });

    // Common Processing Logic
    function processEntry(id) {
        isProcessing = true;
        const resultDiv = document.getElementById('gate-result');
        resultDiv.innerHTML = '<span style="color: blue;">Processing...</span>';

        fetch('check_meal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                national_id: id,
                gate_mode: true 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<div style="color: green; font-size: 2rem;">✅ ' + data.message + '</div>';
                // Play success sound
                let audio = new Audio('https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg'); // Placeholder sound
                // audio.play().catch(e => console.log(e));
                
                // Redirect to Menu Display Page after 1 second
                setTimeout(() => {
                    window.location.href = 'menu_display.php';
                }, 1000);
            } else {
                resultDiv.innerHTML = '<div style="color: red; font-size: 2rem;">❌ ' + data.message + '</div>';
                // Play error sound
                
                // Reset after 3 seconds
                setTimeout(() => { 
                    resultDiv.innerHTML = ''; 
                    isProcessing = false;
                }, 3000);
            }
        })
        .catch(err => {
            resultDiv.innerHTML = '<span style="color: red;">System Error</span>';
            console.error(err);
            isProcessing = false;
        });
    }
</script>

</body>
</html>