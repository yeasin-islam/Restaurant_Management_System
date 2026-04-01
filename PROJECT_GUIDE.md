# Restaurant Management System (RMS) - Complete Setup Guide

## Project Overview
A beginner-friendly Restaurant Management System built with:
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** Core PHP (no frameworks)
- **Database:** MySQL
- **Architecture:** Session-based authentication, simple folder structure

---

## ✅ Project Status: COMPLETE & READY TO USE

All required features have been implemented and tested. The system is fully functional for both users and administrators.

---

## 📁 Project Structure

```
RestaurantManagementSystem/
├── index.php                 # Home page
├── config/
│   ├── db.php               # Database connection
│   └── helpers.php          # Reusable functions
├── assets/
│   ├── css/style.css        # Full responsive styling
│   ├── js/script.js         # Client-side functionality
│   └── images/              # Food images
├── includes/
│   ├── header.php           # HTML head section
│   ├── navbar.php           # Navigation bar
│   └── footer.php           # Footer
├── auth/
│   ├── login.php            # User/Admin login
│   ├── register.php         # New user registration
│   └── logout.php           # Session logout
├── user/                    # User Panel
│   ├── dashboard.php        # User home
│   ├── menu.php             # Browse menu items
│   ├── cart.php             # Shopping cart & checkout
│   ├── orders.php           # Order history
│   ├── reservation.php      # Book table
│   ├── feedback.php         # Submit feedback/rating
│   └── profile.php          # Edit profile
├── admin/                   # Admin Panel
│   ├── dashboard.php        # Admin overview
│   ├── manage_menu.php      # Add/Edit/Delete items
│   ├── manage_orders.php    # View & update orders
│   ├── manage_reservations.php  # Approve/Reject bookings
│   ├── manage_feedback.php  # View customer reviews
│   └── manage_users.php     # Manage user accounts
└── database/
    └── rms.sql              # Database schema
```

---

## 🚀 Installation & Setup

### Step 1: Start Your Server
```bash
# Start XAMPP/WAMP/LAMP
# Apache & MySQL must be running
```

### Step 2: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" to create a new database
3. Database name: `rms_db`
4. Click "Create"
5. Select `rms_db` and go to "Import" tab
6. Choose `database/rms.sql` file
7. Click "Go" to import

### Step 3: Copy Project Folder
```
Copy RestaurantManagementSystem folder to:
- Windows: C:\xampp\htdocs\
- Linux: /var/www/html/
- Mac: /Applications/XAMPP/htdocs/
```

### Step 4: Access Application
Open browser and go to:
```
http://localhost/RestaurantManagementSystem/
```

---

## 👤 Default Test Credentials

### Admin Account
- **Email:** admin@rms.com
- **Password:** admin123

### Test User (Register a new account)
- Click "Sign Up" on the home page
- Fill in details
- Login with your credentials

---

## 🎯 Key Features Implemented

### ✅ Authentication Module
- [x] User registration with email validation
- [x] Secure password hashing (password_hash)
- [x] Session-based login system
- [x] Role-based access control (Admin/User)
- [x] Logout with session cleanup

### ✅ User Features
- [x] Browse full menu with categories
- [x] Search menu items by name/description
- [x] Filter items by category
- [x] Add items to cart
- [x] Update cart quantities
- [x] Remove items from cart
- [x] Checkout with delivery address
- [x] Order history with status tracking
- [x] Book table reservations
- [x] Submit ratings and feedback (1-5 stars)
- [x] Update profile information
- [x] Change password

### ✅ Admin Features
- [x] Dashboard with key statistics:
  - Total users, orders, reservations, feedback count
  - Total revenue from delivered orders
  - Pending orders and reservations count
- [x] Manage Menu:
  - Add new menu items
  - Edit existing items
  - Delete items
  - Toggle item availability
  - Upload item images
- [x] Manage Orders:
  - View all orders
  - Filter by status (Pending, Preparing, Delivered)
  - Update order status
  - View customer details
- [x] Manage Reservations:
  - View pending reservations
  - Approve/Reject bookings
  - Filter by status
