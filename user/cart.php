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

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle cart actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Add item to cart
    if ($action == 'add' && isset($_GET['id'])) {
        $item_id = (int)$_GET['id'];
        $item_query = "SELECT * FROM menu_items WHERE item_id = $item_id AND is_available = 1";
        $item_result = mysqli_query($conn, $item_query);
        
        if (mysqli_num_rows($item_result) > 0) {
            $item = mysqli_fetch_assoc($item_result);
            
            if (isset($_SESSION['cart'][$item_id])) {
                $_SESSION['cart'][$item_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$item_id] = array(
                    'item_id' => $item_id,
                    'name' => $item['item_name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => 1
                );
            }
            $success = $item['item_name'] . ' added to cart!';
        }
    }
    
    // Remove item from cart
    if ($action == 'remove' && isset($_GET['id'])) {
        $item_id = (int)$_GET['id'];
        if (isset($_SESSION['cart'][$item_id])) {
            unset($_SESSION['cart'][$item_id]);
            $success = 'Item removed from cart';
        }
    }
    
    // Clear entire cart
    if ($action == 'clear') {
        $_SESSION['cart'] = array();
        $success = 'Cart cleared successfully';
    }
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $item_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            $_SESSION['cart'][$item_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$item_id]);
        }
    }
    $success = 'Cart updated successfully';
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $error = 'Your cart is empty';
    } else {
        $delivery_address = sanitize($_POST['delivery_address']);
        
        if (empty($delivery_address)) {
            $error = 'Please enter a delivery address';
        } else {
            // Calculate total
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // Insert order
            $order_query = "INSERT INTO orders (user_id, total_amount, delivery_address, status) 
                           VALUES ($user_id, $total, '$delivery_address', 'Pending')";
            
            if (mysqli_query($conn, $order_query)) {
                $order_id = mysqli_insert_id($conn);
                
                // Insert order details
                foreach ($_SESSION['cart'] as $item) {
                    $detail_query = "INSERT INTO order_details (order_id, item_id, quantity, price) 
                                    VALUES ($order_id, {$item['item_id']}, {$item['quantity']}, {$item['price']})";
                    mysqli_query($conn, $detail_query);
                }
                
                // Clear cart
                $_SESSION['cart'] = array();
                
                redirect('orders.php?success=1');
            } else {
                $error = 'Failed to place order. Please try again.';
            }
        }
    }
}

// Calculate cart totals
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}

// Get user's saved address
$user_query = "SELECT address FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="cart-page">
        <div class="container">
            <div class="page-header">
                <h1>Shopping Cart</h1>
                <p><?php echo $cart_count; ?> item(s) in your cart</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="cart-container">
                    <!-- Cart Items -->
                    <div class="cart-items">
                        <form action="cart.php" method="POST">
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <tr>
                                            <td class="cart-item-info">
                                                <img src="../assets/images/<?php echo $item['image']; ?>" 
                                                     alt="<?php echo $item['name']; ?>"
                                                     onerror="this.src='../assets/images/default.jpg'">
                                                <span><?php echo $item['name']; ?></span>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>
                                                <input type="number" 
                                                       name="quantity[<?php echo $item['item_id']; ?>]" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="0" 
                                                       max="99"
                                                       class="quantity-input">
                                            </td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            <td>
                                                <a href="cart.php?action=remove&id=<?php echo $item['item_id']; ?>" 
                                                   class="btn btn-small btn-danger">Remove</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="cart-actions">
                                <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                                <a href="cart.php?action=clear" class="btn btn-danger">Clear Cart</a>
                            </div>
                        </form>
                    </div>

                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee:</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>

                        <form action="cart.php" method="POST" class="checkout-form">
                            <div class="form-group">
                                <label for="delivery_address">Delivery Address</label>
                                <textarea id="delivery_address" 
                                          name="delivery_address" 
                                          rows="3" 
                                          placeholder="Enter your delivery address"
                                          required><?php echo $user_data['address']; ?></textarea>
                            </div>
                            <button type="submit" name="checkout" class="btn btn-primary btn-block">
                                Place Order
                            </button>
                        </form>

                        <a href="menu.php" class="continue-shopping">Continue Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <span class="empty-icon">&#128722;</span>
                    <h3>Your cart is empty</h3>
                    <p>Add some delicious items from our menu</p>
                    <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
