<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

// Get dashboard statistics
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"))['count'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$total_reservations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations"))['count'];
$total_feedback = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM feedback"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status='Delivered'"))['total'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='Pending'"))['count'];
$pending_reservations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations WHERE status='Pending'"))['count'];

// Get recent orders
$recent_orders_query = "SELECT o.*, u.full_name FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        ORDER BY o.order_date DESC LIMIT 5";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Get recent reservations
$recent_reservations_query = "SELECT r.*, u.full_name FROM reservations r 
                              JOIN users u ON r.user_id = u.user_id 
                              ORDER BY r.created_at DESC LIMIT 5";
$recent_reservations = mysqli_query($conn, $recent_reservations_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="admin-dashboard">
        <div class="container">
            <div class="page-header">
                <h1>Admin Dashboard</h1>
                <p>Welcome back, <?php echo $_SESSION['full_name']; ?>!</p>
            </div>

            <!-- Statistics Cards -->
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon bg-blue">&#128100;</div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $total_users; ?></span>
                        <span class="stat-label">Total Users</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-green">&#128230;</div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $total_orders; ?></span>
                        <span class="stat-label">Total Orders</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-orange">&#128197;</div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $total_reservations; ?></span>
                        <span class="stat-label">Reservations</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-purple">&#11088;</div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $total_feedback; ?></span>
                        <span class="stat-label">Feedback</span>
                    </div>
                </div>
                <div class="stat-card highlight">
                    <div class="stat-icon bg-gold">&#128176;</div>
                    <div class="stat-info">
                        <span class="stat-value">$<?php echo number_format($total_revenue, 2); ?></span>
                        <span class="stat-label">Total Revenue</span>
                    </div>
                </div>
            </div>

            <!-- Pending Items Alert -->
            <?php if ($pending_orders > 0 || $pending_reservations > 0): ?>
                <div class="alert alert-warning">
                    <strong>Action Required:</strong> 
                    <?php if ($pending_orders > 0): ?>
                        <?php echo $pending_orders; ?> pending order(s)
                    <?php endif; ?>
                    <?php if ($pending_orders > 0 && $pending_reservations > 0): ?> | <?php endif; ?>
                    <?php if ($pending_reservations > 0): ?>
                        <?php echo $pending_reservations; ?> pending reservation(s)
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="quick-actions admin-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="manage_menu.php" class="action-btn">
                        <span class="action-icon">&#127869;</span>
                        <span>Manage Menu</span>
                    </a>
                    <a href="manage_orders.php" class="action-btn">
                        <span class="action-icon">&#128230;</span>
                        <span>View Orders</span>
                    </a>
                    <a href="manage_reservations.php" class="action-btn">
                        <span class="action-icon">&#128197;</span>
                        <span>Reservations</span>
                    </a>
                    <a href="manage_users.php" class="action-btn">
                        <span class="action-icon">&#128101;</span>
                        <span>Manage Users</span>
                    </a>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Recent Orders -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="manage_orders.php" class="view-all">View All</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['full_name']; ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Reservations -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Reservations</h2>
                        <a href="manage_reservations.php" class="view-all">View All</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Guests</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reservation = mysqli_fetch_assoc($recent_reservations)): ?>
                                <tr>
                                    <td><?php echo $reservation['full_name']; ?></td>
                                    <td><?php echo date('M d', strtotime($reservation['reservation_date'])); ?></td>
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
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
