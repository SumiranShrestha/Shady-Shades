<?php
include('header.php');
require_once("server/connection.php");

// Get prescription frame products
$query = "SELECT p.*, b.brand_name 
          FROM products p 
          JOIN brands b ON p.brand_id = b.id
          WHERE p.category_id = 4";
$result = $conn->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get user's saved prescriptions if logged in
$prescriptions = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM prescription_frames WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $prescriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<main class="container my-5">
    <h2 class="text-center mb-4">Prescription Frames</h2>
    
    <?php if (!empty($prescriptions)): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>Your Saved Prescriptions</h5>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-3 g-3">
                <?php foreach ($prescriptions as $prescription): ?>
                <div class="col">
                    <div class="card h-100 prescription-card">
                        <div class="card-body">
                            <h6 class="card-title">Prescription from <?= date('M d, Y', strtotime($prescription['created_at'])) ?></h6>
                            <div class="row small">
                                <div class="col-6">
                                    <p class="mb-1"><strong>Right Eye:</strong></p>
                                    <p>
                                        SPH: <?= $prescription['right_eye_sphere'] ?><br>
                                        CYL: <?= $prescription['right_eye_cylinder'] ?><br>
                                        Axis: <?= $prescription['right_eye_axis'] ?>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Left Eye:</strong></p>
                                    <p>
                                        SPH: <?= $prescription['left_eye_sphere'] ?><br>
                                        CYL: <?= $prescription['left_eye_cylinder'] ?><br>
                                        Axis: <?= $prescription['left_eye_axis'] ?>
                                    </p>
                                </div>
                            </div>
                            <div class="d-grid mt-2">
                                <a href="prescription_order.php?prescription_id=<?= $prescription['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                   Use This Prescription
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($products as $product): 
            $images = json_decode($product['images'], true);
            $main_image = $images[0] ?? 'default-product.jpg';
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <img src="<?= htmlspecialchars($main_image) ?>" 
                             class="card-img-top h-100 object-fit-cover" 
                             alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($product['brand_name']) ?></p>
                        <p class="card-text fw-bold text-success">रू <?= number_format($product['price']) ?></p>
                        
                        <div class="d-grid gap-2">
                            <a href="customize_prescription.php?product_id=<?= $product['id'] ?>" 
                               class="btn btn-primary">
                               Customize with Prescription
                            </a>
                            
                            <?php if (!empty($prescriptions)): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown">
                                    Use Saved Prescription
                                </button>
                                <ul class="dropdown-menu">
                                    <?php foreach ($prescriptions as $prescription): ?>
                                    <li>
                                        <a class="dropdown-item" 
                                           href="prescription_order.php?product_id=<?= $product['id'] ?>&prescription_id=<?= $prescription['id'] ?>">
                                           <?= date('M d, Y', strtotime($prescription['created_at'])) ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include('footer.php'); ?>