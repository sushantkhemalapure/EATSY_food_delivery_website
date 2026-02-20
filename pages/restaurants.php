<?php
require_once '../backend/config/config.php';

$db = Database::getInstance()->getConnection();

// Get filter parameters
$cuisine = $_GET['cuisine'] ?? '';
$city = $_GET['city'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT * FROM restaurants WHERE is_active = 1";
$params = [];

if ($cuisine) {
    $sql .= " AND cuisine_type LIKE ?";
    $params[] = "%$cuisine%";
}

if ($city) {
    $sql .= " AND city = ?";
    $params[] = $city;
}

if ($search) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY rating DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$restaurants = $stmt->fetchAll();

// Get available cities
$cities = $db->query("SELECT DISTINCT city FROM restaurants ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - EATSY</title>
    <link rel="icon" type="image/png" href="../frontend/images/logo.png">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <!-- Navigation (same as index.php) -->
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

    <div class="container">
        <h1 class="section-title">All Restaurants</h1>
        
        <!-- Filters -->
        <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
            <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Restaurant name..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    <label for="cuisine">Cuisine</label>
                    <select id="cuisine" name="cuisine" class="form-control">
                        <option value="">All Cuisines</option>
                        <option value="Italian" <?= $cuisine === 'Italian' ? 'selected' : '' ?>>Italian</option>
                        <option value="Indian" <?= $cuisine === 'Indian' ? 'selected' : '' ?>>Indian</option>
                        <option value="Japanese" <?= $cuisine === 'Japanese' ? 'selected' : '' ?>>Japanese</option>
                        <option value="Chinese" <?= $cuisine === 'Chinese' ? 'selected' : '' ?>>Chinese</option>
                    </select>
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    <label for="city">City</label>
                    <select id="city" name="city" class="form-control">
                        <option value="">All Cities</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>" <?= $city === $c ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="restaurants.php" class="btn btn-outline">Clear</a>
            </form>
        </div>

        <!-- Results -->
        <?php if (count($restaurants) === 0): ?>
            <div class="alert alert-info">
                No restaurants found matching your criteria. Try adjusting your filters.
            </div>
        <?php else: ?>
            <p style="color: #666; margin-bottom: 1rem;">Found <?= count($restaurants) ?> restaurant(s)</p>
            
            <div class="grid">
                <?php foreach ($restaurants as $restaurant): ?>
                <div class="card" onclick="window.location.href='restaurant.php?id=<?= $restaurant['id'] ?>'">
                    <img src="../frontend/images/<?= htmlspecialchars($restaurant['image']) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>" class="card-image">
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($restaurant['name']) ?></h3>
                        <p class="card-description"><?= htmlspecialchars($restaurant['description']) ?></p>
                        <div style="margin: 0.5rem 0;">
                            <span class="badge badge-new"><?= htmlspecialchars($restaurant['cuisine_type']) ?></span>
                            <span class="badge badge-veg">📍 <?= htmlspecialchars($restaurant['city']) ?></span>
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
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 50px; margin-bottom: 1rem;">
                <p>Delicious food delivered to your doorstep</p>
            </div>
            <div class="footer-section">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../frontend/js/main.js"></script>
</body>
</html>
