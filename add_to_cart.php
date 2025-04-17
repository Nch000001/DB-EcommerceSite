<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once './lib/db.php';
$conn = getDBConnection();

// 取得商品 ID
if (!isset($_GET['product_id'])) {
    echo "❌ 沒有指定商品 ID";
    exit();
}

$product_id = $_GET['product_id'];

// 如果尚未登入，導向登入頁，並記錄欲加入購物車的商品
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "add_to_cart.php?product_id=" . urlencode($product_id);
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$quantity = 1;

// ✅ 檢查庫存數量
$sqlStock = "SELECT stock_quantity FROM product WHERE product_id = ?";
$stmtStock = $conn->prepare($sqlStock);
$stmtStock->bind_param("s", $product_id);
$stmtStock->execute();
$resultStock = $stmtStock->get_result();

if ($resultStock->num_rows === 0) {
    echo "<script>alert('查無此商品'); window.history.back();</script>";
    exit();
}

$rowStock = $resultStock->fetch_assoc();
if ($rowStock['stock_quantity'] <= 0) {
    echo "<script>alert('此商品已售完'); window.history.back();</script>";
    exit();
}

// ✅ 檢查購物車中是否已有此商品
$sqlCheck = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $user_id, $product_id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // ✅ 已存在，更新數量前先檢查庫存是否足夠
    $rowCart = $result->fetch_assoc();
    $newQty = $rowCart['quantity'] + 1;

    if ($newQty > $rowStock['stock_quantity']) {
        echo "<script>alert('超過庫存數量，無法加入更多'); window.history.back();</script>";
        exit();
    }

    $sqlUpdate = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ss", $user_id, $product_id);
    $stmtUpdate->execute();
} else {
    // ✅ 不存在，新增前檢查庫存是否足夠
    if ($quantity > $rowStock['stock_quantity']) {
        echo "<script>alert('庫存不足'); window.history.back();</script>";
        exit();
    }

    $sqlInsert = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ssi", $user_id, $product_id, $quantity);
    $stmtInsert->execute();
}

// ✅ 成功加入購物車，導向購物車頁面
header("Location: cart.php");
exit();
?>