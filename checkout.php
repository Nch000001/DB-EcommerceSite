<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['selected_products'])) {
    header("Location: cart.php");
    exit();
}

// 用過就立即刪除 token
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['checkout_token']) {
    showErrorPage(["⚠️ 不可重複提交表單，請從購物車重新下單"]);
    exit();
}
unset($_SESSION['checkout_token']);


$selected = $_POST['selected_products'];
$errors = [];
$product_ids = [];
$parsed_items = [];
$total_amount = 0;

foreach ($selected as $entry) {
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
        $errors[] = "❗ 商品「{$name}」的數量超過庫存（庫存：{$row['stock_quantity']}，請求：{$quantity}）";
    }
}

if (!empty($errors)) {
    showErrorPage($errors);
    exit();
}

foreach ($parsed_items as $item) {
    $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
    $stmt->bind_param("is", $item['quantity'], $item['product_id']);
    $stmt->execute();
}

$status = 'not pay';
$stmt = $conn->prepare("INSERT INTO orders (user_id, status, total_amount) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $user_id, $status, $total_amount);
if (!$stmt->execute()) {
    showErrorPage(["❌ 訂單建立失敗：" . $stmt->error]);
    exit();
}
$order_id = $conn->insert_id;

$stmtItem = $conn->prepare("INSERT INTO order_item (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
foreach ($parsed_items as $item) {
    $stmtItem->bind_param("issii", $order_id, $item['product_id'], $item['name'], $item['price'], $item['quantity']);
    if (!$stmtItem->execute()) {
        showErrorPage(["❌ 訂單明細建立失敗：" . $stmtItem->error]);
        exit();
    }
}

$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$types = str_repeat('s', count($product_ids));
$sql = "DELETE FROM cart WHERE user_id = ? AND product_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s' . $types, $user_id, ...$product_ids);
$stmt->execute();


showSuccessPage($order_id, $total_amount);

exit();


// ===== ✅ 顯示成功畫面 =====
function showSuccessPage($order_id, $total_amount) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <title>訂單完成</title>
        <style>
            body {
                font-family: 'Noto Sans TC', sans-serif;
                background-color: #f2fff3;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            .box {
                background: #fff;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                border-left: 8px solid #2ecc71;
                max-width: 600px;
                text-align: center; /* ✅ 關鍵：讓內部文字和按鈕置中 */
            }

            .box h1 {
                color: #2ecc71;
                margin-bottom: 20px;
            }

            .box p {
                font-size: 18px;
                color: #333;
            }

            .box a {
                display: inline-block;
                margin: 30px auto 0; /* ✅ 按鈕置中 */
                text-decoration: none;
                background-color: #2ecc71;
                color: white;
                padding: 10px 20px;
                border-radius: 8px;
                transition: background-color 0.3s ease;
            }

            .box a:hover {
                background-color: #27ae60;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>🎉 訂單建立成功！</h1>
            <p>訂單編號：#<?php echo htmlspecialchars($order_id); ?></p>
            <p>總金額：$<?php echo number_format($total_amount); ?></p>
            <a href="index.php">返回首頁</a>
        </div>
    </body>
    </html>
    <?php
}

// ===== ❌ 顯示錯誤畫面 =====
function showErrorPage($errors) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <title>錯誤訊息</title>
        <style>
            body {
                font-family: 'Noto Sans TC', sans-serif;
                background-color: #fff5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            .box {
                background: #fff;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                border-left: 8px solid #e74c3c;
                max-width: 800px;
                text-align: center; /* ✅ 關鍵：讓內部文字和按鈕置中 */
            }

            .box h1 {
                color: #e74c3c;
                margin-bottom: 20px;
            }

            .box ul {
                color: #c0392b;
                font-size: 16px;
                padding-left: 20px;
                text-align: left; /* ✅ 讓文字靠左 */
            }

            .box a {
                display: inline-block;
                margin: 30px auto 0; /* ✅ 按鈕置中 */
                text-decoration: none;
                background-color: #e74c3c;
                color: white;
                padding: 10px 20px;
                border-radius: 8px;
                transition: background-color 0.3s ease;
            }

            .box a:hover {
                background-color: #c0392b;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>❌ 訂單建立失敗</h1>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="cart.php">返回購物車</a>
        </div>
    </body>
    </html>
    <?php
}
?>