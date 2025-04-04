<?php
session_start();
include('connection.php');

header('Content-Type: application/json');

$user_name = trim($_POST['username']);
$user_email = trim($_POST['email']);
$user_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered."]);
    exit();
}
$stmt->close();

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user_name, $user_email, $user_password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_email'] = $user_email;

    echo json_encode(["status" => "success", "message" => "Signup successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Signup failed."]);
}
$stmt->close();
$conn->close();
?>
