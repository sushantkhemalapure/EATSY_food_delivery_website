<?php
require_once '../backend/config/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $db = Database::getInstance()->getConnection();

    // Check if user exists
    $stmt = $db->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $success = "Password reset instructions have been sent to " . htmlspecialchars($email) . "@eatsy.com";
    } else {
        $error = "Email not found in our system.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EATSY</title>
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.php"><img src="../frontend/images/logo.png" alt="EATSY Logo"></a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            </ul>
        </div>
    </nav>

    <div style="max-width: 400px; margin: 3rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 10px;">
        <h2>Reset Your Password</h2>
        <p style="color: #666;">Enter your email address and we'll send you instructions to reset your password.</p>

        <?php if (isset($success)): ?>
            <div style="background: #4CAF50; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                ✓ <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #f44336; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                ✗ <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form" id="forgotPasswordForm">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Send Reset Link</button>
            <p style="text-align: center; margin-top: 1rem;">
                <a href="login.php">Back to Login</a>
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
