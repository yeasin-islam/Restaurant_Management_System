-- =====================================================
-- Restaurant Management System Database
-- Database Name: rms_db
-- =====================================================

CREATE DATABASE IF NOT EXISTS rms_db;
USE rms_db;

-- USERS TABLE
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- MENU ITEMS TABLE
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100),
    category VARCHAR(50),
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255) DEFAULT 'default.jpg',
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ORDERS TABLE
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2),
    status ENUM('Pending','Preparing','Delivered','Cancelled') DEFAULT 'Pending',
    delivery_address TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ORDER DETAILS TABLE
CREATE TABLE order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- RESERVATIONS TABLE
CREATE TABLE reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reservation_date DATE,
    reservation_time TIME,
    num_guests INT,
    special_request TEXT,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- FEEDBACK TABLE
CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    rating INT CHECK (rating >=1 AND rating <=5),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);


-- =====================================================
-- INSERT ADMIN + USER ACCOUNT
-- Password: admin123
-- Password: user123
-- =====================================================

INSERT INTO users
(full_name,email,phone,password,role,address)
VALUES
('Admin','admin@rms.com','1234567890',
'$2y$10$8WxYl5pBzPqFQdQOp.dn8.HwQ8bF3Z5jGwPQWNK1mXvJQUzNkxBXC',
'admin','Restaurant Office'),

('User','user@rms.com','01700000000',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.ogB0eF3Lk9K0sXq6',
'user','Dhaka Bangladesh');

-- SAMPLE MENU ITEMS
INSERT INTO menu_items
(item_name,category,description,price,image)
VALUES
('Classic Burger','Burgers','Beef burger',8.99,'burger.jpg'),
('Cheese Pizza','Pizza','Mozzarella pizza',12.99,'pizza.jpg'),
('Caesar Salad','Salads','Fresh salad',6.99,'salad.jpg'),
('Grilled Chicken','Main Course','Grilled chicken',14.99,'chicken.jpg'),
('Pasta Carbonara','Pasta','Creamy pasta',11.99,'pasta.jpg'),
('Fish and Chips','Seafood','Fried fish',13.99,'fish.jpg'),
('Chocolate Cake','Desserts','Chocolate cake',5.99,'cake.jpg'),
('Ice Cream Sundae','Desserts','Ice cream',4.99,'sundae.jpg'),
('Fresh Lemonade','Drinks','Lemon juice',2.99,'lemonade.jpg'),
('Coffee','Drinks','Hot coffee',3.49,'coffee.jpg');

-- SAMPLE ORDERS (USER ID = 2)
INSERT INTO orders
(user_id,total_amount,status,delivery_address)
VALUES
(2,15.98,'Delivered','Dhaka'),
(2,8.99,'Pending','Dhaka'),
(2,22.98,'Preparing','Dhaka'),
(2,6.99,'Delivered','Dhaka'),
(2,13.99,'Cancelled','Dhaka'),
(2,11.99,'Delivered','Dhaka'),
(2,5.99,'Pending','Dhaka'),
(2,4.99,'Preparing','Dhaka'),
(2,2.99,'Delivered','Dhaka'),
(2,3.49,'Pending','Dhaka');

-- ORDER DETAILS DATA
INSERT INTO order_details
(order_id,item_id,quantity,price)
VALUES
(1,1,2,8.99),
(2,1,1,8.99),
(3,2,1,12.99),
(4,3,1,6.99),
(5,6,1,13.99),
(6,5,1,11.99),
(7,7,1,5.99),
(8,8,1,4.99),
(9,9,1,2.99),
(10,10,1,3.49);

-- RESERVATIONS DATA
INSERT INTO reservations
(user_id,reservation_date,reservation_time,num_guests,special_request,status)
VALUES
(2,'2026-04-10','18:00:00',2,'Window seat','Approved'),
(2,'2026-04-11','19:00:00',4,'Birthday celebration','Pending'),
(2,'2026-04-12','20:00:00',3,'Near entrance','Approved'),
(2,'2026-04-13','17:30:00',5,'Family dinner','Rejected'),
(2,'2026-04-14','18:30:00',2,'Anniversary','Approved'),
(2,'2026-04-15','19:30:00',6,'Friends meetup','Pending'),
(2,'2026-04-16','20:30:00',2,'Corner table','Approved'),
(2,'2026-04-17','18:15:00',4,'VIP seating','Pending'),
(2,'2026-04-18','19:45:00',3,'Dinner plan','Approved'),
(2,'2026-04-19','20:15:00',2,'Casual visit','Pending');

-- FEEDBACK SAMPLE DATA
INSERT INTO feedback
(user_id,rating,message)
VALUES
(2,5,'Excellent food'),
(2,4,'Nice environment'),
(2,5,'Fast delivery'),
(2,3,'Good service'),
(2,4,'Loved pizza');

-- =====================================================