<?php

// Include database configuration
require_once '../config/db.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

$success = '';
$error = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $item_id = (int)$_GET['id'];
    $delete_query = "DELETE FROM menu_items WHERE item_id = $item_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $success = 'Menu item deleted successfully';
    } else {
        $error = 'Failed to delete item';
    }
}

// Handle toggle availability
if (isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['id'])) {
    $item_id = (int)$_GET['id'];
    $toggle_query = "UPDATE menu_items SET is_available = NOT is_available WHERE item_id = $item_id";
    
    if (mysqli_query($conn, $toggle_query)) {
        $success = 'Availability updated';
    }
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = sanitize($_POST['item_name']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

    // Validate inputs
    if (empty($item_name) || empty($category) || $price <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        // Handle image upload
        $image = 'default.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $target_dir = "../assets/images/";
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($file_extension, $allowed_types)) {
                $image = time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $image;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image = 'default.jpg';
                }
            }
        } elseif ($item_id > 0) {
            // Keep existing image when editing
            $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM menu_items WHERE item_id = $item_id"));
            $image = $existing['image'];
        }

        if ($item_id > 0) {
            // Update existing item
            $query = "UPDATE menu_items SET 
                      item_name = '$item_name', 
                      category = '$category', 
                      description = '$description', 
                      price = $price, 
                      image = '$image',
                      is_available = $is_available 
                      WHERE item_id = $item_id";
            $success_msg = 'Menu item updated successfully';
        } else {
            // Add new item
            $query = "INSERT INTO menu_items (item_name, category, description, price, image, is_available) 
                      VALUES ('$item_name', '$category', '$description', $price, '$image', $is_available)";
            $success_msg = 'Menu item added successfully';
        }

        if (mysqli_query($conn, $query)) {
            $success = $success_msg;
        } else {
            $error = 'Operation failed. Please try again.';
        }
    }
}

// Get item for editing if edit action
$edit_item = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $item_id = (int)$_GET['id'];
    $edit_result = mysqli_query($conn, "SELECT * FROM menu_items WHERE item_id = $item_id");
    if (mysqli_num_rows($edit_result) > 0) {
        $edit_item = mysqli_fetch_assoc($edit_result);
    }
}

// Get all menu items
$menu_query = "SELECT * FROM menu_items ORDER BY category, item_name";
$menu_result = mysqli_query($conn, $menu_query);

// Get unique categories for dropdown
$categories = array('Burgers', 'Pizza', 'Pasta', 'Salads', 'Main Course', 'Seafood', 'Desserts', 'Beverages');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <main class="admin-page">
        <div class="container">
            <div class="page-header">
                <h1>Manage Menu</h1>
                <p>Add, edit, or remove menu items</p>
            </div>

            <!-- Display messages -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="admin-content">
                <!-- Add/Edit Form -->
                <div class="admin-form-section">
                    <h2><?php echo $edit_item ? 'Edit Menu Item' : 'Add New Item'; ?></h2>
                    <form action="manage_menu.php" method="POST" enctype="multipart/form-data" class="admin-form">
                        <?php if ($edit_item): ?>
                            <input type="hidden" name="item_id" value="<?php echo $edit_item['item_id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="item_name">Item Name *</label>
                            <input type="text" 
                                   id="item_name" 
                                   name="item_name" 
                                   value="<?php echo $edit_item ? $edit_item['item_name'] : ''; ?>"
                                   required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat; ?>" 
                                                <?php echo ($edit_item && $edit_item['category'] == $cat) ? 'selected' : ''; ?>>
                                            <?php echo $cat; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="price">Price ($) *</label>
                                <input type="number" 
                                       id="price" 
                                       name="price" 
                                       step="0.01" 
                                       min="0"
                                       value="<?php echo $edit_item ? $edit_item['price'] : ''; ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"><?php echo $edit_item ? $edit_item['description'] : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Item Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                            <?php if ($edit_item && $edit_item['image'] != 'default.jpg'): ?>
                                <small>Current: <?php echo $edit_item['image']; ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group checkbox-group">
                            <input type="checkbox" 
                                   id="is_available" 
                                   name="is_available"
                                   <?php echo (!$edit_item || $edit_item['is_available']) ? 'checked' : ''; ?>>
                            <label for="is_available">Available for ordering</label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_item ? 'Update Item' : 'Add Item'; ?>
                            </button>
                            <?php if ($edit_item): ?>
                                <a href="manage_menu.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Menu Items List -->
                <div class="admin-list-section">
                    <h2>All Menu Items (<?php echo mysqli_num_rows($menu_result); ?>)</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($menu_result)): ?>
                                <tr>
                                    <td>
                                        <img src="../assets/images/<?php echo $item['image']; ?>" 
                                             alt="<?php echo $item['item_name']; ?>"
                                             class="table-image"
                                             onerror="this.src='../assets/images/default.jpg'">
                                    </td>
                                    <td><?php echo $item['item_name']; ?></td>
                                    <td><?php echo $item['category']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $item['is_available'] ? 'approved' : 'rejected'; ?>">
                                            <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="manage_menu.php?action=edit&id=<?php echo $item['item_id']; ?>" 
                                           class="btn btn-small">Edit</a>
                                        <a href="manage_menu.php?action=toggle&id=<?php echo $item['item_id']; ?>" 
                                           class="btn btn-small btn-secondary">Toggle</a>
                                        <a href="manage_menu.php?action=delete&id=<?php echo $item['item_id']; ?>" 
                                           class="btn btn-small btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
