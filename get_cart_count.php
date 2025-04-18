<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'cartCount' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($cartCount);
$stmt->fetch();
$stmt->close();

echo json_encode(['success' => true, 'cartCount' => (int)$cartCount]);
?>