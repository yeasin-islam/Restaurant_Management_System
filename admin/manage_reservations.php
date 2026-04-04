<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

$success = '';
$error = '';

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reservation_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $status = 'Approved';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    } else {
        $status = '';
    }
    
    if (!empty($status)) {
        $update_query = "UPDATE reservations SET status = '$status' WHERE reservation_id = $reservation_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success = "Reservation $status successfully";
        } else {
            $error = 'Failed to update reservation';
        }
    }
}

// Filter by status
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query with filter
$reservations_query = "SELECT r.*, u.full_name, u.email, u.phone 
                       FROM reservations r 
                       JOIN users u ON r.user_id = u.user_id";

if (!empty($status_filter)) {
    $reservations_query .= " WHERE r.status = '$status_filter'";
}

$reservations_query .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";
$reservations_result = mysqli_query($conn, $reservations_query);

// Get status counts
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations WHERE status='Pending'"))['count'];
$approved_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations WHERE status='Approved'"))['count'];
$rejected_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations WHERE status='Rejected'"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="admin-page">
        <div class="container">
            <div class="page-header">
                <h1>Manage Reservations</h1>
                <p>Approve or reject table reservations</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Status Filter Tabs -->
            <div class="filter-tabs">
                <a href="manage_reservations.php" 
                   class="filter-tab <?php echo empty($status_filter) ? 'active' : ''; ?>">
                    All (<?php echo $pending_count + $approved_count + $rejected_count; ?>)
                </a>
                <a href="manage_reservations.php?status=Pending" 
                   class="filter-tab <?php echo $status_filter == 'Pending' ? 'active' : ''; ?>">
                    Pending (<?php echo $pending_count; ?>)
                </a>
                <a href="manage_reservations.php?status=Approved" 
                   class="filter-tab <?php echo $status_filter == 'Approved' ? 'active' : ''; ?>">
                    Approved (<?php echo $approved_count; ?>)
                </a>
                <a href="manage_reservations.php?status=Rejected" 
                   class="filter-tab <?php echo $status_filter == 'Rejected' ? 'active' : ''; ?>">
                    Rejected (<?php echo $rejected_count; ?>)
                </a>
            </div>

            <!-- Reservations List -->
            <?php if (mysqli_num_rows($reservations_result) > 0): ?>
                <div class="reservations-admin-list">
                    <table class="data-table full-width">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Guests</th>
                                <th>Special Request</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reservation = mysqli_fetch_assoc($reservations_result)): ?>
                                <tr>
                                    <td>#<?php echo $reservation['reservation_id']; ?></td>
                                    <td><?php echo $reservation['full_name']; ?></td>
                                    <td>
                                        <small><?php echo $reservation['email']; ?></small><br>
                                        <small><?php echo $reservation['phone'] ?: 'N/A'; ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($reservation['reservation_time'])); ?></td>
                                    <td><?php echo $reservation['num_guests']; ?></td>
                                    <td>
                                        <?php echo $reservation['special_request'] ?: '-'; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($reservation['status']); ?>">
                                            <?php echo $reservation['status']; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if ($reservation['status'] == 'Pending'): ?>
                                            <a href="manage_reservations.php?action=approve&id=<?php echo $reservation['reservation_id']; ?>" 
                                               class="btn btn-small btn-success">Approve</a>
                                            <a href="manage_reservations.php?action=reject&id=<?php echo $reservation['reservation_id']; ?>" 
                                               class="btn btn-small btn-danger">Reject</a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No reservations found</h3>
                    <p>There are no reservations matching your filter</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
