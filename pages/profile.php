<?php
require_once '../backend/config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();
$uid = getUserId();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);

    $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, address = ?, city = ? WHERE id = ?");
    if ($stmt->execute([$name, $phone, $address, $city, $uid])) {
        $user = compact('id', 'name', 'email', 'phone', 'address', 'city');
        $success = "Profile updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - EATSY</title>
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
                <li><a href="my-orders.php">My Orders</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <div style="max-width: 600px; margin: 0 auto;">
            <h2>My Profile</h2>

            <?php if (isset($success)): ?>
                <div class="badge badge-new" style="background: #4CAF50; color: white; display: block; text-align: center; padding: 1rem; margin-bottom: 1rem; border-radius: 5px;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form" id="profileForm">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (Cannot be changed)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
