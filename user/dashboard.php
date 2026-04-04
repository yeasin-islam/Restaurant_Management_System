<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is logged in
if (!isLoggedIn() || isAdmin()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];

// Get user's recent orders
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC LIMIT 5";
$orders_result = mysqli_query($conn, $orders_query);

// Get user's reservations
$reservations_query = "SELECT * FROM reservations WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5";
$reservations_result = mysqli_query($conn, $reservations_query);

// Get counts for statistics
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id"))['count'];
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = $user_id"))['total'];
$total_reservations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations WHERE user_id = $user_id"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="dashboard-page">
        <div class="container">
            <div class="page-header">
                <h1>Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
                <p>Manage your orders and reservations</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <span class="stat-icon">&#128230;</span>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $total_orders; ?></span>
                        <span class="stat-label">Total Orders</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">&#128176;</span>
                    <div class="stat-info">
                        <span class="stat-value">$<?php echo number_format($total_spent, 2); ?></span>
                        <span class="stat-label">Total Spent</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">&#128197;</span>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $total_reservations; ?></span>
                        <span class="stat-label">Reservations</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="menu.php" class="action-btn">
                        <span class="action-icon">&#127869;</span>
                        <span>Browse Menu</span>
                    </a>
                    <a href="cart.php" class="action-btn">
                        <span class="action-icon">&#128722;</span>
                        <span>View Cart</span>
                    </a>
                    <a href="reservation.php" class="action-btn">
                        <span class="action-icon">&#128197;</span>
                        <span>Book Table</span>
                    </a>
                    <a href="feedback.php" class="action-btn">
                        <span class="action-icon">&#11088;</span>
                        <span>Give Feedback</span>
                    </a>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Recent Orders -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="orders.php" class="view-all">View All</a>
                    </div>
                    <?php if (mysqli_num_rows($orders_result) > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No orders yet. <a href="menu.php">Browse our menu</a> to place your first order!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Reservations -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Reservations</h2>
                        <a href="reservation.php" class="view-all">Book New</a>
                    </div>
                    <?php if (mysqli_num_rows($reservations_result) > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Guests</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($reservation = mysqli_fetch_assoc($reservations_result)): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($reservation['reservation_time'])); ?></td>
                                        <td><?php echo $reservation['num_guests']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($reservation['status']); ?>">
                                                <?php echo $reservation['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No reservations yet. <a href="reservation.php">Book a table</a> now!</p>
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
