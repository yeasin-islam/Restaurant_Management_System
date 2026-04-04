// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }
    
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(function(alert) {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Registration form validation
    const registerForm = document.querySelector('.auth-form');
    if (registerForm) {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        
        if (passwordField && confirmPasswordField) {
            registerForm.addEventListener('submit', function(e) {
                if (passwordField.value !== confirmPasswordField.value) {
                    e.preventDefault();
                    showNotification('Passwords do not match!', 'error');
                }
                
                if (passwordField.value.length < 6) {
                    e.preventDefault();
                    showNotification('Password must be at least 6 characters!', 'error');
                }
            });
        }
    }
    
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.value < 0) {
                this.value = 0;
            }
            if (this.value > 99) {
                this.value = 99;
            }
        });
    });
    
    const deleteButtons = document.querySelectorAll('[onclick*="confirm"]');
    
    const starRating = document.querySelector('.star-rating');
    
    if (starRating) {
        const stars = starRating.querySelectorAll('label');
        const inputs = starRating.querySelectorAll('input');
        
        stars.forEach(function(star, index) {
            star.addEventListener('mouseenter', function() {
                // Highlight stars on hover
                for (let i = stars.length - 1; i >= index; i--) {
                    stars[i].style.color = '#ffc107';
                }
            });
            
            star.addEventListener('mouseleave', function() {
                // Reset to checked state
                stars.forEach(function(s) {
                    s.style.color = '#ddd';
                });
                
                inputs.forEach(function(input, i) {
                    if (input.checked) {
                        for (let j = stars.length - 1; j >= i; j--) {
                            stars[j].style.color = '#ffc107';
                        }
                    }
                });
            });
        });
    }
    
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    const images = document.querySelectorAll('img');
    
    images.forEach(function(img) {
        img.addEventListener('error', function() {
            // If image fails to load, show default image
            if (!this.src.includes('default.jpg')) {
                this.src = '../assets/images/default.jpg';
            }
        });
    });
    
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(function(input) {
        if (!input.min) {
            input.min = today;
        }
    });
    
    const priceInputs = document.querySelectorAll('input[name="price"]');
    
    priceInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    
    // Print Order Functionality
    window.printOrder = function(orderId) {
        window.print();
    };
    
    // Loading State for Forms
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Please wait...';
            }
        });
    });
    

    // Table Row Hover Effect
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    
    tableRows.forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
});

// Utility Functions
/**
 * Show notification message
 * @param {string} message - The message to display
 * @param {string} type - Type of notification (success, error, warning)
 */
function showNotification(message, type) {
    // Remove existing notifications
    const existingNotification = document.querySelector('.js-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'alert alert-' + type + ' js-notification';
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '100px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '250px';
    notification.style.animation = 'slideIn 0.3s ease';
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(function() {
        notification.style.opacity = '0';
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Confirm action before proceeding
 * @param {string} message - Confirmation message
 * @returns {boolean} - User's choice
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * Format currency
 * @param {number} amount - The amount to format
 * @returns {string} - Formatted currency string
 */
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

/**
 * Format date
 * @param {string} dateString - Date string to format
 * @returns {string} - Formatted date string
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Add CSS animation for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
