    <?php
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Fetch cart count if user is logged in
    $cart_count = 0;
    if (isset($_SESSION["user_id"])) {
        include("server/connection.php");
        $stmt = $conn->prepare("SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_data = $result->fetch_assoc();
        $cart_count = $cart_data["cart_count"] ?? 0;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shady Shades - Home</title>

        <!-- Bootstrap & Toastr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

        <style>
            /* Sticky Header */
            .sticky-header {
                position: sticky;
                top: 0;
                z-index: 1020;
                background: white;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }

            /* Cart Icon */
            .cart-icon {
                position: relative;
                font-size: 1.5rem;
                cursor: pointer;
            }

            /* Cart Badge */
            .cart-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #28a745;
                color: white;
                font-size: 12px;
                font-weight: bold;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
        </style>
    </head>
    <!-- ✅ Cart Drawer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Your Cart</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cartItemsContainer">
                <p class="text-muted text-center">Loading cart...</p>
            </div>
        </div>
        <div class="offcanvas-footer cart-footer p-3">
            <div class="d-flex justify-content-between">
                <strong>Total:</strong>
                <span id="cartTotal">रू 0</span>
            </div>
            <div class="mt-3">
                <a href="cart.php" class="btn btn-outline-primary w-100">View Cart</a>
                <a href="checkout.php" class="btn btn-success w-100 mt-2">Checkout</a>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<!-- ✅ Header -->
<header class="navbar navbar-expand-lg bg-light shadow-sm sticky-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- 🔹 Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.webp" alt="Shady Shades Logo" style="height: 50px; margin-left: 18cm">
        </a>

        <div class="d-flex align-items-center">
            <?php if (!isset($_SESSION["user_id"])): ?>
                <!-- 🔹 Login & Signup Buttons -->
                <a href="#" class="btn btn-outline-primary me-3" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                <a href="#" class="btn btn-outline-success me-3" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a>
            <?php else: ?>
                <!-- 🔹 Welcome Message -->
                <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION["user_name"]); ?></span>
                
                <!-- 🔹 Profile Link -->
                <a href="profile.php" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-person"></i> Profile
                </a>

                <!-- 🔹 Cart Icon with Badge -->
                <div class="cart-icon me-3" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer">
                    <i class="bi bi-bag"></i>
                    <span id="cartBadge" class="cart-badge"><?= $cart_count; ?></span>
                </div>

                <!-- 🔹 Logout Button -->
                <a href="server/logout.php" class="btn btn-outline-danger">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</header>

    <!-- ✅ Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="server/login_process.php" method="POST">
                        <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                        <button type="submit" class="btn btn-primary w-100">Log In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Signup Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" action="server/signup_process.php" method="POST">
                        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                        <button type="submit" class="btn btn-success w-100">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Bar (Below Logo) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-top border-bottom">
            <div class="container-fluid justify-content-center">
                <ul class="navbar-nav">
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="index.php">Shop All</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="skull-rider.php">Skull Rider</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="brand-originals.php">Brand Originals</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="ray-ban.php">RAY-BAN | META WAYFARER</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="premium.php">Premium Sunglasses</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="home.php"><span>🔥 Sale 🔥</span></a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="prescription-frames.php">Prescription Frames</a>
                    </li>
                    <li>
                          <li class="nav-item mx-2">
                        <a class="nav-link" href="doctors.php">Doctors</a>
                    </li>
                    </li>
                </ul>
            </div>
        </nav>
        
    <!-- ✅ Bootstrap & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        function showToast(message, type = "success") {
            toastr.options = { positionClass: "toast-top-right", timeOut: 3000 };
            toastr[type](message);
        }

        function updateCart() {
            $.ajax({
                url: "server/fetch_cart.php",
                method: "GET",
                dataType: "json",
                success: function (data) {
                    let container = $("#cartItemsContainer");
                    let total = 0;
                    container.empty();

                    if (!data.items || data.items.length === 0) {
                        container.html("<p class='text-muted text-center'>Your cart is empty.</p>");
                        $("#cartTotal").text("रू 0");
                        $("#cartBadge").hide();
                        return;
                    }

                    // ✅ Loop through items and build cart HTML
                    data.items.forEach(item => {
                        let price = parseFloat(item.price) || 0;
                        let quantity = parseInt(item.quantity) || 0;
                        total += price * quantity;

                        container.append(`
                            <div class="cart-item d-flex align-items-center" id="cart-item-${item.id}">
                                <img src="${item.image}" alt="${item.name}" class="rounded shadow-sm" 
                                    style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; margin-right: 10px;">
                                <div class="cart-item-details flex-grow-1">
                                    <p class="mb-0 fw-bold">${item.name}</p>
                                    <small>रू ${price.toLocaleString()} x ${quantity}</small>
                                </div>
                                <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">✕</button>
                            </div>
                        `);
                    });

                    // ✅ Fix NaN total issue
                    if (isNaN(total)) {
                        total = 0;
                    }
                    $("#cartTotal").text(`रू ${total.toLocaleString()}`);
                    $("#cartBadge").text(data.count).show();
                },
                error: function () {
                    showToast("Error fetching cart data!", "error");
                }
            });
        }

        $(document).ready(function () {
            updateCart(); // Load cart data when page loads

            // ✅ Handle Remove from Cart
            $("#cartItemsContainer").on("click", ".remove-item", function () {
                let productId = $(this).data("id");

                $.ajax({
                    url: "server/remove_from_cart.php",
                    method: "POST",
                    data: { product_id: productId },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            showToast("Product removed from cart!", "warning");
                            updateCart();
                        } else {
                            showToast("Failed to remove item!", "error");
                        }
                    },
                    error: function () {
                        showToast("Error removing item!", "error");
                    }
                });
            });

            // ✅ Update cart when drawer opens
            $("#cartDrawer").on("shown.bs.offcanvas", function () {
                updateCart();
            });
        });
    </script>

