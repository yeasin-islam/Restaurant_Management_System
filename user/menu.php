<?php

// Include database configuration
require_once '../config/db.php';

// Initialize variables
$search = '';
$category_filter = '';
$success = '';
$error = '';

// Handle add to cart action
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    if (!isLoggedIn()) {
        redirect('../auth/login.php');
    }
    
    $item_id = (int)$_GET['id'];
    
    // Get item details
    $item_query = "SELECT * FROM menu_items WHERE item_id = $item_id AND is_available = 1";
    $item_result = mysqli_query($conn, $item_query);
    
    if (mysqli_num_rows($item_result) > 0) {
        $item = mysqli_fetch_assoc($item_result);
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        // Check if item already in cart
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$item_id] = array(
                'item_id' => $item_id,
                'name' => $item['item_name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'quantity' => 1
            );
        }
        $success = $item['item_name'] . ' added to cart!';
    } else {
        $error = 'Item not found or unavailable';
    }
}

// Handle search
if (isset($_GET['search'])) {
    $search = sanitize($_GET['search']);
}

// Handle category filter
if (isset($_GET['category'])) {
    $category_filter = sanitize($_GET['category']);
}

// Get all categories for filter
$categories_query = "SELECT DISTINCT category FROM menu_items ORDER BY category";
$categories_result = mysqli_query($conn, $categories_query);

// Build menu query with filters
$menu_query = "SELECT * FROM menu_items WHERE is_available = 1";

if (!empty($search)) {
    $menu_query .= " AND (item_name LIKE '%$search%' OR description LIKE '%$search%')";
}

if (!empty($category_filter)) {
    $menu_query .= " AND category = '$category_filter'";
}

$menu_query .= " ORDER BY category, item_name";
$menu_result = mysqli_query($conn, $menu_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu - Delicious Bites</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="menu-page">
        <div class="container">
            <div class="page-header">
                <h1>Our Menu</h1>
                <p>Explore our delicious offerings</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <div class="menu-filters">
                <form action="menu.php" method="GET" class="search-form">
                    <input type="text" 
                           name="search" 
                           placeholder="Search menu items..." 
                           value="<?php echo $search; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <div class="category-filters">
                    <a href="menu.php" class="filter-btn <?php echo empty($category_filter) ? 'active' : ''; ?>">
                        All
                    </a>
                    <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                        <a href="menu.php?category=<?php echo urlencode($cat['category']); ?>" 
                           class="filter-btn <?php echo $category_filter == $cat['category'] ? 'active' : ''; ?>">
                            <?php echo $cat['category']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Menu Items Grid -->
            <?php if (mysqli_num_rows($menu_result) > 0): ?>
                <div class="menu-grid">
                    <?php while ($item = mysqli_fetch_assoc($menu_result)): ?>
                        <div class="menu-card">
                            <div class="menu-card-image">
                                <img src="../assets/images/<?php echo $item['image']; ?>" 
                                     alt="<?php echo $item['item_name']; ?>"
                                     onerror="this.src='../assets/images/default.jpg'">
                                <span class="category-badge"><?php echo $item['category']; ?></span>
                            </div>
                            <div class="menu-card-content">
                                <h3><?php echo $item['item_name']; ?></h3>
                                <p><?php echo $item['description']; ?></p>
                                <div class="menu-card-footer">
                                    <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                                    <?php if (isLoggedIn() && !isAdmin()): ?>
                                        <a href="menu.php?action=add&id=<?php echo $item['item_id']; ?>" 
                                           class="btn btn-small">Add to Cart</a>
                                    <?php elseif (!isLoggedIn()): ?>
                                        <a href="../auth/login.php" class="btn btn-small">Login to Order</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No items found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                    <a href="menu.php" class="btn btn-primary">View All Items</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
