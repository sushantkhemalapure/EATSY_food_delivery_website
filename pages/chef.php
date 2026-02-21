<?php
require_once '../backend/config/config.php';

$chef_id = $_GET['id'] ?? 0;
$db = Database::getInstance()->getConnection();

// Get chef details
$stmt = $db->prepare("SELECT * FROM chefs WHERE id = ? AND is_available = 1");
$stmt->execute([$chef_id]);
$chef = $stmt->fetch();

if (!$chef) {
    redirect('chefs.php');
}

$error = '';
$success = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $booking_date = sanitize($_POST['booking_date']);
    $booking_time = sanitize($_POST['booking_time']);
    $hours = intval($_POST['hours']);
    $event_type = sanitize($_POST['event_type']);
    $special_requests = sanitize($_POST['special_requests']);
    
    if (empty($booking_date) || empty($booking_time) || $hours < 1) {
        $error = 'Please fill in all required fields';
    } else {
        $total_amount = $hours * $chef['hourly_rate'];
        
        $stmt = $db->prepare("INSERT INTO chef_bookings (user_id, chef_id, booking_date, booking_time, hours, total_amount, event_type, special_requests) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([getUserId(), $chef_id, $booking_date, $booking_time, $hours, $total_amount, $event_type, $special_requests])) {
            $success = 'Booking request submitted successfully! We will contact you soon.';
        } else {
            $error = 'Failed to submit booking. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($chef['name']) ?> - EATSY</title>
    <link rel="icon" type="image/png" href="../frontend/images/logo.png">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">
                    <img src="../frontend/images/logo.png" alt="EATSY Logo">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="chefs.php">Chefs</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <!-- Chef Details -->
            <div>
                <div style="text-align: center; background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                    <img src="../frontend/images/<?= htmlspecialchars($chef['image']) ?>" alt="<?= htmlspecialchars($chef['name']) ?>" 
                         style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 5px solid var(--secondary-color); margin-bottom: 1.5rem;">
                    
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($chef['name']) ?></h1>
                    <p style="color: var(--secondary-color); font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem;">
                        <?= htmlspecialchars($chef['specialty']) ?>
                    </p>
                    
                    <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-size: 1.5rem;">⭐ <?= number_format($chef['rating'], 1) ?></div>
                            <small style="color: #666;">Rating</small>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; color: var(--primary-color); font-weight: bold;">
                                ₹<?= number_format($chef['hourly_rate']) ?>
                            </div>
                            <small style="color: #666;">Per Hour</small>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem;"><?= $chef['experience_years'] ?> years</div>
                            <small style="color: #666;">Experience</small>
                        </div>
                    </div>
                    
                    <div style="text-align: left;">
                        <h3 style="margin-bottom: 1rem;">About</h3>
                        <p style="color: #666; line-height: 1.8;"><?= htmlspecialchars($chef['bio']) ?></p>
                        
                        <div style="margin-top: 1.5rem;">
                            <p style="margin-bottom: 0.5rem;"><strong>📧 Email:</strong> <?= htmlspecialchars($chef['email']) ?></p>
                            <p style="margin-bottom: 0.5rem;"><strong>📱 Phone:</strong> <?= htmlspecialchars($chef['phone']) ?></p>
                            <p><strong>📍 Location:</strong> <?= htmlspecialchars($chef['city']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div>
                <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                    <h2 style="margin-bottom: 1.5rem;">Book This Chef</h2>
                    
                    <?php if (!isLoggedIn()): ?>
                        <div class="alert alert-info">
                            Please <a href="login.php" style="color: var(--primary-color); font-weight: 600;">login</a> to book this chef.
                        </div>
                    <?php else: ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php else: ?>
                        
                        <form method="POST" action="" id="chefBookingForm">
                            <div class="form-group">
                                <label for="booking_date">Booking Date *</label>
                                <input type="date" id="booking_date" name="booking_date" class="form-control" required
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="booking_time">Booking Time *</label>
                                <input type="time" id="booking_time" name="booking_time" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hours">Number of Hours *</label>
                                <input type="number" id="hours" name="hours" class="form-control" min="1" max="24" value="4" required
                                       onchange="calculateTotal()">
                                <small>Rate: ₹<?= number_format($chef['hourly_rate']) ?>/hour</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="event_type">Event Type</label>
                                <select id="event_type" name="event_type" class="form-control">
                                    <option value="">Select event type</option>
                                    <option value="Birthday Party">Birthday Party</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="Corporate Event">Corporate Event</option>
                                    <option value="Family Gathering">Family Gathering</option>
                                    <option value="Personal Cooking">Personal Cooking</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="special_requests">Special Requests</label>
                                <textarea id="special_requests" name="special_requests" class="form-control" rows="4"
                                          placeholder="Any dietary restrictions, special menu requests, etc."></textarea>
                            </div>
                            
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                <h4 style="margin-bottom: 0.5rem;">Estimated Total</h4>
                                <p style="font-size: 2rem; color: var(--primary-color); font-weight: bold; margin: 0;" id="total">
                                    ₹<?= number_format($chef['hourly_rate'] * 4) ?>
                                </p>
                                <small style="color: #666;">Final price may vary based on menu and requirements</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Booking Request</button>
                        </form>
                        
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="margin-top: 4rem;">
        <div class="footer-content">
            <div class="footer-section">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 50px; margin-bottom: 1rem;">
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../frontend/js/main.js"></script>
    <script>
        function calculateTotal() {
            const hours = document.getElementById('hours').value;
            const rate = <?= $chef['hourly_rate'] ?>;
            const total = hours * rate;
            document.getElementById('total').textContent = '₹' + total.toLocaleString('en-IN');
        }
    </script>
</body>
</html>
