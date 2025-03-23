    <?php
    session_start();
    if (!isset($_SESSION["admin_logged_in"])) {
        header("Location: admin_login.php");
        exit();
    }

    include('server/connection.php'); // Include database connection

    if (isset($_GET['id'])) {
        $doctor_id = $_GET['id'];

        // Fetch doctor details
        $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_doctor'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $nmc_number = $_POST['nmc_number'];
        $specialization = $_POST['specialization'];
        $availability = json_encode($_POST['availability']);

        // Update doctor
        $stmt = $conn->prepare("UPDATE doctors SET full_name = ?, email = ?, phone = ?, nmc_number = ?, specialization = ?, availability = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $full_name, $email, $phone, $nmc_number, $specialization, $availability, $doctor_id);

        if ($stmt->execute()) {
            $_SESSION['alert_message'] = "Doctor updated successfully";
            $_SESSION['alert_type'] = "success";
            header("Location: view_doctors.php");
            exit();
        } else {
            $_SESSION['alert_message'] = "Failed to update doctor";
            $_SESSION['alert_type'] = "danger";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Doctor</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
                            <a class="nav-link" href="manage_products.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="view_doctors.php">Doctors</a>
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
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Edit Doctor</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($doctor['full_name']); ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($doctor['email']); ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number *</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($doctor['phone']); ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">NMC Number *</label>
                                    <input type="text" name="nmc_number" class="form-control" value="<?= htmlspecialchars($doctor['nmc_number']); ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Specialization</label>
                                    <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($doctor['specialization']); ?>" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Availability *</label>
                                    <textarea name="availability" class="form-control" required><?= htmlspecialchars($doctor['availability']); ?></textarea>
                                </div>
                                <button type="submit" name="update_doctor" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Update Doctor
                                </button>
                            </form>
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
    </body>
    </html>