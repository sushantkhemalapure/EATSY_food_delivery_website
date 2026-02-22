<?php
require_once '../backend/config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();
$uid = getUserId();

// Fetch user's orders
$stmt = $db->prepare("
    SELECT o.*, r.name as restaurant_name, r.image
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$uid]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EATSY</title>
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
                <li><a href="restaurants.php">Restaurants</a></li>
                <li><a href="chefs.php">Hire a Chef</a></li>
                <li><a href="my-orders.php" class="active">My Orders</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h2>My Orders</h2>

        <?php if (empty($orders)): ?>
            <div style="text-align: center; padding: 3rem;">
                <p>No orders yet. <a href="restaurants.php" class="btn btn-primary">Start ordering</a></p>
            </div>
        <?php else: ?>
            <div style="display: grid; gap: 1.5rem;">
                <?php foreach ($orders as $order): ?>
                    <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <h3><?= htmlspecialchars($order['restaurant_name']) ?></h3>
                                <p style="color: #666; font-size: 0.9rem;">Order #<?= $order['id'] ?></p>
                            </div>
                            <span class="badge" style="background: <?= $order['status'] === 'delivered' ? '#4CAF50' : ($order['status'] === 'cancelled' ? '#f44336' : '#FF6B6B') ?>; color: white;">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div>
                                <p style="color: #666; font-size: 0.9rem;">Delivery Address</p>
                                <p><?= htmlspecialchars($order['delivery_address']) ?></p>
                            </div>
                            <div>
                                <p style="color: #666; font-size: 0.9rem;">Order Date</p>
                                <p><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></p>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                            <div>
                                <strong style="font-size: 1.2rem;">₹<?= number_format($order['total_amount'], 2) ?></strong>
                            </div>
                            <div>
                                <p style="color: #666; font-size: 0.9rem;">Payment: <?= htmlspecialchars($order['payment_method']) ?></p>
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
