<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

// Handle delete action
$success = '';
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $feedback_id = (int)$_GET['id'];
    $delete_query = "DELETE FROM feedback WHERE feedback_id = $feedback_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $success = 'Feedback deleted successfully';
    }
}

// Filter by rating
$rating_filter = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;

// Build query with filter
$feedback_query = "SELECT f.*, u.full_name, u.email 
                   FROM feedback f 
                   JOIN users u ON f.user_id = u.user_id";

if ($rating_filter > 0) {
    $feedback_query .= " WHERE f.rating = $rating_filter";
}

$feedback_query .= " ORDER BY f.created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);

// Get average rating
$avg_rating = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(AVG(rating), 0) as avg FROM feedback"))['avg'];

// Get rating distribution
$rating_counts = array();
for ($i = 1; $i <= 5; $i++) {
    $rating_counts[$i] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM feedback WHERE rating = $i"))['count'];
}
$total_feedback = array_sum($rating_counts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="admin-page">
        <div class="container">
            <div class="page-header">
                <h1>Customer Feedback</h1>
                <p>View customer reviews and ratings</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Rating Overview -->
            <div class="rating-overview">
                <div class="avg-rating-box">
                    <span class="avg-number"><?php echo number_format($avg_rating, 1); ?></span>
                    <div class="avg-stars">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= round($avg_rating)) {
                                echo '<span class="star filled">&#9733;</span>';
                            } else {
                                echo '<span class="star">&#9734;</span>';
                            }
                        }
                        ?>
                    </div>
                    <span class="total-reviews"><?php echo $total_feedback; ?> reviews</span>
                </div>
                <div class="rating-bars">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <div class="rating-bar-row">
                            <span class="rating-label"><?php echo $i; ?> &#9733;</span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" 
                                     style="width: <?php echo $total_feedback > 0 ? ($rating_counts[$i] / $total_feedback * 100) : 0; ?>%">
                                </div>
                            </div>
                            <span class="rating-count"><?php echo $rating_counts[$i]; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Rating Filter -->
            <div class="filter-tabs">
                <a href="manage_feedback.php" 
                   class="filter-tab <?php echo $rating_filter == 0 ? 'active' : ''; ?>">
                    All
                </a>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <a href="manage_feedback.php?rating=<?php echo $i; ?>" 
                       class="filter-tab <?php echo $rating_filter == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?> Star (<?php echo $rating_counts[$i]; ?>)
                    </a>
                <?php endfor; ?>
            </div>

            <!-- Feedback List -->
            <?php if (mysqli_num_rows($feedback_result) > 0): ?>
                <div class="feedback-admin-list">
                    <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                        <div class="feedback-admin-card">
                            <div class="feedback-admin-header">
                                <div class="customer-info">
                                    <strong><?php echo $feedback['full_name']; ?></strong>
                                    <small><?php echo $feedback['email']; ?></small>
                                </div>
                                <div class="feedback-meta">
                                    <div class="feedback-stars">
                                        <?php 
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
                            </div>
                            <div class="feedback-admin-body">
                                <p><?php echo $feedback['message']; ?></p>
                            </div>
                            <div class="feedback-admin-footer">
                                <a href="manage_feedback.php?action=delete&id=<?php echo $feedback['feedback_id']; ?>" 
                                   class="btn btn-small btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this feedback?')">
                                    Delete
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No feedback found</h3>
                    <p>There is no feedback matching your filter</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
