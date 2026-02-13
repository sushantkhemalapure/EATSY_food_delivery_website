<?php
require_once '../backend/config/config.php';

if (!isRestaurant()) {
    redirect('../pages/restaurant-login.php');
}

$db = Database::getInstance()->getConnection();
$restaurant_id = getRestaurantId();

// Get restaurant stats
$stmt = $db->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_revenue 
                      FROM orders WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$stats = $stmt->fetch();

// Get recent orders
$stmt = $db->prepare("SELECT o.*, u.name as customer_name, u.phone as customer_phone 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      WHERE o.restaurant_id = ? 
                      ORDER BY o.order_date DESC LIMIT 10");
$stmt->execute([$restaurant_id]);
$orders = $stmt->fetchAll();

// Get menu items count
$stmt = $db->prepare("SELECT COUNT(*) FROM menu_items WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$menu_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard - EATSY</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../../frontend/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="dashboard.php">
                    <img src="../../frontend/images/logo.png" alt="EATSY Logo">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu.php">Menu Items</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../pages/logout.php" class="btn btn-outline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h1 class="section-title">Restaurant Dashboard</h1>
        <p style="color: #666; margin-bottom: 2rem;">Welcome, <?= htmlspecialchars($_SESSION['restaurant_name']) ?>!</p>
        
        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
            <div style="background: linear-gradient(135deg, #FF6B6B, #e55555); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 20px rgba(255, 107, 107, 0.3);">
                <h3 style="font-size: 3rem; margin-bottom: 0.5rem;"><?= $stats['total_orders'] ?></h3>
                <p style="opacity: 0.9; font-size: 1.1rem;">Total Orders</p>
            </div>
            
            <div style="background: linear-gradient(135deg, #4ECDC4, #3ab8af); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 20px rgba(78, 205, 196, 0.3);">
                <h3 style="font-size: 3rem; margin-bottom: 0.5rem;">₹<?= number_format($stats['total_revenue']) ?></h3>
                <p style="opacity: 0.9; font-size: 1.1rem;">Total Revenue</p>
            </div>
            
            <div style="background: linear-gradient(135deg, #FFE66D, #f0d755); color: #333; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 20px rgba(255, 230, 109, 0.3);">
                <h3 style="font-size: 3rem; margin-bottom: 0.5rem;"><?= $menu_count ?></h3>
                <p style="opacity: 0.9; font-size: 1.1rem;">Menu Items</p>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 1.5rem;">Recent Orders</h2>
            
            <?php if (count($orders) === 0): ?>
                <div class="alert alert-info">No orders yet.</div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left;">
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Order ID</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Customer</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Phone</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Amount</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Status</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 1rem;">#<?= $order['id'] ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['customer_phone']) ?></td>
                                <td style="padding: 1rem; font-weight: 600;">₹<?= number_format($order['total_amount'], 2) ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge <?= $order['status'] === 'delivered' ? 'badge-veg' : 'badge-new' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../../frontend/js/main.js"></script>
</body>
</html>
