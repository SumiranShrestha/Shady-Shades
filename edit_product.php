<?php
session_start();
include("server/connection.php");

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all categories from the database
$categories_result = $conn->query("SELECT * FROM categories");

if (isset($_GET["id"])) {
    $product_id = $_GET["id"];

    // Fetch the product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

// Handle updating the product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"] ?? '';
    $category_id = $_POST["category"] ?? '';
    $product_id = $_POST["product_id"];

    // Handle image uploads
    $uploaded_images = [];
    if (!empty($_FILES["images"]["name"][0])) {
        $target_dir = "uploads/"; // Directory to store uploaded images
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }

        foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {
            $file_name = basename($_FILES["images"]["name"][$key]);
            $target_file = $target_dir . uniqid() . "_" . $file_name; // Unique file name to avoid conflicts

            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_images[] = $target_file; // Save the file path
            }
        }
    }

    // Fetch existing images
    $stmt = $conn->prepare("SELECT images FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_product = $result->fetch_assoc();
    $existing_images = json_decode($existing_product['images'], true);

    // Merge existing and new images
    $updated_images = array_merge($existing_images, $uploaded_images);

    // Update the product in the database
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, category_id = ?, images = ? WHERE id = ?");
    $images_json = json_encode($updated_images); // Convert images array to JSON
    $stmt->bind_param("sdsssi", $name, $price, $description, $category_id, $images_json, $product_id);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = "Product successfully updated";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['alert_message'] = "Error updating product";
        $_SESSION['alert_type'] = "danger";
    }

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .img-thumbnail {
            position: relative;
            margin: 5px;
            width: 100px;
            height: auto;
        }
        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: rgba(255, 0, 0, 1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_products.php">Products</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?></span>
                    <a href="admin_logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-pencil me-2"></i>Edit Product</h2>
            <a href="manage_products.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Products
            </a>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($_SESSION['alert_message'])): ?>
            <div class="alert alert-<?= $_SESSION['alert_type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['alert_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
                // Clear the message after displaying
                unset($_SESSION['alert_message']); 
                unset($_SESSION['alert_type']); 
            ?>
        <?php endif; ?>
        
        <!-- Edit Product Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Product</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (रू)</label>
                        <div class="input-group">
                            <span class="input-group-text">रू</span>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="" <?= empty($product['category_id']) ? 'selected' : ''; ?>>Select category</option>
                            <?php if ($categories_result->num_rows > 0): ?>
                                <?php while ($category = $categories_result->fetch_assoc()): ?>
                                    <option value="<?= $category['id']; ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <!-- Display Existing Images -->
                    <div class="mb-3">
                        <label class="form-label">Existing Images</label>
                        <div class="d-flex flex-wrap">
                            <?php
                                $images = json_decode($product['images'], true);
                                foreach ($images as $image): ?>
                                <div style="position: relative; margin: 5px;">
                                    <img src="<?= $image; ?>" alt="Product Image" class="img-thumbnail">
                                    <a href="delete_image.php?product_id=<?= $product['id']; ?>&image_url=<?= urlencode($image); ?>" 
                                       class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this image?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Add New Images -->
                    <div class="mb-3">
                        <label for="images" class="form-label">Add More Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                    </div>
                    
                    <button type="submit" name="update_product" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i>Update Product
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> Admin Panel. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>