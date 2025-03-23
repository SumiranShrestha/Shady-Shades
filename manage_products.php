<?php
session_start();
include("server/connection.php");

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all categories from the database
$categories_result = $conn->query("SELECT * FROM categories");

// Handle Product Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"] ?? '';
    $category_id = $_POST["category"] ?? '';

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

    // Insert product into the database
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, category_id, images) VALUES (?, ?, ?, ?, ?)");
    $images_json = json_encode($uploaded_images); // Convert images array to JSON
    $stmt->bind_param("sdsss", $name, $price, $description, $category_id, $images_json);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = "Product successfully added";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['alert_message'] = "Error adding product";
        $_SESSION['alert_type'] = "danger";
    }

    header("Location: manage_products.php");
    exit();
}

// Handle Product Deletion
if (isset($_GET["delete_product"])) {
    $product_id = $_GET["delete_product"];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        $_SESSION['alert_message'] = "Product successfully deleted";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['alert_message'] = "Error deleting product";
        $_SESSION['alert_type'] = "danger";
    }
    
    header("Location: manage_products.php");
    exit();
}

// Fetch All Products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .img-thumbnail {
            position: relative;
            margin: 5px;
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
            <h2><i class="bi bi-box-seam me-2"></i>Manage Products</h2>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
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
        
        <div class="row">
            <!-- Add Product Form -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (रू)</label>
                                <div class="input-group">
                                    <span class="input-group-text">रू</span>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="0.00" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected>Select category</option>
                                    <?php if ($categories_result->num_rows > 0): ?>
                                        <?php while ($category = $categories_result->fetch_assoc()): ?>
                                            <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Product description"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="images" class="form-label">Product Images</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            </div>
                            
                            <button type="submit" name="add_product" class="btn btn-success w-100">
                                <i class="bi bi-plus-lg me-1"></i>Add Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Product List -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-list-ul me-2"></i>Product List</h5>
                        <div class="input-group input-group-sm" style="max-width: 200px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search products">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Images</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($product = $result->fetch_assoc()): ?>
                                            <?php
                                                $images = json_decode($product['images'], true);
                                            ?>
                                            <tr>
                                                <td><?= $product['id']; ?></td>
                                                <td>
                                                    <?php foreach ($images as $image): ?>
                                                        <div style="position: relative; display: inline-block;">
                                                            <img src="<?= $image; ?>" alt="Product Image" class="img-thumbnail" style="width: 100px; height: auto;">
                                                            <a href="delete_image.php?product_id=<?= $product['id']; ?>&image_url=<?= urlencode($image); ?>" 
                                                               class="delete-btn" 
                                                               onclick="return confirm('Are you sure you want to delete this image?')">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><?= htmlspecialchars($product['name']); ?></td>
                                                <td>
                                                    <?php
                                                        // Fetch category name from categories table
                                                        $category_id = $product['category_id'];
                                                        $category_stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                                                        $category_stmt->bind_param("i", $category_id);
                                                        $category_stmt->execute();
                                                        $category_result = $category_stmt->get_result();
                                                        $category = $category_result->fetch_assoc();
                                                        echo htmlspecialchars($category['name'] ?? 'Uncategorized');
                                                    ?>
                                                </td>
                                                <td>रू <?= number_format($product['price'], 2); ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#productModal<?= $product['id']; ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <a href="manage_products.php?delete_product=<?= $product['id']; ?>" 
                                                           class="btn btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No products found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <small class="text-muted">Showing <?= $result->num_rows ?? 0 ?> products</small>
                    </div>
                </div>
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
    
    <!-- Simple search functionality -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const productTableBody = document.getElementById('productTableBody');
        const rows = productTableBody.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();

            for (let row of rows) {
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let cell of cells) {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
    </script>
</body>
</html>