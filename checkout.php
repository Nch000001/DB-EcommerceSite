<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>結帳確認</title>
</head>
</html>

<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (empty($_POST['selected_products'])) {
    echo "❗ 請先選擇商品再進行結帳。";
    header("Location: cart.php");
    exit();
}

// 🧪 除錯印出 POST 資料
echo "<pre>";
print_r($_POST);
echo "</pre>";

$selected = $_POST['selected_products'];
$errors = [];
$product_ids = [];
$parsed_items = [];
$total_amount = 0;

foreach ($selected as $entry) {
    // 將 product_id 和其餘資訊分開
    list($product_id, $rest) = explode(':', $entry, 2);
    list($name, $price, $quantity) = explode(',', $rest);

    $quantity = intval($quantity);
    $price = floatval($price);

    $product_ids[] = $product_id;
    $parsed_items[] = [
        'product_id' => $product_id,
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity
    ];
    $total_amount += $price * $quantity;

    // 查詢該商品目前的庫存
    $stmt = $conn->prepare("SELECT stock_quantity FROM product WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $errors[] = "❌ 找不到商品 ID：$product_id";
        continue;
    }

    if ($quantity > $row['stock_quantity']) {
        $errors[] = "❗ 商品「{$name}」的數量超過庫存（庫存：{$row['stock_quantity']} : 請求：{$quantity}）";    }
    }

// 顯示錯誤訊息（若有）
if (!empty($errors)) {
  $msg = implode("\n", $errors);
  echo "<script>alert(" . json_encode($msg) . "); window.location.href = 'cart.php';</script>";
  exit();
}

//扣除每一項商品的庫存
foreach ($parsed_items as $item) {
  $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
  $stmt->bind_param("is", $item['quantity'], $item['product_id']);
  $stmt->execute();
}



//插入訂單至 orders 表
$status = 'not pay';
$stmt = $conn->prepare("INSERT INTO orders (user_id, status, total_amount) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $user_id, $status, $total_amount);
if (!$stmt->execute()) {
    echo "❌ 插入失敗：(" . $stmt->errno . ") " . $stmt->error . "<br>";
    exit();
}
$order_id = $conn->insert_id;


// //插入每一筆訂單明細到 order_items
$stmtItem = $conn->prepare("INSERT INTO order_item (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
foreach ($parsed_items as $item) {
    $stmtItem->bind_param("issii", $order_id, $item['product_id'], $item['name'], $item['price'], $item['quantity']);
    if (!$stmtItem->execute()) {
        echo "❌ 插入失敗：(" . $stmt->errno . ") " . $stmt->error . "<br>";
        exit();
    }
}

// 刪除這些商品在購物車中的紀錄
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$types = str_repeat('s', count($product_ids));
$sql = "DELETE FROM cart WHERE user_id = ? AND product_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s' . $types, $user_id, ...$product_ids);
$stmt->execute();

// ✅ 顯示成功訊息並跳轉
echo "<script>alert('✅ 訂單已成功建立！訂單編號：#{$order_id}'); window.location.href = 'index.php';</script>";
exit();
?>