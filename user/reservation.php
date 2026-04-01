<?php
/**
 * Table Reservation Page
 * Allows users to book a table at the restaurant
 * Shows existing reservations and their status
 */

// Include database configuration
require_once '../config/db.php';

// Check if user is logged in
if (!isLoggedIn() || isAdmin()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_date = sanitize($_POST['reservation_date']);
    $reservation_time = sanitize($_POST['reservation_time']);
    $num_guests = (int)$_POST['num_guests'];
    $special_request = sanitize($_POST['special_request']);

    // Validate inputs
    if (empty($reservation_date) || empty($reservation_time) || $num_guests < 1) {
        $error = 'Please fill in all required fields';
    }
    // Check if date is in the future
    elseif (strtotime($reservation_date) < strtotime(date('Y-m-d'))) {
        $error = 'Please select a future date';
    }
    // Check if guests count is reasonable
    elseif ($num_guests > 20) {
        $error = 'For parties larger than 20, please call us directly';
    }
    else {
        // Insert reservation
        $insert_query = "INSERT INTO reservations (user_id, reservation_date, reservation_time, num_guests, special_request, status) 
                        VALUES ($user_id, '$reservation_date', '$reservation_time', $num_guests, '$special_request', 'Pending')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success = 'Reservation submitted successfully! We will confirm it shortly.';
        } else {
            $error = 'Failed to submit reservation. Please try again.';
        }
    }
}

// Get user's reservations
$reservations_query = "SELECT * FROM reservations WHERE user_id = $user_id ORDER BY created_at DESC";
$reservations_result = mysqli_query($conn, $reservations_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Reservation - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="reservation-page">
        <div class="container">
            <div class="page-header">
                <h1>Table Reservation</h1>
                <p>Book your table in advance for a seamless dining experience</p>
            </div>

            <div class="reservation-container">
                <!-- Reservation Form -->
                <div class="reservation-form-section">
                    <h2>Book a Table</h2>
                    
                    <!-- Display messages -->
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="reservation.php" method="POST" class="reservation-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="reservation_date">Date *</label>
                                <input type="date" 
                                       id="reservation_date" 
                                       name="reservation_date" 
                                       min="<?php echo date('Y-m-d'); ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="reservation_time">Time *</label>
                                <select id="reservation_time" name="reservation_time" required>
                                    <option value="">Select Time</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">1:00 PM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="17:00">5:00 PM</option>
                                    <option value="18:00">6:00 PM</option>
                                    <option value="19:00">7:00 PM</option>
                                    <option value="20:00">8:00 PM</option>
                                    <option value="21:00">9:00 PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="num_guests">Number of Guests *</label>
                            <input type="number" 
                                   id="num_guests" 
                                   name="num_guests" 
                                   min="1" 
                                   max="20" 
                                   placeholder="Enter number of guests"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="special_request">Special Requests (Optional)</label>
                            <textarea id="special_request" 
                                      name="special_request" 
                                      rows="3" 
                                      placeholder="Any special requests or dietary requirements?"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Book Table</button>
                    </form>
                </div>

                <!-- Existing Reservations -->
                <div class="reservations-list-section">
                    <h2>Your Reservations</h2>
                    
                    <?php if (mysqli_num_rows($reservations_result) > 0): ?>
                        <div class="reservations-list">
                            <?php while ($reservation = mysqli_fetch_assoc($reservations_result)): ?>
                                <div class="reservation-card">
                                    <div class="reservation-date-box">
                                        <span class="day"><?php echo date('d', strtotime($reservation['reservation_date'])); ?></span>
                                        <span class="month"><?php echo date('M', strtotime($reservation['reservation_date'])); ?></span>
                                    </div>
                                    <div class="reservation-info">
                                        <div class="reservation-time">
                                            <?php echo date('h:i A', strtotime($reservation['reservation_time'])); ?>
                                        </div>
                                        <div class="reservation-guests">
                                            <?php echo $reservation['num_guests']; ?> Guest(s)
                                        </div>
                                        <?php if ($reservation['special_request']): ?>
                                            <div class="special-request">
                                                "<?php echo $reservation['special_request']; ?>"
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="status-badge status-<?php echo strtolower($reservation['status']); ?>">
                                        <?php echo $reservation['status']; ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state small">
                            <p>No reservations yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
