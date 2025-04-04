<?php
include('header.php');
include('server/connection.php'); // Database connection

// Get selected brand filter
$selected_brand = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : '';

// Fetch products with brand names using JOIN
$query = "SELECT products.*, brands.brand_name FROM products 
          LEFT JOIN brands ON products.brand_id = brands.id";
if (!empty($selected_brand)) {
    $query .= " WHERE brand_id = $selected_brand";
}
$result = mysqli_query($conn, $query);
?>

<!-- Main Section -->
<main class="container my-4">
    <h2 class="text-center mb-4">All Products</h2>

    <div class="row">
        <!-- Filter Section -->
        <div class="col-md-3">
            <form method="GET">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Filter By Brand</h5>
                        <?php
                        // Fetch all brands
                        $brandQuery = "SELECT * FROM brands";
                        $brandResult = mysqli_query($conn, $brandQuery);
                        while ($brand = mysqli_fetch_assoc($brandResult)) {
                            $brandID = $brand['id'];
                            $brandName = $brand['brand_name'];
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='radio' name='brand_id' value='$brandID' " . ($selected_brand == $brandID ? "checked" : "") . ">
                                    <label class='form-check-label'>$brandName</label>
                                  </div>";
                        }
                        ?>
                        <button type="submit" class="btn btn-primary mt-3">Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>
        

        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php while ($product = mysqli_fetch_assoc($result)) { 
                    $images = json_decode($product['images'], true); // Convert JSON to array
                ?>
                    <div class="col">
                        <div class="card h-100">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo $images[0]; ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                            </a>
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="text-muted"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                                <p class="card-text">
                                    <span class="text-muted text-decoration-line-through">रू <?php echo number_format($product['price']); ?></span>
                                    <span class="ms-2 fw-bold text-success">रू <?php echo number_format($product['discount_price']); ?></span>
                                </p>
                                <span class="badge bg-primary">SAVE रू <?php echo number_format($product['price'] - $product['discount_price']); ?></span>
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-success mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<!-- Image Hover Effect -->
<style>
    .product-image {
        transition: transform 0.3s ease-in-out;
    }
    .product-image:hover {
        transform: scale(1.1);
    }
</style>

<?php include('footer.php'); ?>