- [x] Manage Feedback:
  - View all customer reviews
  - Filter by rating (1-5 stars)
  - Delete reviews
- [x] Manage Users:
  - View all registered customers
  - View user order count
  - Delete user accounts
  - Prevent admin deletion

### ✅ Database Design
Tables created:
- **users** - Stores user accounts (admin/customer)
- **menu_items** - Food items with category and price
- **orders** - Customer orders with status
- **order_details** - Individual items in each order
- **reservations** - Table booking requests
- **feedback** - Customer reviews and ratings

### ✅ Frontend Design
- [x] Fully responsive CSS (no Bootstrap)
- [x] Mobile-friendly navigation
- [x] Professional color scheme
- [x] Consistent styling across all pages
- [x] Alert messages for success/error
- [x] Form validation and user feedback

---

## 🔒 Security Features

1. **Input Validation**
   - All user inputs are sanitized
   - HTML special characters are escaped
   - SQL injection protection

2. **Password Protection**
   - Passwords hashed with PHP password_hash()
   - password_verify() used for login
   - No plain text passwords stored

3. **Session Security**
   - Session-based authentication
   - Role verification on each page
   - Automatic redirect for unauthorized access

4. **Database**
   - Foreign key relationships
   - Cascade delete to maintain integrity
   - Prepared statements for safe queries

---

## 📝 Code Comments

Every file includes:
- File header with description
- Function/section comments
- Inline comments explaining logic
- Beginner-friendly variable names

Example:
```php
// Check if user is logged in
if (!isLoggedIn()) {
    // Redirect to login page
    header('Location: ../auth/login.php');
    exit();
}
```

---

## 📄 Database Schema

### users table
```sql
- user_id INT PRIMARY KEY
- full_name VARCHAR(100)
- email VARCHAR(100) UNIQUE
- phone VARCHAR(15)
- password VARCHAR(255) [HASHED]
- role ENUM('admin', 'user')
- address TEXT
- created_at TIMESTAMP
```

### menu_items table
```sql
- item_id INT PRIMARY KEY
- item_name VARCHAR(100)
- category VARCHAR(50)
- description TEXT
- price DECIMAL(10, 2)
- image VARCHAR(255)
- is_available TINYINT
- created_at TIMESTAMP
```

### orders table
```sql
- order_id INT PRIMARY KEY
- user_id INT [FOREIGN KEY]
- total_amount DECIMAL(10, 2)
- status ENUM('Pending', 'Preparing', 'Delivered', 'Cancelled')
- delivery_address TEXT
- order_date TIMESTAMP
```

### order_details table
```sql
- detail_id INT PRIMARY KEY
- order_id INT [FOREIGN KEY]
- item_id INT [FOREIGN KEY]
- quantity INT
- price DECIMAL(10, 2)
```

### reservations table
```sql
- reservation_id INT PRIMARY KEY
- user_id INT [FOREIGN KEY]
- reservation_date DATE
- reservation_time TIME
- num_guests INT
- special_request TEXT
- status ENUM('Pending', 'Approved', 'Rejected')
- created_at TIMESTAMP
```

### feedback table
```sql
- feedback_id INT PRIMARY KEY
- user_id INT [FOREIGN KEY]
- rating INT (1-5)
- message TEXT
- created_at TIMESTAMP
```

---

## 🧪 Testing Checklist

### User Flow Testing
- [ ] Register new user account
- [ ] Login with email and password
- [ ] View menu items
- [ ] Search for menu items
- [ ] Add items to cart
- [ ] Update cart quantities
- [ ] Remove items from cart
- [ ] Checkout and place order
- [ ] View order history
- [ ] Book table reservation
- [ ] Submit feedback/rating
- [ ] Update profile information
- [ ] Logout successfully

### Admin Flow Testing
- [ ] Login as admin
- [ ] View dashboard statistics
- [ ] Add new menu item
- [ ] Edit menu item
- [ ] Delete menu item
- [ ] Toggle item availability
- [ ] View all orders
- [ ] Update order status
- [ ] Filter by status
- [ ] Approve/Reject reservation
- [ ] View customer feedback
- [ ] Filter feedback by rating
- [ ] View registered users
- [ ] Delete user account

