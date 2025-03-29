<?php
// Ensure no output is sent before headers
ob_start();
include('header.php');
require_once("server/connection.php");

// Check if required parameters exist
if (!isset($_GET['prescription_id']) || !isset($_GET['product_id'])) {
    ob_end_clean(); // Clean any output buffer
    header("Location: prescription-frames.php");
    exit();
}

$prescription_id = $_GET['prescription_id'];
$product_id = $_GET['product_id'];
$user_id = $_SESSION['user_id'] ?? null;

// Verify the prescription belongs to the user
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM prescription_frames WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $prescription_id, $user_id);
    $stmt->execute();
    $prescription = $stmt->get_result()->fetch_assoc();
} else {
    ob_end_clean(); // Clean any output buffer
    header("Location: login.php?redirect=prescription_order.php?" . $_SERVER['QUERY_STRING']);
    exit();
}

// Get product details
$stmt = $conn->prepare("SELECT p.*, b.brand_name FROM products p 
                       JOIN brands b ON p.brand_id = b.id 
                       WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$prescription || !$product) {
    ob_end_clean(); // Clean any output buffer
    header("Location: prescription-frames.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create the prescription order
    $stmt = $conn->prepare("INSERT INTO prescription_orders 
                          (user_id, product_id, prescription_id, 
                           right_eye_sphere, right_eye_cylinder, right_eye_axis, right_eye_pd,
                           left_eye_sphere, left_eye_cylinder, left_eye_axis, left_eye_pd,
                           lens_type, coating_type, frame_color, frame_size, status)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted')");
    
    $stmt->bind_param("iiiddddddddssss", 
        $user_id, $product_id, $prescription_id,
        $prescription['right_eye_sphere'], $prescription['right_eye_cylinder'], 
        $prescription['right_eye_axis'], $prescription['right_eye_pd'],
        $prescription['left_eye_sphere'], $prescription['left_eye_cylinder'], 
        $prescription['left_eye_axis'], $prescription['left_eye_pd'],
        $_POST['lens_type'], $_POST['coating_type'],
        $_POST['frame_color'], $_POST['frame_size']
    );
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        ob_end_clean(); // Clean any output buffer
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } else {
        $error = "Error submitting your order. Please try again.";
    }
}

// Flush the output buffer
ob_end_flush();
?>

<!-- Rest of your HTML content remains the same -->
<main class="container my-5">
    [... rest of your existing HTML/PHP code ...]
</main>

<?php include('footer.php'); ?>