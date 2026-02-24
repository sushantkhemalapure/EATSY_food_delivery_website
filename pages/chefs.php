<?php
require_once '../backend/config/config.php';

$db = Database::getInstance()->getConnection();

// Get filter parameters
$specialty = $_GET['specialty'] ?? '';
$city = $_GET['city'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT * FROM chefs WHERE is_available = 1";
$params = [];

if ($specialty) {
    $sql .= " AND specialty LIKE ?";
    $params[] = "%$specialty%";
}

if ($city) {
    $sql .= " AND city = ?";
    $params[] = $city;
}

if ($search) {
    $sql .= " AND (name LIKE ? OR bio LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY rating DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$chefs = $stmt->fetchAll();

// Get available cities
$cities = $db->query("SELECT DISTINCT city FROM chefs ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hire a Chef - EATSY</title>
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
                    <li><a href="my-bookings.php">My Bookings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero -->
    <div style="background: linear-gradient(135deg, #FFE66D, #4ECDC4); padding: 3rem 2rem; text-align: center; color: white;">
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Hire Professional Chefs</h1>
        <p style="font-size: 1.2rem;">Perfect for parties, events, or personal cooking sessions</p>
    </div>

    <div class="container">
        <h2 class="section-title">Browse Chefs</h2>
        
        <!-- Filters -->
        <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
            <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Chef name..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    <label for="specialty">Specialty</label>
                    <select id="specialty" name="specialty" class="form-control">
                        <option value="">All Specialties</option>
                        <option value="Italian" <?= $specialty === 'Italian' ? 'selected' : '' ?>>Italian</option>
                        <option value="Indian" <?= $specialty === 'Indian' ? 'selected' : '' ?>>Indian</option>
                        <option value="Japanese" <?= $specialty === 'Japanese' ? 'selected' : '' ?>>Japanese</option>
                        <option value="French" <?= $specialty === 'French' ? 'selected' : '' ?>>French</option>
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
                <a href="chefs.php" class="btn btn-outline">Clear</a>
            </form>
        </div>

        <!-- Results -->
        <?php if (count($chefs) === 0): ?>
            <div class="alert alert-info">
                No chefs found matching your criteria. Try adjusting your filters.
            </div>
        <?php else: ?>
            <p style="color: #666; margin-bottom: 1rem;">Found <?= count($chefs) ?> chef(s)</p>
            
            <div class="grid">
                <?php foreach ($chefs as $chef): ?>
                <div class="card chef-card" onclick="window.location.href='chef.php?id=<?= $chef['id'] ?>'">
                    <div class="card-content">
                        <img src="../frontend/images/<?= htmlspecialchars($chef['image']) ?>" alt="<?= htmlspecialchars($chef['name']) ?>">
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
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 50px; margin-bottom: 1rem;">
                <p>Professional chefs for your events</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../frontend/js/main.js"></script>
</body>
</html>
