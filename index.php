<?php
/**
 * Index Page - Restaurant Management System
 * This is the main landing page of the website
 */

// Include database configuration
require_once 'config/db.php';

// Fetch featured menu items (latest 6 items)
$featured_query = "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY item_id DESC LIMIT 6";
$featured_result = mysqli_query($conn, $featured_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    <title>Delicious Bites - Restaurant Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Delicious Bites</h1>
            <p>Experience the finest cuisine with exceptional service</p>
            <div class="hero-buttons">
                <a href="user/menu.php" class="btn btn-primary">View Menu</a>
                <a href="user/reservation.php" class="btn btn-secondary">Book a Table</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <h2>About Us</h2>
            <p>At Delicious Bites, we believe in serving not just food, but memories. 
               Our chefs use only the freshest ingredients to create dishes that delight 
               your taste buds and warm your heart. Whether you're here for a quick lunch, 
               a romantic dinner, or a family celebration, we promise an unforgettable experience.</p>
        </div>
    </section>

    <!-- Featured Menu Section -->
    <section class="featured-section">
        <div class="container">
            <h2>Featured Menu</h2>
            <div class="menu-grid">
                <?php 
                // Loop through featured menu items
                while ($item = mysqli_fetch_assoc($featured_result)): 
                ?>
                <div class="menu-card">
                    <div class="menu-card-image">
                        <img src="assets/images/<?php echo $item['image']; ?>" 
                             alt="<?php echo $item['item_name']; ?>"
                             onerror="this.src='assets/images/default.jpg'">
                    </div>
                    <div class="menu-card-content">
                        <span class="category-badge"><?php echo $item['category']; ?></span>
                        <h3><?php echo $item['item_name']; ?></h3>
                        <p><?php echo $item['description']; ?></p>
                        <div class="menu-card-footer">
                            <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                            <?php if (isLoggedIn()): ?>
                                <a href="user/cart.php?action=add&id=<?php echo $item['item_id']; ?>" 
                                   class="btn btn-small">Add to Cart</a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-small">Login to Order</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center">
                <a href="user/menu.php" class="btn btn-primary">View Full Menu</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2>Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">&#127869;</div>
                    <h3>Online Ordering</h3>
                    <p>Order your favorite dishes online and enjoy doorstep delivery</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">&#128197;</div>
                    <h3>Table Reservation</h3>
                    <p>Book your table in advance and skip the waiting line</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">&#11088;</div>
                    <h3>Quality Food</h3>
                    <p>We use fresh ingredients to prepare delicious meals</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <?php
                // Get total counts for statistics
                $users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"))['count'];
                $menu_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM menu_items"))['count'];
                $orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
                ?>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $users_count; ?>+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $menu_count; ?>+</span>
                    <span class="stat-label">Menu Items</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $orders_count; ?>+</span>
                    <span class="stat-label">Orders Served</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Star Rating</span>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
