// Shopping Cart Management
class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
        this.updateCartCount();
    }

    addItem(item) {
        // Check if adding item from different restaurant
        if (this.items.length > 0 && this.items[0].restaurant_id !== item.restaurant_id) {
            if (confirm('You have items from another restaurant. Clear your cart and add this item?')) {
                this.clear();
            } else {
                this.showNotification('Add items from the same restaurant only');
                return;
            }
        }

        const existingItem = this.items.find(i => i.id === item.id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({ ...item, quantity: 1 });
        }
        this.save();
        this.showNotification('Item added to cart!');
    }

    removeItem(itemId) {
        this.items = this.items.filter(i => i.id !== itemId);
        this.save();
        this.updateCartCount();
    }

    updateQuantity(itemId, quantity) {
        const item = this.items.find(i => i.id === itemId);
        if (item) {
            item.quantity = quantity;
            if (quantity <= 0) {
                this.removeItem(itemId);
            } else {
                this.save();
            }
        }
    }

    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    clear() {
        this.items = [];
        this.save();
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
        this.updateCartCount();
    }

    updateCartCount() {
        const count = this.items.reduce((sum, item) => sum + item.quantity, 0);
        const cartCountEl = document.querySelector('.cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = count;
            cartCountEl.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '3000';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize cart
const cart = new Cart();

// Add to cart functionality
function addToCart(itemId, itemName, itemPrice, restaurantId) {
    cart.addItem({
        id: itemId,
        name: itemName,
        price: itemPrice,
        restaurant_id: restaurantId
    });
}

// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal on outside click
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'var(--danger-color)';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    return isValid;
}

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Phone validation
function validatePhone(phone) {
    const re = /^[0-9]{10}$/;
    return re.test(phone.replace(/\s/g, ''));
}

// Search functionality
function searchItems(searchTerm) {
    searchTerm = searchTerm.toLowerCase();
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.card-description')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Filter by category
function filterByCategory(category) {
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        const cardCategory = card.dataset.category;
        if (category === 'all' || cardCategory === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Smooth scroll
function smoothScroll(targetId) {
    const element = document.getElementById(targetId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Image carousel for categories
function scrollCarousel(direction, carouselId) {
    const carousel = document.getElementById(carouselId);
    if (carousel) {
        const scrollAmount = 300;
        carousel.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
}

// Format currency
function formatCurrency(amount) {
    return '₹' + amount.toFixed(2);
}

// Loading state
function setLoading(elementId, isLoading) {
    const element = document.getElementById(elementId);
    if (element) {
        if (isLoading) {
            element.innerHTML = '<div class="spinner"></div>';
        }
    }
}

// AJAX helper
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return null;
    }
}

// Show/hide password
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    cart.updateCartCount();
    
    // Add search event listener
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            searchItems(e.target.value);
        }, 300));
    }
    
    // Form submission handling - only validate forms that have IDs and require validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Only validate checkout and special forms, not login/register
            if (this.id === 'checkoutForm' || (this.id && this.classList.contains('validate-form'))) {
                if (!validateForm(this.id)) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                }
            }
        });
    });
});

// Rating stars
function renderStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let stars = '';
    
    for (let i = 0; i < fullStars; i++) {
        stars += '★';
    }
    if (hasHalfStar) {
        stars += '☆';
    }
    
    return stars;
}

// Time ago function
function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    for (const [name, value] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / value);
        if (interval >= 1) {
            return interval + ' ' + name + (interval === 1 ? '' : 's') + ' ago';
        }
    }
    
    return 'Just now';
}
