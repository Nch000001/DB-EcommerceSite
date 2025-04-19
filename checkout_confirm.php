<?php

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['checkout_items']) || !isset($_SESSION['checkout_token'])) { // 多加一份token 防止從成功畫面返回
    header("Location: cart.php?error=expired");
    exit();
}

$parsed_items = $_SESSION['checkout_items'];
$product_info = $_SESSION['checkout_products'];
$user = $_SESSION['user'];

$token = $_SESSION['checkout_token'];

$total = 0;
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>結帳確認</title>
  <style>
    body { font-family: 'Noto Sans TC', sans-serif; background-color: #f0f2f5; padding: 40px; }
    .form-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px 40px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { text-align: center; margin-bottom: 20px; }
    label { font-weight: bold; margin-top: 15px; display: block; }
    input[type="text"], select { width: 100%; padding: 10px; font-size: 16px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;}
    input[required], select[required] { border-color: #f44336; }
    .submit-btn { margin-top: 25px; background-color: #4CAF50; color: white; font-size: 16px; padding: 12px; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
    .submit-btn:hover { background-color: #45A049; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 12px; text-align: center; }
    th { background-color: #f0f0f0; }
    .total-row td { font-weight: bold; background-color: #f7f7f7; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>確認結帳資訊</h2>

    <form method="POST" action="checkout.php">
      <label>收件人姓名 *</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

      <label>收件人地址 *</label>
      <input type="text" name="home_address" value="<?php echo htmlspecialchars($user['home_address']); ?>" required>

      <label>收件人電話 *</label>
      <input type="text" name="phone_numbers" value="<?php echo htmlspecialchars($user['phone_numbers']); ?>" pattern="09\d{8}" title="請輸入以 09 開頭的 10 碼手機號碼" required>

      <label>付款方式 *</label>
      <select name="payment_method" required>
        <option value="cash">現金</option>
        <!-- <option value="credit">信用卡</option>
        <option value="atm">ATM 轉帳</option> -->
      </select>

      <!-- <label>運送方式 *</label>
      <select name="shipping_method" required>
        <option value="yourself">自取</option>
        <option value="home">宅配</option>
        <option value="store">超商取貨</option>
      </select> -->

      <br><br>
      <h2>🛒 訂單明細</h2>
      <table>
        <thead>
          <tr>
            <th>商品名稱</th>
            <th>單價</th>
            <th>數量</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $total = 0;
          foreach ($parsed_items as $item):
            $info = $product_info[$item['product_id']] ?? ['name' => $item['product_id'], 'price' => 0];
            $subtotal = $info['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
          <tr>
            <td><?php echo htmlspecialchars($info['name']); ?></td>
            <td>$<?php echo number_format($info['price']); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>$<?php echo number_format($subtotal); ?></td>
          </tr>
          <input type="hidden" name="selected_products[]" value="<?php echo htmlspecialchars($item['product_id'] . ':' . $info['name'] . ',' . $info['price'] . ',' . $item['quantity']); ?>">
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="3">總金額</td>
            <td>$<?php echo number_format($total); ?></td>
          </tr>
        </tfoot>
      </table>
      
      <input type="hidden" name="token" value="<?php echo $_SESSION['checkout_token']; ?>">
      <button type="submit" class="submit-btn">✅ 確認並提交訂單</button>
    </form>
  </div>

  <button onclick="goBack()" style="
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1000;
    background-color: #888;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  ">🔙 返回上一頁</button>


<script>
function goBack() {
  window.location.href = 'cart.php'; // 沒有上一頁就回首頁
}
</script>
</body>
</html>