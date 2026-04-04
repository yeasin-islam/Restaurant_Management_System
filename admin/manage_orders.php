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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    
    $update_query = "UPDATE orders SET status = '$status' WHERE order_id = $order_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success = 'Order status updated successfully';
    } else {
        $error = 'Failed to update order status';
    }
}

// Filter by status
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query with filter
$orders_query = "SELECT o.*, u.full_name, u.email, u.phone 
                 FROM orders o 
                 JOIN users u ON o.user_id = u.user_id";

if (!empty($status_filter)) {
    $orders_query .= " WHERE o.status = '$status_filter'";
}

$orders_query .= " ORDER BY o.order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);

// Get status counts
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='Pending'"))['count'];
$preparing_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='Preparing'"))['count'];
$delivered_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='Delivered'"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="admin-page">
        <div class="container">
            <div class="page-header">
                <h1>Manage Orders</h1>
                <p>View and update order statuses</p>
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
                <a href="manage_orders.php" 
                   class="filter-tab <?php echo empty($status_filter) ? 'active' : ''; ?>">
                    All (<?php echo $pending_count + $preparing_count + $delivered_count; ?>)
                </a>
                <a href="manage_orders.php?status=Pending" 
                   class="filter-tab <?php echo $status_filter == 'Pending' ? 'active' : ''; ?>">
                    Pending (<?php echo $pending_count; ?>)
                </a>
                <a href="manage_orders.php?status=Preparing" 
                   class="filter-tab <?php echo $status_filter == 'Preparing' ? 'active' : ''; ?>">
                    Preparing (<?php echo $preparing_count; ?>)
                </a>
                <a href="manage_orders.php?status=Delivered" 
                   class="filter-tab <?php echo $status_filter == 'Delivered' ? 'active' : ''; ?>">
                    Delivered (<?php echo $delivered_count; ?>)
                </a>
            </div>

            <!-- Orders List -->
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <div class="orders-admin-list">
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <div class="order-admin-card">
                            <div class="order-admin-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo $order['order_id']; ?></h3>
                                    <span class="order-date">
                                        <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                                    </span>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </div>

                            <div class="order-admin-body">
                                <!-- Customer Info -->
                                <div class="customer-info">
                                    <h4>Customer Information</h4>
                                    <p><strong>Name:</strong> <?php echo $order['full_name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $order['phone'] ?: 'N/A'; ?></p>
                                    <p><strong>Delivery Address:</strong> <?php echo $order['delivery_address']; ?></p>
                                </div>

                                <!-- Order Items -->
                                <div class="order-items-list">
                                    <h4>Order Items</h4>
                                    <?php
                                    $details_query = "SELECT od.*, mi.item_name 
                                                     FROM order_details od 
                                                     JOIN menu_items mi ON od.item_id = mi.item_id 
                                                     WHERE od.order_id = {$order['order_id']}";
                                    $details_result = mysqli_query($conn, $details_query);
                                    ?>
                                    <table class="items-table">
                                        <tr>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                        <?php while ($detail = mysqli_fetch_assoc($details_result)): ?>
                                            <tr>
                                                <td><?php echo $detail['item_name']; ?></td>
                                                <td><?php echo $detail['quantity']; ?></td>
                                                <td>$<?php echo number_format($detail['price'], 2); ?></td>
                                                <td>$<?php echo number_format($detail['price'] * $detail['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <tr class="total-row">
                                            <td colspan="3"><strong>Total</strong></td>
                                            <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="order-admin-footer">
                                <form action="manage_orders.php" method="POST" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <label>Update Status:</label>
                                    <select name="status" class="status-select">
                                        <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Preparing" <?php echo $order['status'] == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-small">Update</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No orders found</h3>
                    <p>There are no orders matching your filter</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
