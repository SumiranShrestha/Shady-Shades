<?php
include('server/connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'] ?? null;

// Redirect if no order ID is provided
if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Fetch order details
$stmt = $conn->prepare("
    SELECT o.id, o.full_name, o.email, o.phone, o.address, o.city, 
           o.payment_method, o.total_price, o.order_status, o.created_at
    FROM orders o
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Redirect if order is not found
if (!$order) {
    header("Location: index.php");
    exit();
}

// Fetch order items
$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price, p.name, p.images
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Shady Shades</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="alert alert-success text-center">
        🎉 <strong>Success!</strong> Your order has been placed successfully.
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Order #<?= htmlspecialchars($order['id']); ?>
        </div>
        <div class="card-body">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['address'] . ", " . $order['city']); ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Total Price:</strong> ₹ <?= number_format($order['total_price']); ?></p>
            <p><strong>Order Status:</strong> <span class="badge bg-warning"><?= htmlspecialchars($order['order_status']); ?></span></p>
            <p><strong>Order Date:</strong> <?= date("d M Y, H:i A", strtotime($order['created_at'])); ?></p>
        </div>
    </div>

    <h3 class="mt-4">Order Items</h3>
    <ul class="list-group">
        <?php foreach ($order_items as $item) : ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <img src="<?= htmlspecialchars(json_decode($item['images'])[0] ?? 'images/default.jpg'); ?>" 
                         alt="Product Image" class="img-thumbnail me-2" style="width: 50px;">
                    <strong><?= htmlspecialchars($item['name']); ?></strong> (x<?= $item['quantity']; ?>)
                </div>
                <span>₹ <?= number_format($item['price'] * $item['quantity']); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="index.php" class="btn btn-primary mt-4">Back to Home</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
