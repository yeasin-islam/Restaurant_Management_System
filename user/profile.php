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

// Get current user data
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);

    if (empty($full_name)) {
        $error = 'Full name is required';
    } else {
        $update_query = "UPDATE users SET full_name = '$full_name', phone = '$phone', address = '$address' 
                        WHERE user_id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['full_name'] = $full_name;
            $success = 'Profile updated successfully!';
            
            // Refresh user data
            $user_result = mysqli_query($conn, $user_query);
            $user = mysqli_fetch_assoc($user_result);
        } else {
            $error = 'Failed to update profile';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all password fields';
    }
    elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters';
    }
    elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    }
    elseif (!password_verify($current_password, $user['password'])) {
        $error = 'Current password is incorrect';
    }
    else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success = 'Password changed successfully!';
        } else {
            $error = 'Failed to change password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="profile-page">
        <div class="container">
            <div class="page-header">
                <h1>My Profile</h1>
                <p>Manage your account information</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="profile-container">
                <!-- Profile Information -->
                <div class="profile-section">
                    <h2>Profile Information</h2>
                    <form action="profile.php" method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   value="<?php echo $user['email']; ?>" 
                                   disabled>
                            <small>Email cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?php echo $user['full_name']; ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo $user['phone']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="address">Delivery Address</label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"><?php echo $user['address']; ?></textarea>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="profile-section">
                    <h2>Change Password</h2>
                    <form action="profile.php" method="POST" class="password-form">
                        <div class="form-group">
                            <label for="current_password">Current Password *</label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password *</label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Min 6 characters"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password *</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-secondary">
                            Change Password
                        </button>
                    </form>
                </div>

                <!-- Account Info -->
                <div class="profile-section account-info">
                    <h2>Account Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Member Since</span>
                            <span class="info-value">
                                <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Account Type</span>
                            <span class="info-value"><?php echo ucfirst($user['role']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
