<?php
$restaurants = [];
$menuItems = [];
$chefs = [];

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return false;
    }
}

$configFile = __DIR__ . '/includes/config.php';
if (file_exists($configFile)) {
    require_once $configFile;

    $dbFile = __DIR__ . '/database/eatsy.db';
    $initDbFile = __DIR__ . '/includes/init_db.php';
    if (!file_exists($dbFile) && file_exists($initDbFile)) {
        require_once $initDbFile;
    }

    if (class_exists('Database')) {
        try {
            $db = Database::getInstance()->getConnection();

            $stmt = $db->query("SELECT * FROM restaurants WHERE is_active = 1 LIMIT 6");
            $restaurants = $stmt ? $stmt->fetchAll() : [];

            $stmt = $db->query("SELECT m.*, r.name as restaurant_name, r.delivery_time
                                FROM menu_items m
                                JOIN restaurants r ON m.restaurant_id = r.id
                                WHERE m.is_available = 1
                                LIMIT 12");
            $menuItems = $stmt ? $stmt->fetchAll() : [];

            $stmt = $db->query("SELECT * FROM chefs WHERE is_available = 1 LIMIT 6");
            $chefs = $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            $restaurants = [];
            $menuItems = [];
            $chefs = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EATSY - Order Food & Hire Chefs</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="EATSY Logo">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="restaurants.php">Restaurants</a></li>
                <li><a href="chefs.php">Hire a Chef</a></li>
                <li><a href="about.php">About</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="my-orders.php">My Orders</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Sign Up</a></li>
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

    <!-- Hero Section -->
    <section class="hero">
        <h1>Delicious Food, Delivered Fast</h1>
        <p>Order from the best restaurants or hire a professional chef</p>
        <div class="search-box">
            <input type="search" placeholder="Search for restaurants, dishes, or cuisines...">
            <button>Search</button>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Categories Section -->
        <section class="section">
            <h2 class="section-title">Explore Cuisines</h2>
            <div class="category-carousel" id="categoryCarousel">
                <div class="category-item">
                    <img src="images/1.png" alt="Italian">
                    <p>Italian</p>
                </div>
                <div class="category-item">
                    <img src="images/2.png" alt="Indian">
                    <p>Indian</p>
                </div>
                <div class="category-item">
                    <img src="images/3.png" alt="Japanese">
                    <p>Japanese</p>
                </div>
                <div class="category-item">
                    <img src="images/1.png" alt="Chinese">
                    <p>Chinese</p>
                </div>
                <div class="category-item">
                    <img src="images/2.png" alt="Mexican">
                    <p>Mexican</p>
                </div>
                <div class="category-item">
                    <img src="images/3.png" alt="Thai">
                    <p>Thai</p>
                </div>
            </div>
        </section>

        <!-- Featured Restaurants -->
        <section class="section">
            <h2 class="section-title">Featured Restaurants</h2>
            <div class="grid">
                <?php foreach ($restaurants as $restaurant): ?>
                <div class="card" onclick="window.location.href='restaurant.php?id=<?= $restaurant['id'] ?>'">
                    <img src="images/<?= htmlspecialchars($restaurant['image']) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>" class="card-image">
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($restaurant['name']) ?></h3>
                        <p class="card-description"><?= htmlspecialchars($restaurant['description']) ?></p>
                        <div style="margin: 0.5rem 0;">
                            <span class="badge badge-new"><?= htmlspecialchars($restaurant['cuisine_type']) ?></span>
                        </div>
                        <div class="card-footer">
                            <div class="rating">
                                <span>⭐ <?= number_format($restaurant['rating'], 1) ?></span>
                            </div>
                            <div>
                                <small>🕒 <?= htmlspecialchars($restaurant['delivery_time']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="restaurants.php" class="btn btn-primary">View All Restaurants</a>
            </div>
        </section>

        <!-- Popular Dishes -->
        <section class="section">
            <h2 class="section-title">Popular Dishes</h2>
            <div class="grid">
                <?php foreach ($menuItems as $item): ?>
                <div class="card" data-category="<?= htmlspecialchars($item['category']) ?>">
                    <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="card-image">
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="card-description"><?= htmlspecialchars($item['description']) ?></p>
                        <div style="margin: 0.5rem 0;">
                            <span class="badge <?= $item['is_vegetarian'] ? 'badge-veg' : 'badge-nonveg' ?>">
                                <?= $item['is_vegetarian'] ? '🌱 Veg' : '🍖 Non-Veg' ?>
                            </span>
                        </div>
                        <p style="color: #666; font-size: 0.9rem;">from <?= htmlspecialchars($item['restaurant_name']) ?></p>
                        <div class="card-footer">
                            <div class="price">₹<?= number_format($item['price'], 2) ?></div>
                            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', <?= $item['price'] ?>, <?= $item['restaurant_id'] ?>)">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Hire a Chef Section -->
        <section class="section" style="background: linear-gradient(135deg, #FFE66D, #FF6B6B); padding: 3rem; border-radius: 20px; text-align: center;">
            <h2 style="color: white; font-size: 2.5rem; margin-bottom: 1rem;">Need a Chef for Your Event?</h2>
            <p style="color: white; font-size: 1.2rem; margin-bottom: 2rem;">Hire professional chefs for parties, events, or personal cooking</p>
            <a href="chefs.php" class="btn" style="background: white; color: #FF6B6B; padding: 1rem 3rem; font-size: 1.1rem;">Browse Chefs</a>
        </section>

        <!-- Featured Chefs -->
        <section class="section">
            <h2 class="section-title">Featured Chefs</h2>
            <div class="grid">
                <?php foreach ($chefs as $chef): ?>
                <div class="card chef-card" onclick="window.location.href='chef.php?id=<?= $chef['id'] ?>'">
                    <div class="card-content">
                        <img src="images/<?= htmlspecialchars($chef['image']) ?>" alt="<?= htmlspecialchars($chef['name']) ?>">
                        <h3 class="card-title"><?= htmlspecialchars($chef['name']) ?></h3>
                        <p class="chef-specialty"><?= htmlspecialchars($chef['specialty']) ?></p>
                        <p class="card-description"><?= htmlspecialchars($chef['bio']) ?></p>
                        <div class="card-footer">
                            <div class="rating">
                                <span>⭐ <?= number_format($chef['rating'], 1) ?></span>
                            </div>
                            <div class="chef-rate">₹<?= number_format($chef['hourly_rate']) ?>/hr</div>
                        </div>
                        <div style="margin-top: 1rem;">
                            <span class="badge badge-new"><?= $chef['experience_years'] ?> yrs exp</span>
                            <span class="badge badge-veg">📍 <?= htmlspecialchars($chef['city']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="chefs.php" class="btn btn-secondary">View All Chefs</a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <img src="images/logo.png" alt="EATSY" style="height: 50px; margin-bottom: 1rem;">
                <p>Delicious food delivered to your doorstep</p>
            </div>
            <div class="footer-section">
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="careers.php">Careers</a></li>
                    <li><a href="restaurant-partner.php">Partner with Us</a></li>
                    <li><a href="chef-partner.php">Become a Chef</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>For Customers</h4>
                <ul>
                    <li><a href="help.php">Help & Support</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Available In</h4>
                <ul>
                    <li>Bangalore</li>
                    <li>Mumbai</li>
                    <li>Delhi</li>
                    <li>Hyderabad</li>
                    <li>Pune</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
