<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is logged in
if (!isLoggedIn() || isAdmin()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';

// Check for success message from checkout
if (isset($_GET['success'])) {
    $success = 'Order placed successfully! You can track your order status here.';
}

// Get all orders for this user
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="orders-page">
        <div class="container">
            <div class="page-header">
                <h1>My Orders</h1>
                <p>Track and view your order history</p>
            </div>

            <!-- Display success message -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <div class="orders-list">
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo $order['order_id']; ?></h3>
                                    <span class="order-date">
                                        <?php echo date('F d, Y - h:i A', strtotime($order['order_date'])); ?>
                                    </span>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </div>

                            <div class="order-details">
                                <?php
                                // Get order items
                                $details_query = "SELECT od.*, mi.item_name, mi.image 
                                                 FROM order_details od 
                                                 JOIN menu_items mi ON od.item_id = mi.item_id 
                                                 WHERE od.order_id = {$order['order_id']}";
                                $details_result = mysqli_query($conn, $details_query);
                                ?>
                                
                                <div class="order-items">
                                    <?php while ($detail = mysqli_fetch_assoc($details_result)): ?>
                                        <div class="order-item">
                                            <img src="../assets/images/<?php echo $detail['image']; ?>" 
                                                 alt="<?php echo $detail['item_name']; ?>"
                                                 onerror="this.src='../assets/images/default.jpg'">
                                            <div class="item-info">
                                                <span class="item-name"><?php echo $detail['item_name']; ?></span>
                                                <span class="item-qty">Qty: <?php echo $detail['quantity']; ?></span>
                                            </div>
                                            <span class="item-price">
                                                $<?php echo number_format($detail['price'] * $detail['quantity'], 2); ?>
                                            </span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>

                                <div class="order-footer">
                                    <div class="delivery-address">
                                        <strong>Delivery Address:</strong>
                                        <p><?php echo $order['delivery_address']; ?></p>
                                    </div>
                                    <div class="order-total">
                                        <span>Total:</span>
                                        <span class="total-amount">$<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <span class="empty-icon">&#128230;</span>
                    <h3>No orders yet</h3>
                    <p>Your order history will appear here once you place an order</p>
                    <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
