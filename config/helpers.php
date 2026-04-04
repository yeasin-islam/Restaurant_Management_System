<?php

// ===============================================
// SESSION MANAGEMENT FUNCTIONS
// ===============================================

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../auth/login.php');
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: ../../index.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../../index.php');
        exit();
    }
}

// UTILITY FUNCTIONS
function showError($message) {
    echo '<div class="alert alert-error">' . htmlspecialchars($message) . '</div>';
}

function showSuccess($message) {
    echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatPrice($price) {
    return number_format((float)$price, 2, '.', '');
}

function formatDate($date) {
    return date('d-M-Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d-M-Y H:i', strtotime($datetime));
}

?>
