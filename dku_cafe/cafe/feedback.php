<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'] ?? '';
    $feedback = trim($_POST['feedback'] ?? '');
    $meal_session = $_POST['meal_session'] ?? '';

    if ($rating && $feedback) {
        // In a real app, you'd create a feedback table
        // For now, just show success message
        $message = "Thank you for your feedback! Rating: $rating stars";
    } else {
        $message = "Please provide both rating and feedback.";
    }
}

// Get recent meal sessions for feedback
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT DISTINCT ms.session_name, DATE(ml.scan_time) as meal_date
    FROM meal_logs ml
    JOIN meal_sessions ms ON ml.meal_session_id = ms.id
    WHERE ml.student_id = ?
    ORDER BY ml.scan_time DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_meals = $stmt->fetchAll();

include __DIR__ . '/header.php';
?>

<div class="feedback-container">
    <h1>Meal Feedback</h1>
    <p>Help us improve by sharing your experience with recent meals.</p>

    <?php if ($message): ?>
        <div class="success" style="margin-bottom: 2rem;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="feedback.php" class="feedback-form">
        <div class="form-group">
            <label for="meal_session">Select Meal Session:</label>
            <select name="meal_session" id="meal_session" required>
                <option value="">Choose a recent meal...</option>
                <?php foreach ($recent_meals as $meal): ?>
                    <option value="<?= htmlspecialchars($meal['session_name'] . ' - ' . $meal['meal_date']) ?>">
                        <?= htmlspecialchars($meal['session_name']) ?> (<?= date('M d', strtotime($meal['meal_date'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Rate your experience:</label>
            <div class="rating-stars">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5">★</label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">★</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">★</label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">★</label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">★</label>
            </div>
        </div>

        <div class="form-group">
            <label for="feedback">Your Feedback:</label>
            <textarea name="feedback" id="feedback" rows="5" placeholder="Tell us about your meal experience..." required></textarea>
        </div>

        <button type="submit" class="btn success">Submit Feedback</button>
    </form>
</div>

<style>
.feedback-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

.feedback-container h1 {
    text-align: center;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.feedback-container > p {
    text-align: center;
    color: var(--text-light);
    margin-bottom: 2rem;
}

.feedback-form {
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.rating-stars {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin: 1rem 0;
}

.rating-stars input[type="radio"] {
    display: none;
}

.rating-stars label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: var(--transition);
}

.rating-stars input[type="radio"]:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: var(--warning-color);
}

.rating-stars input[type="radio"]:checked + label {
    color: var(--warning-color);
}

@media (max-width: 768px) {
    .rating-stars {
        gap: 0.25rem;
    }

    .rating-stars label {
        font-size: 1.5rem;
    }
}
</style>

<?php include __DIR__ . '/footer.php'; ?>