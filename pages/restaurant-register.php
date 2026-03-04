<?php
require_once '../backend/config/config.php';

if (isRestaurant()) {
    redirect('admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $db = Database::getInstance()->getConnection();

        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO restaurants (name, email, phone, password, address, city) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hashed_password, '', '']);

            $_SESSION['restaurant_id'] = $db->lastInsertId();
            redirect('admin/dashboard.php');
        } catch (PDOException $e) {
            $error = "Registration failed. Email may already be registered.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Registration - EATSY</title>
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.php"><img src="../frontend/images/logo.png" alt="EATSY Logo"></a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="restaurant-login.php">Restaurant Login</a></li>
            </ul>
        </div>
    </nav>

    <div style="max-width: 500px; margin: 2rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 10px;">
        <h2>Register Your Restaurant</h2>

        <?php if ($error): ?>
            <div style="background: #f44336; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                ✗ <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form" id="restaurantRegisterForm">
            <div class="form-group">
                <label>Restaurant Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Register Restaurant</button>
            <p style="text-align: center; margin-top: 1rem;">
                Already registered? <a href="restaurant-login.php">Login here</a>
            </p>
        </form>
    </div>

    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
