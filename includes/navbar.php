<?php

// This helps with relative URLs when in subdirectories
$base_path = '';
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
if ($current_dir == 'admin' || $current_dir == 'user' || $current_dir == 'auth') {
    $base_path = '../';
}
?>

<nav class="navbar">
    <div class="nav-container">
        <!-- Logo and Brand Name -->
        <a href="<?php echo $base_path; ?>index.php" class="nav-logo">
            <img src="<?php echo $base_path; ?>assets/images/favicon.png" alt="Delicious Bites Logo" class="logo-icon" style="height:50px; width:auto;">
            <span class="logo-text">Delicious Bites</span>
        </a>

        <!-- Mobile Menu Toggle Button -->
        <button class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Navigation Links -->
        <ul class="nav-menu" id="navMenu">
            <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
            <li><a href="<?php echo $base_path; ?>user/menu.php">Menu</a></li>

            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <!-- Admin Navigation Links -->
                    <li><a href="<?php echo $base_path; ?>admin/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/manage_menu.php">Manage Menu</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/manage_orders.php">Orders</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/manage_reservations.php">Reservations</a></li>
                <?php else: ?>
                    <!-- User Navigation Links -->
                    <li><a href="<?php echo $base_path; ?>user/cart.php">Cart
                            <?php
                            // Show cart count if items exist
                            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                echo '<span class="cart-badge">' . count($_SESSION['cart']) . '</span>';
                            }
                            ?>
                        </a></li>
                    <li><a href="<?php echo $base_path; ?>user/orders.php">My Orders</a></li>
                    <li><a href="<?php echo $base_path; ?>user/reservation.php">Reservation</a></li>
                <?php endif; ?>

                <!-- User Dropdown Menu -->
                <li class="nav-dropdown">
                    <a href="#" class="dropdown-toggle">
                        <?php echo $_SESSION['full_name']; ?> &#9662;
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (!isAdmin()): ?>
                            <li><a href="<?php echo $base_path; ?>user/profile.php">My Profile</a></li>
                            <li><a href="<?php echo $base_path; ?>user/feedback.php">Feedback</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_path; ?>auth/logout.php">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <!-- Guest Navigation Links -->
                <li><a href="<?php echo $base_path; ?>auth/login.php">Login</a></li>
                <li><a href="<?php echo $base_path; ?>auth/register.php" class="btn btn-nav">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>