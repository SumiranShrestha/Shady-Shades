<?php
session_start();

// Check if user is logged in and is a doctor
if (!isset($_SESSION["user_id"]) || ($_SESSION["user_type"] ?? '') !== 'doctor') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST["appointment_id"])) {
    header("Location: doctor_appointments.php");
    exit();
}

include("connection.php");

$appointment_id = $_POST["appointment_id"];
$status = $_POST["status"] ?? 'pending';
$prescription = $_POST["prescription"] ?? '';
$doctor_id = $_SESSION["user_id"];

// Update appointment
$stmt = $conn->prepare("
    UPDATE appointments 
    SET status = ?, prescription = ?
    WHERE id = ? AND doctor_id = ?
");
$stmt->bind_param("ssii", $status, $prescription, $appointment_id, $doctor_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION["success"] = "Appointment updated successfully!";
} else {
    $_SESSION["error"] = "Failed to update appointment.";
}

header("Location: doctor_appointment.php?id=" . $appointment_id);
exit();
?>