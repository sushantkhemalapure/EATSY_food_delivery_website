<?php
require_once '../backend/config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            redirect('index.php');
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
    <title>Login - EATSY</title>
    <link rel="icon" type="image/png" href="../frontend/images/logo.png">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div style="text-align: center; margin-bottom: 2rem;">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 60px;">
            </div>
            <h2>Welcome Back</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <a href="forgot-password.php" style="color: var(--primary-color); text-decoration: none;">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <div style="text-align: center; margin-top: 2rem;">
                <p>Don't have an account? <a href="register.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Sign Up</a></p>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <p><a href="restaurant-login.php" style="color: var(--secondary-color); text-decoration: none;">Restaurant Login</a></p>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php" style="color: #666; text-decoration: none;">← Back to Home</a>
            </div>
        </div>
    </div>
    
    <script src="../frontend/js/main.js"></script>
</body>
</html>
