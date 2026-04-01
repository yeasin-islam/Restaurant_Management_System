<?php
/**
 * Helper Functions Library
 * This file contains reusable functions used throughout the application
 * Used for authentication, validation, and utility functions
 */

// ===============================================
// SESSION MANAGEMENT FUNCTIONS
// ===============================================

/**
 * Check if a user is logged in
 * Verifies if user_id exists in current session
 * Returns: true if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if logged-in user is an admin
 * Verifies if user's role is 'admin'
 * Returns: true if admin, false otherwise
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to login page if not logged in
 * Useful for protecting pages that require authentication
 * Redirects to: auth/login.php
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../auth/login.php');
        exit();
    }
}

/**
 * Redirect to home if logged in
 * Useful for auth pages that shouldn't be visited by logged-in users
 * Redirects to: index.php
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: ../../index.php');
        exit();
    }
}

/**
 * Redirect to home if not admin
 * Protects admin pages from non-admin users
 * Redirects to: index.php
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../../index.php');
        exit();
    }
}

// ===============================================
// UTILITY FUNCTIONS
// ===============================================

/**
 * Display a formatted error message
 * Creates a styled error box for user feedback
 */
function showError($message) {
    echo '<div class="alert alert-error">' . htmlspecialchars($message) . '</div>';
}

/**
 * Display a formatted success message
 * Creates a styled success box for user feedback
 */
function showSuccess($message) {
    echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

/**
 * Sanitize user input to prevent XSS attacks
 * Removes HTML and special characters
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format price to 2 decimal places
 * Used for displaying prices consistently
 */
function formatPrice($price) {
    return number_format((float)$price, 2, '.', '');
}

/**
 * Format date for display
 * Converts database date to readable format
 */
function formatDate($date) {
    return date('d-M-Y', strtotime($date));
}

/**
 * Format date and time for display
 * Converts database timestamp to readable format
 */
function formatDateTime($datetime) {
    return date('d-M-Y H:i', strtotime($datetime));
}

?>
