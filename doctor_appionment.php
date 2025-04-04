<?php
session_start();

// Check if user is logged in and is a doctor
if (!isset($_SESSION["user_id"]) || ($_SESSION["user_type"] ?? '') !== 'doctor') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: doctor_appointments.php");
    exit();
}

include("connection.php");

$appointment_id = $_GET["id"];
$doctor_id = $_SESSION["user_id"];

// Fetch appointment details
$stmt = $conn->prepare("
    SELECT a.*, u.full_name, u.email, u.phone 
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: doctor_appointments.php");
    exit();
}

$appointment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title">Appointment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h5>Patient Information</h5>
                            <p><strong>Name:</strong> <?= htmlspecialchars($appointment['full_name']); ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($appointment['email']); ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($appointment['phone']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <h5>Appointment Details</h5>
                            <p><strong>Date & Time:</strong> <?= date('M j, Y h:i A', strtotime($appointment['appointment_date'])); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?= 
                                    $appointment['status'] === 'pending' ? 'warning' : 
                                    ($appointment['status'] === 'confirmed' ? 'success' : 
                                    ($appointment['status'] === 'completed' ? 'info' : 'secondary')) ?>">
                                    <?= ucfirst($appointment['status']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <?php if (!empty($appointment['prescription'])): ?>
                            <div class="mb-3">
                                <h5>Prescription</h5>
                                <div class="border p-3"><?= nl2br(htmlspecialchars($appointment['prescription'])); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="update_appointment.php">
                            <input type="hidden" name="appointment_id" value="<?= $appointment['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Update Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pending" <?= $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?= $appointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?= $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?= $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="prescription" class="form-label">Prescription</label>
                                <textarea class="form-control" id="prescription" name="prescription" rows="5"><?= htmlspecialchars($appointment['prescription'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Appointment</button>
                            <a href="doctor_appointments.php" class="btn btn-secondary">Back to Appointments</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>