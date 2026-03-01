<?php
require_once '../backend/config/config.php';

$restaurant_id = $_GET['id'] ?? 0;
$db = Database::getInstance()->getConnection();

// Get restaurant details
$stmt = $db->prepare("SELECT * FROM restaurants WHERE id = ? AND is_active = 1");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

if (!$restaurant) {
    redirect('restaurants.php');
}

// Get menu items
$stmt = $db->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? AND is_available = 1 ORDER BY category, name");
$stmt->execute([$restaurant_id]);
$menuItems = $stmt->fetchAll();

// Group by category
$categories = [];
foreach ($menuItems as $item) {
    $categories[$item['category']][] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> - EATSY</title>
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
                <li><a href="restaurants.php">Restaurants</a></li>
                <li><a href="chefs.php">Hire a Chef</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="my-orders.php">My Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                <?php endif; ?>
                <li>
                    <a href="cart.php" class="cart-icon">
                        🛒
                        <span class="cart-count">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Restaurant Header -->
    <div style="background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('images/<?= htmlspecialchars($restaurant['image']) ?>'); 
                background-size: cover; background-position: center; color: white; padding: 4rem 2rem;">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 1rem;"><?= htmlspecialchars($restaurant['name']) ?></h1>
            <p style="font-size: 1.2rem; margin-bottom: 1rem;"><?= htmlspecialchars($restaurant['description']) ?></p>
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div>
                    <span style="font-size: 1.5rem;">⭐ <?= number_format($restaurant['rating'], 1) ?></span>
                    <span style="opacity: 0.9;"> Rating</span>
                </div>
                <div>
                    <span style="font-size: 1.5rem;">🕒 <?= htmlspecialchars($restaurant['delivery_time']) ?></span>
                    <span style="opacity: 0.9;"> Delivery</span>
                </div>
                <div>
                    <span style="font-size: 1.5rem;">🍽️ <?= htmlspecialchars($restaurant['cuisine_type']) ?></span>
                    <span style="opacity: 0.9;"> Cuisine</span>
                </div>
                <div>
                    <span style="font-size: 1.5rem;">📍 <?= htmlspecialchars($restaurant['city']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div style="display: flex; gap: 2rem; margin-top: 2rem;">
            <!-- Menu Categories Sidebar -->
            <div style="flex: 0 0 200px;">
                <div style="position: sticky; top: 100px; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1rem;">Menu</h3>
                    <ul style="list-style: none;">
                        <?php foreach (array_keys($categories) as $category): ?>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="#<?= urlencode($category) ?>" style="color: var(--dark-color); text-decoration: none; font-weight: 500;">
                                <?= htmlspecialchars($category) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Menu Items -->
            <div style="flex: 1;">
                <?php foreach ($categories as $category => $items): ?>
                <section id="<?= urlencode($category) ?>" style="margin-bottom: 3rem;">
                    <h2 style="font-size: 2rem; margin-bottom: 1.5rem; color: var(--dark-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">
                        <?= htmlspecialchars($category) ?>
                    </h2>
                    
                    <div class="grid">
                        <?php foreach ($items as $item): ?>
                        <div class="card">
                            <img src="../frontend/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="card-image">
                            <div class="card-content">
                                <h3 class="card-title"><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="card-description"><?= htmlspecialchars($item['description']) ?></p>
                                <div style="margin: 0.5rem 0;">
                                    <span class="badge <?= $item['is_vegetarian'] ? 'badge-veg' : 'badge-nonveg' ?>">
                                        <?= $item['is_vegetarian'] ? '🌱 Veg' : '🍖 Non-Veg' ?>
                                    </span>
                                </div>
                                <div class="card-footer">
                                    <div class="price">₹<?= number_format($item['price'], 2) ?></div>
                                    <button class="btn btn-primary" onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', <?= $item['price'] ?>, <?= $restaurant_id ?>)">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 50px; margin-bottom: 1rem;">
                <p>Delicious food delivered to your doorstep</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../frontend/js/main.js"></script>
</body>
</html>
