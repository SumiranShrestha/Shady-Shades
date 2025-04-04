<?php
include('header.php');

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('server/connection.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch cart items
$cart_items = [];
$total_price = 0;
$delivery_charge = 100; // Default delivery charge

$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.discount_price, p.images 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $product_price = ($row['discount_price'] > 0) ? $row['discount_price'] : $row['price'];
    $total_price += $product_price * $row['quantity'];
}

$grand_total = $total_price + $delivery_charge;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout | Shady Shades</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

    <style>
        body {
            background-color: #fff;
        }

        .checkout-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .checkout-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .checkout-header a {
            text-decoration: none;
            color: #16a34a;
            font-size: 1.2rem;
        }

        .order-summary {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 1rem;
        }

        .order-summary .order-item img {
            width: 64px;
            height: auto;
            object-fit: cover;
        }

        .place-order-btn {
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="container checkout-container">
        <div class="checkout-header">
            <a href="cart.php" aria-label="Go back">←</a>
            <h2 class="mb-0">Checkout</h2>
        </div>

        <div class="row">
            <div class="col-md-8">
                <form id="checkoutForm">
                    <h5 class="mb-3">1. General Information</h5>
                    <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? ''); ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <div class="input-group">
                            <span class="input-group-text">NP</span>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" required />
                        </div>
                    </div>

                    <h5 class="mb-3">2. Delivery Address</h5>
                    <div class="mb-3">
                        <label class="form-label">City / District *</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? ''); ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address'] ?? ''); ?>" required />
                    </div>

                    <button type="button" id="placeOrderBtn" class="btn btn-success place-order-btn">Place Order</button>
                </form>
            </div>

            <div class="col-md-4">
                <div class="order-summary">
                    <h5>Order Summary</h5>
                    <?php foreach ($cart_items as $item) : ?>
                        <div class="d-flex align-items-start mb-3 order-item">
                            <img src="<?= htmlspecialchars(json_decode($item['images'])[0] ?? 'images/default.jpg'); ?>" alt="Product" class="me-2 rounded" />
                            <div>
                                <p class="fw-bold"><?= htmlspecialchars($item['name']); ?> (x<?= $item['quantity']; ?>)</p>
                                <p class="text-success fw-bold mb-0">₹ <?= number_format(($item['discount_price'] > 0 ? $item['discount_price'] : $item['price']) * $item['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <hr />
                    <p>Total: ₹ <?= number_format($grand_total); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("placeOrderBtn").addEventListener("click", function() {
            let formData = new FormData(document.getElementById("checkoutForm"));

            fetch("server/place_order.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    window.location.href = "order-confirmation.php?order_id=" + data.order_id;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    </script>

</body>

</html>