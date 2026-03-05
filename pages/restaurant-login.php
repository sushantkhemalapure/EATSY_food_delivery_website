<?php
require_once '../backend/config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE email = ?");
        $stmt->execute([$email]);
        $restaurant = $stmt->fetch();
        
        if ($restaurant && password_verify($password, $restaurant['password'])) {
            $_SESSION['restaurant_id'] = $restaurant['id'];
            $_SESSION['restaurant_name'] = $restaurant['name'];
            redirect('admin/dashboard.php');
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Login - EATSY</title>
    <link rel="icon" type="image/png" href="../frontend/images/logo.png">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div style="text-align: center; margin-bottom: 2rem;">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 60px;">
            </div>
            <h2>Restaurant Partner Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="restaurantLoginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <div style="text-align: center; margin-top: 2rem;">
                <p>Want to partner with us? <a href="restaurant-register.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Register Here</a></p>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php" style="color: #666; text-decoration: none;">← Back to Home</a>
            </div>
            
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 2rem;">
                <p style="margin: 0; color: #666; font-size: 0.9rem;"><strong>Demo Credentials:</strong></p>
                <p style="margin: 0.5rem 0 0; color: #666; font-size: 0.9rem;">Email: italian@eatsy.com<br>Password: demo123</p>
            </div>
        </div>
    </div>
    
    <script src="../frontend/js/main.js"></script>
</body>
</html>
