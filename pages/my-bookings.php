<?php
require_once '../backend/config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();
$uid = getUserId();

// Fetch user's chef bookings
$stmt = $db->prepare("
    SELECT cb.*, c.name as chef_name, c.specialty, c.hourly_rate, c.image
    FROM chef_bookings cb
    JOIN chefs c ON cb.chef_id = c.id
    WHERE cb.user_id = ?
    ORDER BY cb.booking_date DESC
");
$stmt->execute([$uid]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Chef Bookings - EATSY</title>
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php"><img src="../frontend/images/logo.png" alt="EATSY Logo"></a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="chefs.php">Hire a Chef</a></li>
                <li><a href="my-bookings.php" class="active">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h2>My Chef Bookings</h2>

        <?php if (empty($bookings)): ?>
            <div style="text-align: center; padding: 3rem;">
                <p>No bookings yet. <a href="chefs.php" class="btn btn-primary">Browse chefs</a></p>
            </div>
        <?php else: ?>
            <div style="display: grid; gap: 1.5rem;">
                <?php foreach ($bookings as $booking): ?>
                    <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 10px; display: flex; gap: 1.5rem;">
                        <img src="../frontend/images/<?= htmlspecialchars($booking['image']) ?>" alt="<?= htmlspecialchars($booking['chef_name']) ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        <div style="flex: 1;">
                            <h3><?= htmlspecialchars($booking['chef_name']) ?></h3>
                            <p style="color: #666; margin: 0.5rem 0;"><?= htmlspecialchars($booking['specialty']) ?></p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1rem 0;">
                                <div>
                                    <p style="color: #666; font-size: 0.9rem;">Booking Date</p>
                                    <p><?= date('d M Y', strtotime($booking['booking_date'])) ?> at <?= htmlspecialchars($booking['booking_time']) ?></p>
                                </div>
                                <div>
                                    <p style="color: #666; font-size: 0.9rem;">Duration & Event</p>
                                    <p><?= $booking['hours'] ?> hours - <?= htmlspecialchars($booking['event_type']) ?></p>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong style="font-size: 1.2rem;">₹<?= number_format($booking['total_amount'], 2) ?></strong>
                                <span class="badge" style="background: <?= $booking['status'] === 'confirmed' ? '#4CAF50' : '#FF6B6B' ?>; color: white;">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
