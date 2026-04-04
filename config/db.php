<?php

// Database Configuration
$db_host = "localhost";     
$db_user = "root";           
$db_pass = "";               
$db_name = "rms_db";

// Create database connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check if connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set to UTF-8 for proper text handling
mysqli_set_charset($conn, "utf8");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Helper function to sanitize user input
 * This prevents SQL injection attacks
 * 
 * @param string $data - The input data to sanitize
 * @return string - The sanitized data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);                    // Remove extra spaces
    $data = stripslashes($data);            // Remove backslashes
    $data = htmlspecialchars($data);        // Convert special characters
    $data = mysqli_real_escape_string($conn, $data);  // Escape SQL characters
    return $data;
}

/**
 * Helper function to check if user is logged in
 * 
 * @return boolean - True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Helper function to check if logged in user is admin
 * 
 * @return boolean - True if admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

/**
 * Helper function to redirect to another page
 * 
 * @param string $url - The URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Helper function to display alert message
 * 
 * @param string $message - The message to display
 * @param string $type - Type of alert (success, error, warning)
 */
function showAlert($message, $type = 'success') {
    return "<div class='alert alert-$type'>$message</div>";
}
?>
