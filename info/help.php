<?php require_once '../backend/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - EATSY</title>
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.php"><img src="../frontend/images/logo.png" alt="EATSY Logo"></a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php">Back</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem; max-width: 800px;">
        <h1>Help & Support</h1>
        <p style="font-size: 1.1rem;">We're here to help! Choose one of the options below:</p>
        <div style="display: grid; gap: 2rem; margin-top: 2rem;">
            <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 10px;">
                <h3>📞 Call Us</h3>
                <p>+91 1-800-EATSY-1 (Available 9 AM - 9 PM)</p>
            </div>
            <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 10px;">
                <h3>📧 Email Support</h3>
                <p>support@eatsy.com (Response within 24 hours)</p>
            </div>
            <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 10px;">
                <h3>❓ <a href="faq.php" style="color: #FF6B6B;">View FAQ</a></h3>
                <p>Find answers to common questions</p>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
