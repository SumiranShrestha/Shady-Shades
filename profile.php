<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include('server/connection.php'); // Include database connection
include('header.php');

$user_id = $_SESSION["user_id"];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user's orders
$orders = [];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}

// Fetch user's appointments
$appointments = [];
$stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointment_result = $stmt->get_result();
while ($row = $appointment_result->fetch_assoc()) {
    $appointments[] = $row;
}

// Check for success or error messages
$alert_message = $_SESSION['alert_message'] ?? null;
$alert_type = $_SESSION['alert_type'] ?? null;

// Clear the messages from the session
unset($_SESSION['alert_message']);
unset($_SESSION['alert_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Main Content -->
    <div class="container mt-5">
        <!-- Display Success or Error Messages -->
        <?php if ($alert_message) : ?>
            <div class="alert alert-<?= $alert_type; ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($alert_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Profile</h5>
                    </div>
                    <div class="card-body">
                        <!-- Profile Update Form -->
                        <form method="POST" action="update_profile.php">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']); ?>" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="user_email" class="form-control" value="<?= htmlspecialchars($user['user_email']); ?>" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']); ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city']); ?>" />
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Update Profile
                            </button>
                        </form>

                        <!-- Change Password Form -->
                        <hr class="my-4">
                        <h5 class="mb-3">Change Password</h5>
                        <form method="POST" action="server/change_password.php">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_new_password" class="form-control" required />
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key me-1"></i>Change Password
                            </button>
                        </form>

                        <!-- Orders Section -->
                        <hr class="my-4">
                        <h5 class="mb-3">Your Orders</h5>
                        <?php if (empty($orders)) : ?>
                            <p class="text-muted">No orders found.</p>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['id']); ?></td>
                                                <td><?= date('F j, Y', strtotime($order['created_at'])); ?></td>
                                                <td>रू <?= number_format($order['total_price'], 2); ?></td>
                                                <td><?= htmlspecialchars($order['status']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Appointments Section -->
                        <hr class="my-4">
                        <h5 class="mb-3">Your Appointments</h5>
                        <?php if (empty($appointments)) : ?>
                            <p class="text-muted">No appointments found.</p>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Appointment ID</th>
                                            <th>Date</th>
                                            <th>Doctor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appointment) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($appointment['id']); ?></td>
                                                <td><?= date('F j, Y H:i', strtotime($appointment['appointment_date'])); ?></td>
                                                <td><?= htmlspecialchars($appointment['doctor_name']); ?></td>
                                                <td><?= htmlspecialchars($appointment['status']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Back to Home Button -->
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> Shady Shades. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>