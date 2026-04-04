<?php

// Determine base path
$base_path = '';
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
if ($current_dir == 'admin' || $current_dir == 'user' || $current_dir == 'auth') {
    $base_path = '../';
}
?>

<footer class="footer">
    <div class="footer-container">
        <!-- About Column -->
        <div class="footer-column">
            <h3>Delicious Bites</h3>
            <p>Experience the finest cuisine with exceptional service. 
               We serve happiness on a plate!</p>
        </div>

        <!-- Quick Links Column -->
        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                <li><a href="<?php echo $base_path; ?>user/menu.php">Menu</a></li>
                <li><a href="<?php echo $base_path; ?>user/reservation.php">Book a Table</a></li>
                <li><a href="<?php echo $base_path; ?>auth/login.php">Login</a></li>
            </ul>
        </div>

        <!-- Opening Hours Column -->
        <div class="footer-column">
            <h3>Opening Hours</h3>
            <ul class="footer-hours">
                <li><span>Monday - Friday:</span> 10:00 AM - 10:00 PM</li>
                <li><span>Saturday:</span> 11:00 AM - 11:00 PM</li>
                <li><span>Sunday:</span> 12:00 PM - 9:00 PM</li>
            </ul>
        </div>

        <!-- Contact Column -->
        <div class="footer-column">
            <h3>Contact Us</h3>
            <ul class="footer-contact">
                <li>&#128205; 123 Food Street, City</li>
                <li>&#128222; +1 234 567 8900</li>
                <li>&#128231; info@deliciousbites.com</li>
            </ul>
        </div>
    </div>

    <!-- Copyright Bar -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Delicious Bites. All Rights Reserved.</p>
        <p>CSE Academic Project - Restaurant Management System</p>
    </div>
</footer>
