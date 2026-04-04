<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

$success = '';
$error = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Prevent deleting admin
    $check_query = "SELECT role FROM users WHERE user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    $user_to_delete = mysqli_fetch_assoc($check_result);
    
    if ($user_to_delete && $user_to_delete['role'] == 'admin') {
        $error = 'Cannot delete admin users';
    } else {
        $delete_query = "DELETE FROM users WHERE user_id = $user_id";
        if (mysqli_query($conn, $delete_query)) {
            $success = 'User deleted successfully';
        } else {
            $error = 'Failed to delete user';
        }
    }
}

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$users_query = "SELECT u.*, 
                (SELECT COUNT(*) FROM orders WHERE user_id = u.user_id) as total_orders,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.user_id) as total_spent
                FROM users u WHERE u.role = 'user'";

if (!empty($search)) {
    $users_query .= " AND (u.full_name LIKE '%$search%' OR u.email LIKE '%$search%' OR u.phone LIKE '%$search%')";
}

$users_query .= " ORDER BY u.created_at DESC";
$users_result = mysqli_query($conn, $users_query);

// Get total count
$total_users = mysqli_num_rows($users_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="admin-page">
        <div class="container">
            <div class="page-header">
                <h1>Manage Users</h1>
                <p>View registered customers (<?php echo $total_users; ?> users)</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Search Form -->
            <div class="search-section">
                <form action="manage_users.php" method="GET" class="search-form">
                    <input type="text" 
                           name="search" 
                           placeholder="Search by name, email, or phone..." 
                           value="<?php echo $search; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="manage_users.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Users List -->
            <?php if (mysqli_num_rows($users_result) > 0): ?>
                <div class="users-admin-list">
                    <table class="data-table full-width">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registered</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                <tr>
                                    <td>#<?php echo $user['user_id']; ?></td>
                                    <td>
                                        <strong><?php echo $user['full_name']; ?></strong>
                                    </td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['phone'] ?: 'N/A'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <span class="badge"><?php echo $user['total_orders']; ?></span>
                                    </td>
                                    <td>$<?php echo number_format($user['total_spent'], 2); ?></td>
                                    <td class="action-buttons">
                                        <a href="manage_users.php?action=delete&id=<?php echo $user['user_id']; ?>" 
                                           class="btn btn-small btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this user? This will also delete their orders, reservations, and feedback.')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <?php if (!empty($search)): ?>
                        <h3>No users found</h3>
                        <p>No users match your search criteria</p>
                        <a href="manage_users.php" class="btn btn-primary">View All Users</a>
                    <?php else: ?>
                        <h3>No registered users</h3>
                        <p>There are no registered customers yet</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
