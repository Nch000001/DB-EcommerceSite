<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();
$_SESSION['checkout_token'] = bin2hex(random_bytes(16));

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['selected_products'])) {
  header("Location: cart.php?error=expired");
  exit();
}

$selected = $_POST['selected_products'];
$errors = [];
$product_ids = [];
$parsed_items = [];

foreach ($selected as $entry) {
    list($product_id, $quantity) = explode(':', $entry);
    $quantity = intval($quantity);
    $product_ids[] = $product_id;
    $parsed_items[] = ['product_id' => $product_id, 'quantity' => $quantity]; // 要拿

    $stmt = $conn->prepare("SELECT stock_quantity FROM product WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $errors[] = "❌ 找不到商品 ID：$product_id";
        continue;
    }

    // if ($quantity > $row['stock_quantity']) {
    //     $errors[] = "❗ 商品 ID $product_id 的數量超過庫存（庫存為 {$row['stock_quantity']}，請求數量為 $quantity ）";
    // }
}

if (!empty($errors)) {
    echo implode("<br>", $errors);
    exit();
}

// ✅ 查詢商品名稱與價格
$product_info = [];
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$types = str_repeat('s', count($product_ids));
$stmt = $conn->prepare("SELECT product_id, product_name, price FROM product WHERE product_id IN ($placeholders)");
$stmt->bind_param($types, ...$product_ids);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $product_info[$row['product_id']] = [ // 拿
        'name' => $row['product_name'],
        'price' => $row['price']
    ];
}

// ✅ 取得使用者資料
$stmt = $conn->prepare("SELECT name, home_address, phone_numbers FROM user WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); // 拿

if (!$user) {
    echo "<script>alert('❌ 無法取得使用者資料。'); window.location.href='index.php';</script>";
    exit();
}

$_SESSION['checkout_items'] = $parsed_items; //這三個要傳
$_SESSION['checkout_products'] = $product_info;
$_SESSION['user'] = $user;

header("Location: checkout_confirm.php");
exit();
