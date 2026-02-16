<?php
require_once '../backend/config/config.php';

if (!isRestaurant()) {
    redirect('../pages/restaurant-login.php');
}

$db = Database::getInstance()->getConnection();
$restaurant_id = getRestaurantId();
$error = '';
$success = '';

// Get restaurant details
$stmt = $db->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $description = sanitize($_POST['description']);
    $cuisine_type = sanitize($_POST['cuisine_type']);
    $delivery_time = sanitize($_POST['delivery_time']);

    if (empty($name)) {
        $error = 'Restaurant name is required.';
    } else {
        $stmt = $db->prepare("UPDATE restaurants SET name = ?, email = ?, phone = ?, description = ?, cuisine_type = ?, delivery_time = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $phone, $description, $cuisine_type, $delivery_time, $restaurant_id])) {
            $success = 'Profile updated successfully!';
            $_SESSION['restaurant_name'] = $name;
            $restaurant = compact('id', 'name', 'email', 'phone', 'description', 'cuisine_type', 'delivery_time');
        } else {
            $error = 'Error updating profile.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Profile - EATSY</title>
    <link rel="stylesheet" href="../../frontend/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><a href="dashboard.php"><img src="../../frontend/images/logo.png" alt="EATSY Logo"></a></div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu.php">Menu Items</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem; max-width: 600px;">
        <h1 class="section-title">Restaurant Profile</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="form" id="restaurantProfileForm">
            <div class="form-group">
                <label>Restaurant Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($restaurant['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($restaurant['email']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($restaurant['phone']) ?>">
            </div>
            <div class="form-group">
                <label>Cuisine Type</label>
                <input type="text" name="cuisine_type" value="<?= htmlspecialchars($restaurant['cuisine_type']) ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($restaurant['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Delivery Time (e.g., 30-40 mins)</label>
                <input type="text" name="delivery_time" value="<?= htmlspecialchars($restaurant['delivery_time'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
        </form>
    </div>

    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
