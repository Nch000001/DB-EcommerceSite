<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

header('Content-Type: text/plain'); // 一定記得加！

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "未登入";
    exit();
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    http_response_code(400);
    echo "參數錯誤";
    exit();
}

$product_id = $data['product_id'];
$quantity = (int)$data['quantity'];

// 驗證數量
if ($quantity < 0) {
    http_response_code(400);
    echo "❌ 數量無效";
    exit();
}

// ✅ 如果 quantity 為 0，從購物車刪除該項目
if ($quantity === 0) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ss", $user_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "🗑️ 商品已從購物車中移除";
    } else {
        echo "⚠️ 找不到商品或已刪除";
    }
    exit();
}

// ✅ 否則更新購物車數量
$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("iss", $quantity, $user_id, $product_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "✅ 數量已更新為 $quantity";
} else {
    echo "⚠️ 數量未變更或找不到商品";
}
?>