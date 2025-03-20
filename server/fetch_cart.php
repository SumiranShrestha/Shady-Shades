<?php
session_start();
header('Content-Type: application/json');
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["items" => [], "total" => 0, "count" => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.product_id, p.name, p.price, p.discount_price, p.images, c.quantity 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $product_price = $row['discount_price'] > 0 ? $row['discount_price'] : $row['price'];
    $total += $product_price * $row['quantity'];

    $images = json_decode($row['images'], true);
    $image = $images[0] ?? 'default.jpg';

    $items[] = [
        "id" => $row['product_id'],
        "name" => $row['name'],
        "price" => number_format($product_price, 2),
        "quantity" => $row['quantity'],
        "image" => $image
    ];
}

echo json_encode([
    "items" => $items,
    "total" => number_format($total, 2),
    "count" => count($items)
]);

exit();
?>
