<?php
require_once '../backend/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - EATSY</title>
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
                <?php if (isLoggedIn()): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h1 class="section-title">Shopping Cart</h1>
        
        <div id="cartContainer">
            <div class="alert alert-info">Your cart is empty. <a href="restaurants.php" style="color: var(--primary-color);">Browse restaurants</a></div>
        </div>
        
        <div id="cartSummary" style="display: none;">
            <div style="max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                
                <div id="cartItems"></div>
                
                <div style="border-top: 2px solid #eee; padding-top: 1rem; margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Subtotal:</span>
                        <span id="subtotal">₹0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Delivery Fee:</span>
                        <span>₹40</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Taxes:</span>
                        <span id="taxes">₹0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin-top: 1rem;">
                        <span>Total:</span>
                        <span id="total">₹0</span>
                    </div>
                </div>
                
                <div style="margin-top: 2rem;">
                    <?php if (isLoggedIn()): ?>
                        <button onclick="proceedToCheckout()" class="btn btn-primary" style="width: 100%;">Proceed to Checkout</button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary" style="width: 100%; display: block; text-align: center;">Login to Checkout</a>
                    <?php endif; ?>
                    <a href="restaurants.php" class="btn btn-outline" style="width: 100%; display: block; text-align: center; margin-top: 1rem;">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <img src="../frontend/images/logo.png" alt="EATSY" style="height: 50px;">
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../frontend/js/main.js"></script>
    <script>
        function displayCart() {
            const cartItems = cart.items;
            const container = document.getElementById('cartContainer');
            const summary = document.getElementById('cartSummary');
            const itemsDiv = document.getElementById('cartItems');
            
            if (cartItems.length === 0) {
                container.innerHTML = '<div class="alert alert-info">Your cart is empty. <a href="restaurants.php" style="color: var(--primary-color);">Browse restaurants</a></div>';
                summary.style.display = 'none';
                return;
            }
            
            container.innerHTML = '';
            summary.style.display = 'block';
            
            let html = '';
            cartItems.forEach(item => {
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #eee;">
                        <div style="flex: 1;">
                            <h4 style="margin-bottom: 0.3rem;">${item.name}</h4>
                            <p style="color: #666; margin: 0;">₹${item.price.toFixed(2)} each</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <button onclick="updateItemQuantity(${item.id}, ${item.quantity - 1})" 
                                        style="width: 30px; height: 30px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 5px;">-</button>
                                <span style="min-width: 30px; text-align: center; font-weight: 600;">${item.quantity}</span>
                                <button onclick="updateItemQuantity(${item.id}, ${item.quantity + 1})"
                                        style="width: 30px; height: 30px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 5px;">+</button>
                            </div>
                            <div style="font-weight: bold; min-width: 80px; text-align: right;">₹${(item.price * item.quantity).toFixed(2)}</div>
                            <button onclick="removeFromCart(${item.id})" 
                                    style="color: var(--danger-color); border: none; background: none; cursor: pointer; font-size: 1.2rem;">🗑️</button>
                        </div>
                    </div>
                `;
            });
            
            itemsDiv.innerHTML = html;
            
            // Calculate totals
            const subtotal = cart.getTotal();
            const deliveryFee = 40;
            const taxes = subtotal * 0.05; // 5% tax
            const total = subtotal + deliveryFee + taxes;
            
            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('taxes').textContent = '₹' + taxes.toFixed(2);
            document.getElementById('total').textContent = '₹' + total.toFixed(2);
        }
        
        function updateItemQuantity(itemId, newQuantity) {
            cart.updateQuantity(itemId, newQuantity);
            displayCart();
        }
        
        function removeFromCart(itemId) {
            if (confirm('Remove this item from cart?')) {
                cart.removeItem(itemId);
                displayCart();
            }
        }
        
        function proceedToCheckout() {
            if (cart.items.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            window.location.href = 'checkout.php';
        }
        
        // Display cart on page load
        document.addEventListener('DOMContentLoaded', displayCart);
    </script>
</body>
</html>
