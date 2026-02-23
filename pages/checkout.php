<?php
require_once '../backend/config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = Database::getInstance()->getConnection();
$error = '';
$success = '';

// Get user details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getUserId()]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_address = sanitize($_POST['delivery_address']);
    $payment_method = sanitize($_POST['payment_method']);

    if (empty($delivery_address)) {
        $error = 'Please provide a delivery address';
    } else {
        // Get cart data from POST (sent via AJAX)
        $cart_data = isset($_POST['cartData']) ? json_decode($_POST['cartData'], true) : [];

        if (empty($cart_data) || empty($cart_data['items'])) {
            $error = 'Your cart is empty';
        } else {
            try {
                $db->beginTransaction();

                // Get restaurant ID from first item
                $restaurant_id = $cart_data['items'][0]['restaurant_id'];

                // Create order
                $stmt = $db->prepare("INSERT INTO orders (user_id, restaurant_id, total_amount, delivery_address, status, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    getUserId(),
                    $restaurant_id,
                    $cart_data['total'],
                    $delivery_address,
                    'pending',
                    $payment_method
                ]);

                $order_id = $db->lastInsertId();

                // Insert order items
                $stmt = $db->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
                foreach ($cart_data['items'] as $item) {
                    $stmt->execute([
                        $order_id,
                        $item['id'],
                        $item['quantity'],
                        $item['price']
                    ]);
                }

                $db->commit();
                $success = 'Order placed successfully! Order ID: #' . $order_id;
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Failed to place order: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EATSY</title>
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
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h1 class="section-title">Checkout</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
                <br>
                <a href="my-orders.php" style="color: var(--success-color); font-weight: 600;">View your orders</a>
            </div>
        <?php else: ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Checkout Form -->
            <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 1.5rem;">Delivery Details</h2>
                
                <form method="POST" action="" id="checkoutForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?= htmlspecialchars($user['name']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_address">Delivery Address *</label>
                        <textarea id="delivery_address" name="delivery_address" class="form-control" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" 
                               value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                    </div>
                    
                    <h3 style="margin: 2rem 0 1rem;">Payment Method</h3>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; margin-bottom: 1rem;">
                            <input type="radio" name="payment_method" value="cash" checked style="margin-right: 1rem;">
                            <div>
                                <strong>Cash on Delivery</strong>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">Pay when you receive your order</p>
                            </div>
                        </label>
                        
                        <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; margin-bottom: 1rem;">
                            <input type="radio" name="payment_method" value="online" style="margin-right: 1rem;">
                            <div>
                                <strong>Online Payment</strong>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">UPI, Card, Net Banking</p>
                            </div>
                        </label>
                    </div>
                    
                    <button type="button" onclick="placeOrder()" class="btn btn-primary" style="width: 100%;">
                        Place Order
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div>
                <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.1); position: sticky; top: 100px;">
                    <h2 style="margin-bottom: 1.5rem;">Order Summary</h2>
                    
                    <div id="orderItems"></div>
                    
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
                            <span>Taxes (5%):</span>
                            <span id="taxes">₹0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #eee;">
                            <span>Total:</span>
                            <span id="total">₹0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 EATSY. All rights reserved.</p>
        </div>
    </footer>

    <script src="../frontend/js/main.js"></script>
    <script>
        function displayOrderSummary() {
            const cartItems = cart.items;
            const itemsDiv = document.getElementById('orderItems');
            
            if (cartItems.length === 0) {
                window.location.href = 'cart.php';
                return;
            }
            
            let html = '';
            cartItems.forEach(item => {
                html += `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                        <div>
                            <h4 style="margin-bottom: 0.3rem;">${item.name}</h4>
                            <p style="color: #666; margin: 0;">Qty: ${item.quantity} × ₹${item.price.toFixed(2)}</p>
                        </div>
                        <div style="font-weight: bold;">₹${(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                `;
            });
            
            itemsDiv.innerHTML = html;
            
            // Calculate totals
            const subtotal = cart.getTotal();
            const deliveryFee = 40;
            const taxes = subtotal * 0.05;
            const total = subtotal + deliveryFee + taxes;
            
            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('taxes').textContent = '₹' + taxes.toFixed(2);
            document.getElementById('total').textContent = '₹' + total.toFixed(2);
        }
        
        async function placeOrder() {
            const form = document.getElementById('checkoutForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (cart.items.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const formData = new FormData(form);
            const cartData = {
                items: cart.items,
                subtotal: cart.getTotal(),
                deliveryFee: 40,
                taxes: cart.getTotal() * 0.05,
                total: cart.getTotal() + 40 + (cart.getTotal() * 0.05)
            };

            // Add cart data to form
            formData.append('cartData', JSON.stringify(cartData));

            try {
                // Disable button
                const btn = event.target;
                btn.disabled = true;
                btn.textContent = 'Processing...';

                // Submit form
                const response = await fetch('checkout.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    // Clear cart from localStorage before reload
                    cart.clear();
                    // Reload to show success message
                    setTimeout(() => {
                        window.location.href = 'checkout.php';
                    }, 500);
                } else {
                    alert('Order submission failed. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Place Order';
                }
            } catch (error) {
                console.error('Order error:', error);
                alert('Error placing order: ' + error.message);
                const btn = event.target;
                btn.disabled = false;
                btn.textContent = 'Place Order';
            }
        }
        
        document.addEventListener('DOMContentLoaded', displayOrderSummary);
    </script>
</body>
</html>
