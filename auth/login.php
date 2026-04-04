<?php

session_start();

// Include database configuration
require_once '../config/db.php';

// Initialize variables
$email = '';
$error = '';
$success = '';

// Check if already logged in
if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/menu.php");
    }

    exit();
}


// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form data safely
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {

        $error = "Please fill in all fields";
    } else {

        // Query database
        $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // Check password column exists
            if (isset($user['password'])) {

                // Check hashed password OR plain password (fallback)
                if (
                    password_verify($password, $user['password']) ||
                    $password === $user['password']
                ) {

                    // Create session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect by role
                    if ($user['role'] == 'admin') {

                        header("Location: ../admin/dashboard.php");
                    } else {

                        header("Location: ../user/menu.php");
                    }

                    exit();
                } else {

                    $error = "Invalid email or password";
                }
            } else {

                $error = "Password column missing in database";
            }
        } else {

            $error = "Invalid email or password";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Delicious Bites</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include '../includes/navbar.php'; ?>

    <main class="auth-page">

        <div class="auth-container">

            <div class="auth-box">

                <h2>Welcome Back!</h2>

                <p class="auth-subtitle">Sign in to your account</p>


                <!-- Error Message -->
                <?php if ($error != '') : ?>

                    <div class="alert alert-error">

                        <?php echo $error; ?>

                    </div>

                <?php endif; ?>


                <!-- Success Message -->
                <?php if (isset($_GET['registered'])) : ?>

                    <div class="alert alert-success">

                        Registration successful! Please login.

                    </div>

                <?php endif; ?>


                <form action="login.php" method="POST" class="auth-form">

                    <div class="form-group">

                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            value="<?php echo $email; ?>"
                            placeholder="Enter your email"
                            required>

                    </div>


                    <div class="form-group">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required>

                    </div>


                    <button type="submit" class="btn btn-primary btn-block">

                        Login

                    </button>

                </form>


                <div class="auth-footer">

                    <p>

                        Don't have an account?

                        <a href="register.php">Sign Up</a>

                    </p>

                </div>


                <div class="demo-credentials">

                    <p><strong>Demo Admin Login:</strong></p>

                    <p>Email: admin@rms.com | Password: admin123</p>

                </div>


            </div>

        </div>

    </main>


    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/script.js"></script>

</body>

</html>