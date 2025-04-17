<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

// 未登入導回登入頁
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 查詢購物車資料 + 對應商品資料
$sql = "
    SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_path
    FROM cart c
    JOIN product p ON c.product_id = p.product_id
    WHERE c.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['quantity'] * $row['price'];
    $row['subtotal'] = $subtotal;
    $cartItems[] = $row;
    $total += $subtotal;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>我的購物車</title>
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; padding: 20px; background-color: #f8f8f8; }
        .cart-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .cart-item { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .cart-item img { width: 100px; height: auto; margin-right: 20px; }
        .cart-item-details { flex: 1; }
        .price, .quantity, .subtotal { margin-top: 5px; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

<div class="cart-container">
    <h2>🛒 我的購物車</h2>

    <?php if (empty($cartItems)): ?>
        <p>您的購物車是空的。</p>
    <?php else: ?>
        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="商品圖片">
                <div class="cart-item-details">
                    <div><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></div>
                    <div class="price">單價：$<?php echo $item['price']; ?></div>
                    <div class="quantity">數量：<?php echo $item['quantity']; ?></div>
                    <div class="subtotal">小計：$<?php echo $item['subtotal']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="total">總金額：$<?php echo $total; ?></div>
    <?php endif; ?>
</div>

</body>
</html>