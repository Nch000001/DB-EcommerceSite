<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_POST['selected_products'])) {
    echo "❗ 請先選擇商品再進行結帳。";
    exit();
}

$selected = $_POST['selected_products'];
$errors = [];

foreach ($selected as $product_id) {
    // 你可以根據實際送出的表單把數量也一起處理，這裡假設你有送出 quantity 資料（需調整表單送出方式）
    $quantity = intval($_POST['quantities'][$product_id] ?? 1);

    $stmt = $conn->prepare("SELECT stock_quantity FROM product WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || $quantity > $row['stock_quantity']) {
        $errors[] = "商品 ID $product_id 的數量超過庫存。";
    }
}

if (!empty($errors)) {
    echo implode("<br>", $errors);
    exit();
}

// ✅ 接下來可進行新增訂單或其他結帳流程
echo "✅ 所有商品庫存確認成功，可結帳！";

?>