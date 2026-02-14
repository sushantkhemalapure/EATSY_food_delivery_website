<?php
require_once '../backend/config/config.php';

if (!isRestaurant()) {
    redirect('../pages/restaurant-login.php');
}

$db = Database::getInstance()->getConnection();
$restaurant_id = getRestaurantId();

// Get all orders for this restaurant
$stmt = $db->prepare("
    SELECT o.*, u.name as customer_name, u.phone as customer_phone, u.address
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.restaurant_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$restaurant_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - EATSY</title>
    <link rel="stylesheet" href="../../frontend/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><a href="dashboard.php"><img src="../../frontend/images/logo.png" alt="EATSY Logo"></a></div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu.php">Menu Items</a></li>
                <li><a href="orders.php" class="active">Orders</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h1 class="section-title">Order Management</h1>

        <?php if (empty($orders)): ?>
            <div style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: #666;">No orders yet</p>
            </div>
        <?php else: ?>
            <div style="display: grid; gap: 1.5rem;">
                <?php foreach ($orders as $order): ?>
                    <div style="background: white; padding: 2rem; border-radius: 10px; border-left: 5px solid var(--primary-color); box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: start;">
                            <div>
                                <p style="color: #666; font-size: 0.9rem;">Order ID</p>
                                <h3 style="margin: 0.5rem 0;">#<?= $order['id'] ?></h3>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <div>
                                    <p style="color: #666; font-size: 0.9rem;">Customer</p>
                                    <p style="margin: 0.5rem 0;"><?= htmlspecialchars($order['customer_name']) ?></p>
                                    <p style="color: #666; margin: 0.3rem 0; font-size: 0.9rem;">📞 <?= htmlspecialchars($order['customer_phone']) ?></p>
                                </div>
                                <div>
                                    <p style="color: #666; font-size: 0.9rem;">Delivery Address</p>
                                    <p style="margin: 0.5rem 0;"><?= htmlspecialchars($order['delivery_address']) ?></p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <p style="color: #666; font-size: 0.9rem;">Amount</p>
                                <p style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin: 0.5rem 0;">₹<?= number_format($order['total_amount'], 2) ?></p>
                                <span class="badge" style="background: <?= $order['status'] === 'delivered' ? '#4CAF50' : ($order['status'] === 'pending' ? '#FF6B6B' : '#4ECDC4') ?>; color: white;">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </div>
                        </div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; color: #666; font-size: 0.9rem;">
                            Order Date: <?= date('d M Y, H:i', strtotime($order['order_date'])) ?> | Payment: <?= htmlspecialchars($order['payment_method']) ?>
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
