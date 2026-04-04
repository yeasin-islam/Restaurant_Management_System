<?php

// Include database configuration if not already included
if (!isset($conn)) {
    require_once dirname(__FILE__) . '/../config/db.php';
}

// Determine base path
$base_path = '';
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
if ($current_dir == 'admin' || $current_dir == 'user' || $current_dir == 'auth') {
    $base_path = '../';
}

// Default page title if not set
if (!isset($page_title)) {
    $page_title = 'Restaurant Management System';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Delicious Bites - Restaurant Management System">
    <title><?php echo $page_title; ?> - Delicious Bites</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>
<body>
    <?php include dirname(__FILE__) . '/navbar.php'; ?>
