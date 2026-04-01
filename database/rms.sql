-- =====================================================
-- Restaurant Management System Database
-- Created for: CSE Academic Project
-- =====================================================

-- =====================================================
-- Table: users
-- Stores both admin and customer information
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: menu_items
-- Stores all food items in the restaurant menu
-- =====================================================
CREATE TABLE IF NOT EXISTS menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: orders
-- Stores customer order information
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Preparing', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    delivery_address TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =====================================================
-- Table: order_details
-- Stores individual items in each order
-- =====================================================
CREATE TABLE IF NOT EXISTS order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- =====================================================
-- Table: reservations
-- Stores table booking information
-- =====================================================
CREATE TABLE IF NOT EXISTS reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    num_guests INT NOT NULL,
    special_request TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =====================================================
-- Table: feedback
-- Stores customer reviews and ratings
-- =====================================================
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =====================================================
-- Insert Default Admin Account
-- Email: admin@rms.com | Password: admin123
-- =====================================================
INSERT INTO users (full_name, email, phone, password, role) VALUES 
('Admin', 'admin@rms.com', '1234567890', '$2y$10$8WxYl5pBzPqFQdQOp.dn8.HwQ8bF3Z5jGwPQWNK1mXvJQUzNkxBXC', 'admin'),('Admin', 'admin@rms.com', '1234567890', '$2y$10$8WxYl5pBzPqFQdQOp.dn8.HwQ8bF3Z5jGwPQWNK1mXvJQUzNkxBXC', 'admin');

-- =====================================================
-- Insert Sample Menu Items
-- =====================================================
INSERT INTO menu_items (item_name, category, description, price, image, is_available) VALUES
('Classic Burger', 'Burgers', 'Juicy beef patty with fresh lettuce, tomato, and special sauce', 8.99, 'burger.jpg', 1),
('Cheese Pizza', 'Pizza', 'Wood-fired pizza with mozzarella and fresh basil', 12.99, 'pizza.jpg', 1),
('Caesar Salad', 'Salads', 'Crisp romaine lettuce with parmesan and caesar dressing', 6.99, 'salad.jpg', 1),
('Grilled Chicken', 'Main Course', 'Tender grilled chicken breast with herbs and spices', 14.99, 'chicken.jpg', 1),
('Pasta Carbonara', 'Pasta', 'Creamy pasta with bacon and parmesan cheese', 11.99, 'pasta.jpg', 1),
('Fish and Chips', 'Seafood', 'Crispy battered fish with golden fries', 13.99, 'fish.jpg', 1),
('Chocolate Cake', 'Desserts', 'Rich chocolate layer cake with ganache', 5.99, 'cake.jpg', 1),
('Ice Cream Sundae', 'Desserts', 'Vanilla ice cream with chocolate sauce and nuts', 4.99, 'sundae.jpg', 1),
('Fresh Lemonade', 'Beverages', 'Freshly squeezed lemonade with mint', 2.99, 'lemonade.jpg', 1),
('Coffee', 'Beverages', 'Premium arabica coffee', 3.49, 'coffee.jpg', 1);

-- =====================================================
-- End of Database Setup
-- =====================================================