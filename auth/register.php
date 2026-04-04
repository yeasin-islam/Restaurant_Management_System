<?php

// Include database configuration
require_once '../config/db.php';

// Initialize variables
$full_name = '';
$email = '';
$phone = '';
$address = '';
$error = '';
$success = '';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('../user/menu.php');
}

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate required fields
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    }
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    }
    // Validate password length
    elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    }
    else {
        // Check if email already exists in database
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email already registered. Please use a different email.';
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $insert_query = "INSERT INTO users (full_name, email, phone, password, address, role) 
                            VALUES ('$full_name', '$email', '$phone', '$hashed_password', '$address', 'user')";

            if (mysqli_query($conn, $insert_query)) {
                // Registration successful - redirect to login
                redirect('login.php?registered=1');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-box">
                <h2>Create Account</h2>
                <p class="auth-subtitle">Join us for delicious experiences</p>

                <!-- Display error message if any -->
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form action="register.php" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               value="<?php echo $full_name; ?>" 
                               placeholder="Enter your full name"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo $email; ?>" 
                               placeholder="Enter your email"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?php echo $phone; ?>" 
                               placeholder="Enter your phone number">
                    </div>

                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <textarea id="address" 
                                  name="address" 
                                  placeholder="Enter your delivery address"
                                  rows="2"><?php echo $address; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Min 6 characters"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Confirm password"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
