<?php
session_start();
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$city = $_POST['city'];
$address = $_POST['address'];

// Calculate grand total (you can fetch this from the session or recalculate it here)
$grand_total = 0; // Replace with actual calculation

// Insert order into the database
$stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, email, phone, city, address, total_price, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("isssssd", $user_id, $full_name, $email, $phone, $city, $address, $grand_total);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    echo json_encode(["status" => "success", "order_id" => $order_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to place order"]);
}
?>