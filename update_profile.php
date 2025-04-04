<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include('server/connection.php'); // Include database connection

$user_id = $_SESSION["user_id"];
$full_name = $_POST['full_name'];
$user_email = $_POST['user_email']; // Use 'user_email' instead of 'email'
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city']; // Add 'city' field

// Validate input (optional but recommended)
if (empty($full_name) || empty($user_email)) {
    $_SESSION['alert_message'] = "Full name and email are required";
    $_SESSION['alert_type'] = "danger";
    header("Location: profile.php");
    exit();
}

// Update user profile
$stmt = $conn->prepare("UPDATE users SET full_name = ?, user_email = ?, phone = ?, address = ?, city = ? WHERE id = ?");
$stmt->bind_param("sssssi", $full_name, $user_email, $phone, $address, $city, $user_id);

if ($stmt->execute()) {
    $_SESSION['alert_message'] = "Profile updated successfully";
    $_SESSION['alert_type'] = "success";
} else {
    $_SESSION['alert_message'] = "Failed to update profile";
    $_SESSION['alert_type'] = "danger";
}

header("Location: profile.php");
exit();
?>