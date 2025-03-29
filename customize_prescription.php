<?php
session_start();
include('header.php');
require_once("server/connection.php");

if (!isset($_GET['order_id'])) {
    header("Location: cart.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'] ?? null;

// Try to get order from prescription_orders first
$stmt = $conn->prepare("SELECT po.*, p.name as product_name, p.price, b.brand_name 
                       FROM prescription_orders po
                       JOIN products p ON po.product_id = p.id
                       JOIN brands b ON p.brand_id = b.id
                       WHERE po.id = ?" . ($user_id ? " AND po.user_id = $user_id" : ""));
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// If not found in prescription_orders, check regular orders
if (!$order) {
    $stmt = $conn->prepare("SELECT o.*, oi.product_id, oi.quantity, oi.price as item_price, 
                           p.name as product_name, b.brand_name
                           FROM orders o
                           JOIN order_items oi ON o.id = oi.order_id
                           JOIN products p ON oi.product_id = p.id
                           JOIN brands b ON p.brand_id = b.id
                           WHERE o.id = ?" . ($user_id ? " AND o.user_id = $user_id" : ""));
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
}

if (!$order) {
    header("Location: cart.php");
    exit();
}

// Check if this is a prescription order
$is_prescription_order = isset($order['right_eye_sphere']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Shady Shades</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .order-detail-card {
            border-left: 4px solid #0d6efd;
        }
        .prescription-detail {
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Order Confirmation</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h4 class="alert-heading">Thank you for your order!</h4>
                    <p>Your order #<?= htmlspecialchars($order_id) ?> has been placed successfully.</p>
                    <hr>
                    <p class="mb-0">We'll send you a confirmation email shortly.</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card order-detail-card mb-4">
                            <div class="card-header">
                                <h5>Order Details</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Order Number:</strong> #<?= htmlspecialchars($order_id) ?></p>
                                <p><strong>Date:</strong> <?= date('F j, Y, g:i a') ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-info">Processing</span></p>
                                
                                <h6 class="mt-4">Product:</h6>
                                <div class="d-flex align-items-center">
                                    <?php 
                                    $images = json_decode($order['images'] ?? '[]', true);
                                    $main_image = $images[0] ?? 'default-product.jpg';
                                    ?>
                                    <img src="<?= htmlspecialchars($main_image) ?>" class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;" alt="<?= htmlspecialchars($order['product_name']) ?>">
                                    <div>
                                        <h6><?= htmlspecialchars($order['product_name']) ?></h6>
                                        <p class="text-muted small mb-1"><?= htmlspecialchars($order['brand_name']) ?></p>
                                        <p class="fw-bold">रू <?= number_format($is_prescription_order ? $order['price'] : $order['item_price']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php if ($is_prescription_order): ?>
                        <div class="card prescription-detail mb-4">
                            <div class="card-header">
                                <h5>Prescription Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h6>Right Eye</h6>
                                        <p class="mb-1">SPH: <?= $order['right_eye_sphere'] ?></p>
                                        <p class="mb-1">CYL: <?= $order['right_eye_cylinder'] ?></p>
                                        <p class="mb-1">Axis: <?= $order['right_eye_axis'] ?></p>
                                        <p class="mb-1">PD: <?= $order['right_eye_pd'] ?></p>
                                    </div>
                                    <div class="col-6">
                                        <h6>Left Eye</h6>
                                        <p class="mb-1">SPH: <?= $order['left_eye_sphere'] ?></p>
                                        <p class="mb-1">CYL: <?= $order['left_eye_cylinder'] ?></p>
                                        <p class="mb-1">Axis: <?= $order['left_eye_axis'] ?></p>
                                        <p class="mb-1">PD: <?= $order['left_eye_pd'] ?></p>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-1"><strong>Lens Type:</strong> <?= ucfirst(str_replace('_', ' ', $order['lens_type'])) ?></p>
                                <p class="mb-1"><strong>Coating:</strong> <?= ucfirst(str_replace('_', ' ', $order['coating_type'])) ?></p>
                                <p class="mb-1"><strong>Frame Color:</strong> <?= ucfirst($order['frame_color']) ?></p>
                                <p class="mb-1"><strong>Frame Size:</strong> <?= ucfirst($order['frame_size']) ?></p>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Shipping Details</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                                <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                                <p class="mb-1"><strong>City:</strong> <?= htmlspecialchars($order['city']) ?></p>
                                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                                <hr>
                                <p class="mb-1"><strong>Payment Method:</strong> <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <a href="index.php" class="btn btn-primary me-md-2">
                        <i class="bi bi-house-door"></i> Continue Shopping
                    </a>
                    <a href="my_orders.php" class="btn btn-outline-secondary">
                        <i class="bi bi-list-check"></i> View All Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>