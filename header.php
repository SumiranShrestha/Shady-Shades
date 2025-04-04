<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include("server/connection.php");

// Initialize counts
$cart_count = 0;
$appointment_count = 0;
$user_type = '';

// Check if user is logged in
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $user_type = $_SESSION["user_type"] ?? 'user';

    if ($user_type === 'user') {
        // Fetch cart count for users
        $stmt = $conn->prepare("SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_data = $result->fetch_assoc();
        $cart_count = $cart_data["cart_count"] ?? 0;
    } elseif ($user_type === 'doctor') {
        // Fetch pending appointment count for doctors
        $stmt = $conn->prepare("SELECT COUNT(*) AS appointment_count FROM appointments WHERE doctor_id = ? AND status = 'pending'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointment_data = $result->fetch_assoc();
        $appointment_count = $appointment_data["appointment_count"] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shady Shades - <?= $page_title ?? 'Home' ?></title>
    
    <!-- Bootstrap & Toastr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        /* Icons */
        .icon-container {
            position: relative;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Badges */
        .badge-count {
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

        /* Navigation */
        .nav-link.active {
            font-weight: 600;
            color: #0d6efd !important;
        }

        /* Cart Drawer */
        .cart-item-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border: 1px solid #ddd;
            margin-right: 10px;
        }
        
        /* Custom Primary Color Overrides */
        .bg-primary {
            background-color: #E673DE !important;
        }
        .btn-primary {
            background-color: #E673DE !important;
            border-color: #E673DE !important;
        }
    </style>
</head>
<body>

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

<!-- ✅ Header -->
<header class="navbar navbar-expand-lg bg-light shadow-sm sticky-header">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.webp" alt="Shady Shades Logo" style="height: 50px;">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Main Navigation -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Shop All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'skull-rider.php' ? 'active' : '' ?>" href="skull-rider.php">Skull Rider</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'brand-originals.php' ? 'active' : '' ?>" href="brand-originals.php">Brand Originals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'prescription-frames.php' ? 'active' : '' ?>" href="prescription-frames.php">Prescription Frames</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'doctors.php' ? 'active' : '' ?>" href="doctors.php">Doctors</a>
                </li>
                
                <?php if (isset($_SESSION["user_id"]) && $user_type === 'doctor'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'doctor_dashboard.php' ? 'active' : '' ?>" href="doctor_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'doctor_appointments.php' ? 'active' : '' ?>" href="doctor_appointments.php">Appointments</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- User Actions -->
            <div class="d-flex align-items-center">
                <?php if (!isset($_SESSION["user_id"])): ?>
                    <!-- Guest User -->
                    <a href="#" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    <a href="#" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a>
                    <a href="#" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#doctorLoginModal">Doctor Login</a>
                <?php else: ?>
                    <!-- Logged In User -->
                    <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION["user_name"]); ?></span>
                    
                    <a href="profile.php" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-person"></i> Profile
                    </a>

                    <?php if ($user_type === 'user'): ?>
                        <!-- User Cart -->
                        <div class="icon-container me-2" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer">
                            <i class="bi bi-bag"></i>
                            <?php if ($cart_count > 0): ?>
                                <span id="cartBadge" class="badge-count"><?= $cart_count; ?></span>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($user_type === 'doctor'): ?>
                        <!-- Doctor Appointments -->
                        <a href="doctor_appointments.php" class="icon-container me-2">
                            <i class="bi bi-calendar-check"></i>
                            <?php if ($appointment_count > 0): ?>
                                <span id="appointmentBadge" class="badge-count"><?= $appointment_count; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <a href="server/logout.php" class="btn btn-outline-danger">Logout</a>
                <?php endif; ?>
            </div>
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
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
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

<!-- ✅ Doctor Login Modal -->
<div class="modal fade" id="doctorLoginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Doctor Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="doctorLoginForm" action="server/doctor_login.php" method="POST">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Doctor Email" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <button type="submit" class="btn btn-primary w-100">Login as Doctor</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // Toastr Configuration
    toastr.options = {
        positionClass: "toast-top-right",
        timeOut: 3000,
        closeButton: true
    };

    // Update Cart Functionality
    function updateCart() {
        $.ajax({
            url: "server/fetch_cart.php",
            method: "GET",
            dataType: "json",
            success: function(data) {
                let container = $("#cartItemsContainer");
                let total = 0;
                container.empty();

                if (!data.items || data.items.length === 0) {
                    container.html('<p class="text-muted text-center">Your cart is empty.</p>');
                    $("#cartTotal").text("रू 0");
                    $("#cartBadge").hide();
                    return;
                }

                // Build cart items
                data.items.forEach(item => {
                    let price = parseFloat(item.price) || 0;
                    let quantity = parseInt(item.quantity) || 0;
                    total += price * quantity;

                    container.append(`
                        <div class="cart-item d-flex align-items-center mb-3" id="cart-item-${item.id}">
                            <img src="${item.image}" alt="${item.name}" class="cart-item-img rounded">
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-bold">${item.name}</p>
                                <small>रू ${price.toLocaleString()} x ${quantity}</small>
                            </div>
                            <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">✕</button>
                        </div>
                    `);
                });

                // Update total
                $("#cartTotal").text(`रू ${total.toLocaleString()}`);
                $("#cartBadge").text(data.count).show();
            },
            error: function() {
                toastr.error("Error fetching cart data!");
            }
        });
    }

    // Document Ready
    $(document).ready(function() {
        // Initialize cart
        updateCart();

        // Remove item from cart
        $(document).on("click", ".remove-item", function() {
            let productId = $(this).data("id");
            
            $.ajax({
                url: "server/remove_from_cart.php",
                method: "POST",
                data: { product_id: productId },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        toastr.warning("Product removed from cart!");
                        updateCart();
                    } else {
                        toastr.error("Failed to remove item!");
                    }
                },
                error: function() {
                    toastr.error("Error removing item!");
                }
            });
        });

        // Update cart when drawer opens
        $("#cartDrawer").on("shown.bs.offcanvas", function() {
            updateCart();
        });

        // Form submissions
        $("#loginForm, #signupForm, #doctorLoginForm").on("submit", function(e) {
            e.preventDefault();
            let form = $(this);
            
            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                dataType: "json",  // expecting JSON response
                success: function(response) {
                    if (response.status && response.status === "error") {
                        toastr.error(response.message);
                    } else if (response.status && response.status === "success") {
                        toastr.success(response.message);
                        if(response.redirect) {
                            setTimeout(function(){
                               window.location.href = response.redirect;
                            }, 1500); // Delay redirection to allow toast to be seen
                        }
                    } else if (response.redirect) {
                        toastr.success("Login successful");
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    try {
                        let error = JSON.parse(xhr.responseText);
                        toastr.error(error.message || "An error occurred");
                    } catch (e) {
                        toastr.error("An error occurred");
                    }
                }
            });
        });

    });
</script>
</body>
</html>
