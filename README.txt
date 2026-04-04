==================================================
RESTAURANT MANAGEMENT SYSTEM
CSE Academic Project - PHP & MySQL
==================================================

INSTALLATION INSTRUCTIONS
--------------------------

1. REQUIREMENTS:
   - XAMPP/WAMP/LAMP Server
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Web Browser (Chrome, Firefox, Edge)

2. SETUP STEPS:

   Step 1: Install XAMPP
   - Download from: https://www.apachefriends.org/
   - Install and start Apache & MySQL services

   Step 2: Copy Project Files
   - Copy the entire "RestaurantManagementSystem" folder
   - Paste it into: C:\xampp\htdocs\ (Windows)
     or /var/www/html/ (Linux)

   Step 3: Create Database
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database named: restaurant_db
   - Import the SQL file: database/rms.sql
     (Click Import tab -> Choose file -> Select rms.sql -> Go)

   Step 4: Configure Database Connection
   - Open config/db.php
   - Update the database credentials if needed:
     * DB_HOST = "localhost"
     * DB_USER = "root"
     * DB_PASS = "" (empty for XAMPP default)
     * DB_NAME = "restaurant_db"

   Step 5: Access the Application
   - Open browser and go to:  

3. DEFAULT ADMIN CREDENTIALS:
   - Email: admin@restaurant.com
   - Password: admin123

4. PROJECT STRUCTURE:
   
   RestaurantManagementSystem/
   |
   |-- admin/                 (Admin panel pages)
   |   |-- dashboard.php
   |   |-- manage_menu.php
   |   |-- manage_orders.php
   |   |-- manage_reservations.php
   |   |-- manage_feedback.php
   |   |-- manage_users.php
   |
   |-- assets/                (Static files)
   |   |-- css/
   |   |   |-- style.css
   |   |-- js/
   |   |   |-- script.js
   |   |-- images/
   |       |-- uploads/       (Menu item images)
   |
   |-- auth/                  (Authentication)
   |   |-- login.php
   |   |-- register.php
   |   |-- logout.php
   |
   |-- config/                (Configuration)
   |   |-- db.php
   |
   |-- database/              (SQL files)
   |   |-- rms.sql
   |
   |-- includes/              (Common components)
   |   |-- header.php
   |   |-- footer.php
   |   |-- navbar.php
   |
   |-- user/                  (User panel pages)
   |   |-- dashboard.php
   |   |-- menu.php
   |   |-- cart.php
   |   |-- orders.php
   |   |-- reservation.php
   |   |-- feedback.php
   |   |-- profile.php
   |
   |-- index.php              (Home page)
   |-- README.txt             (This file)

5. FEATURES:

   USER PANEL:
   - User Registration & Login
   - Browse Menu by Categories
   - Add Items to Cart
   - Place Orders (Dine-in/Takeaway/Delivery)
   - Make Table Reservations
   - Submit Feedback
   - View Order History
   - Update Profile

   ADMIN PANEL:
   - Dashboard with Statistics
   - Manage Menu Items (Add/Edit/Delete)
   - Manage Categories
   - View & Update Order Status
   - Manage Table Reservations
   - View Customer Feedback
   - Manage Users

6. TECHNOLOGIES USED:
   - Frontend: HTML5, CSS3, JavaScript
   - Backend: PHP
   - Database: MySQL
   - Icons: Font Awesome
   - Fonts: Google Fonts (Poppins)

7. TROUBLESHOOTING:

   Error: "Connection failed"
   - Make sure MySQL service is running
   - Check database credentials in config/db.php
   - Verify database "restaurant_db" exists

   Error: "Table doesn't exist"
   - Import the rms.sql file in phpMyAdmin

   Images not uploading:
   - Check write permissions on assets/images/uploads/
   - On Linux: chmod 777 assets/images/uploads/

==================================================
Developed for CSE Academic Project
==================================================