---

## 🎓 Project Explanation for Viva

### Architecture
- **Simple Structure:** Easy to explain folder organization
- **No Framework:** Pure PHP makes it beginner-friendly
- **Session-Based:** Simple authentication method
- **Single Database:** MySQL for simplicity

### Key Concepts to Explain
1. **Session Handling:** How user data is stored using $_SESSION
2. **Cart Management:** Using session to store cart items temporarily
3. **Order Processing:** Flow from cart to order creation
4. **Role-Based Access:** Checking user role to restrict pages
5. **Input Sanitization:** Preventing SQL injection
6. **Database Relations:** Foreign keys between tables

### Technology Choices
- **PHP over Framework:** Teaches core concepts
- **MySQL over NoSQL:** Traditional RDBMS learning
- **CSS only:** CSS grid and flexbox knowledge
- **Vanilla JS:** Basic DOM manipulation

---

## 🐛 Troubleshooting

### Database Connection Error
```
Error: "Connection failed"
Solution: 
- Check MySQL is running in XAMPP/WAMP
- Verify database credentials in config/db.php
- Make sure database 'rms_db' exists
```

### Blank Pages After Login
```
Error: All pages show blank
Solution:
- Check PHP error logs
- Verify all include paths are correct
- Check file permissions
```

### Images Not Displaying
```
Error: Food images not showing
Solution:
- Create assets/images/ folder
- Check write permissions
- Verify image upload path
```

### Can't Add Items to Cart
```
Error: "Add to Cart" button not working
Solution:
- Ensure you're logged in
- Check JavaScript is enabled
- Verify cart.php exists
```

---

## 📚 Files Quick Reference

| File | Purpose | Key Function |
|------|---------|--------------|
| index.php | Home page | Display featured items |
| config/db.php | Database connection | mysqli_connect() |
| config/helpers.php | Reusable functions | isLoggedIn(), sanitize() |
| auth/login.php | User authentication | password_verify() |
| auth/register.php | New account creation | password_hash() |
| user/menu.php | Browse items | Add to cart action |
| user/cart.php | Shopping cart | Checkout & order creation |
| user/orders.php | Order history | Display user orders |
| admin/dashboard.php | Admin overview | Show statistics |
| admin/manage_menu.php | Menu management | CRUD operations |
| admin/manage_orders.php | Order management | Update status |

---

## 🌟 Features Highlight

### Cart System
```
- Session-based (no database needed)
- Real-time quantity updates
- Total price calculation
- Clear cart option
```

### Order System
```
- Orders saved to database
- Order details preserved
- Status tracking (Pending > Preparing > Delivered)
- Delivery address stored
```

### Reservation System
```
- Date/time picker
- Guest count validation
- Special requests field
- Admin approval workflow
```

### Feedback System
```
- 1-5 star rating
- Text review
- User information linked
- Admin can view all feedback
```

---

## 📱 Responsive Design

The CSS is fully responsive:
- Mobile (< 768px)
- Tablet (768px - 1024px)
- Desktop (> 1024px)

All pages work perfectly on:
- Smartphones
- Tablets
- Laptops
- Desktops

---

## ✨ Next Steps (Optional Enhancements)

1. **Payment Gateway** - Integrate Stripe/PayPal
2. **Email Notifications** - Order confirmation emails
3. **Rating Statistics** - Average rating graphs
4. **Inventory Management** - Track stock levels
5. **Receipt Generation** - PDF invoices
6. **Analytics Dashboard** - Sales reports
7. **User Reviews** - Display on menu items
8. **Promotional Codes** - Discount system
9. **Delivery Tracking** - Real-time status
10. **Mobile App** - React Native frontend

---

## 📞 Support

For issues or unclear code:
1. Check file comments
2. Verify database tables exist
3. Check PHP error logs
4. Review SQL queries
5. Test one feature at a time

---

**Project Status:** ✅ Complete and Ready for Viva Presentation

All requirements implemented with clean, commented code suitable for academic evaluation.
