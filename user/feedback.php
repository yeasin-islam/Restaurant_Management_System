<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is logged in
if (!isLoggedIn() || isAdmin()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = (int)$_POST['rating'];
    $message = sanitize($_POST['message']);

    // Validate inputs
    if ($rating < 1 || $rating > 5) {
        $error = 'Please select a valid rating (1-5)';
    }
    elseif (empty($message)) {
        $error = 'Please write a review message';
    }
    else {
        // Insert feedback
        $insert_query = "INSERT INTO feedback (user_id, rating, message) 
                        VALUES ($user_id, $rating, '$message')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success = 'Thank you for your feedback!';
        } else {
            $error = 'Failed to submit feedback. Please try again.';
        }
    }
}

// Get user's previous feedback
$feedback_query = "SELECT * FROM feedback WHERE user_id = $user_id ORDER BY created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="feedback-page">
        <div class="container">
            <div class="page-header">
                <h1>Share Your Feedback</h1>
                <p>We value your opinion! Help us serve you better.</p>
            </div>

            <div class="feedback-container">
                <!-- Feedback Form -->
                <div class="feedback-form-section">
                    <h2>Write a Review</h2>
                    
                    <!-- Display messages -->
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="feedback.php" method="POST" class="feedback-form">
                        <div class="form-group">
                            <label>Your Rating *</label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" 
                                           id="star<?php echo $i; ?>" 
                                           name="rating" 
                                           value="<?php echo $i; ?>" 
                                           required>
                                    <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">&#9733;</label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">Your Review *</label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="5" 
                                      placeholder="Tell us about your experience..."
                                      required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    </form>
                </div>

                <!-- Previous Feedback -->
                <div class="feedback-history-section">
                    <h2>Your Previous Reviews</h2>
                    
                    <?php if (mysqli_num_rows($feedback_result) > 0): ?>
                        <div class="feedback-list">
                            <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                                <div class="feedback-card">
                                    <div class="feedback-header">
                                        <div class="feedback-stars">
                                            <?php 
                                            // Display filled stars
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $feedback['rating']) {
                                                    echo '<span class="star filled">&#9733;</span>';
                                                } else {
                                                    echo '<span class="star">&#9734;</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="feedback-date">
                                            <?php echo date('M d, Y', strtotime($feedback['created_at'])); ?>
                                        </span>
                                    </div>
                                    <p class="feedback-message"><?php echo $feedback['message']; ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state small">
                            <p>You haven't submitted any feedback yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
