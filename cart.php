<?php
session_start();
require_once './lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$sql = "
    SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_path, p.stock_quantity
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
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .cart-header h2 { margin: 0; font-size: 24px; }
        .actions button { background: linear-gradient(to right, #4CAF50, #45A049); border: none; color: white; padding: 8px 14px; font-size: 14px; border-radius: 6px; cursor: pointer; margin-left: 10px; }
        .actions button:hover { background: linear-gradient(to right, #45A049, #4CAF50); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .cart-item { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .cart-item img { width: 100px; height: auto; margin-right: 20px; }
        .cart-item-details { flex: 1; display: flex; flex-direction: column; }
        .cart-item-row { display: flex; align-items: center; justify-content: space-between; }
        .quantity-control { display: flex; align-items: center; margin-top: 5px; }
        .qty-btn { padding: 6px 12px; font-size: 16px; background-color: #f0f0f0; border: 1px solid #ccc; border-radius: 6px; cursor: pointer; transition: background-color 0.2s ease; }
        .qty-btn:hover { background-color: #ddd; }
        .qty-input { width: 60px; text-align: center; font-size: 16px; margin: 0 8px; padding: 6px; border: 1px solid #ccc; border-radius: 6px; }
        .subtotal { margin-top: 5px; font-size: 16px; }
        .subtotal-value { font-weight: bold; color: #e91e63; }
        .delete-btn { padding: 6px 12px; font-size: 14px; background-color: #f44336; border: none; color: white; border-radius: 6px; cursor: pointer; margin-left: 10px; }
        .floating-summary { position: fixed; top: 20px; right: 20px; background-color: rgba(255, 255, 255, 0.95); padding: 20px 24px; border-radius: 16px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 999; width: 250px; text-align: center; }
        .floating-summary button { margin-top: 15px; width: 100%; padding: 12px; font-size: 16px; border: none; border-radius: 8px; background: linear-gradient(to right, #4CAF50, #45A049); color: white; cursor: pointer; transition: 0.3s; }
        .floating-summary button:hover { background: linear-gradient(to right, #45A049, #4CAF50); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .total-price { font-size: 24px; font-weight: bold; color: #4CAF50; margin-top: 8px; }
        .floating-summary button.active {
            background: linear-gradient(to right, #f44336, #e53935); /* 紅色漸層 */
            color: white;
            font-weight: bold;
            animation: pulse 1s infinite;
        }
    </style>
</head>
<body>

<div class="floating-summary">
    <div id="totalDisplay">
        <div>總金額</div>
        <div class="total-price">$<?php echo $total; ?></div>
    </div>
    <button type="button" id="checkoutBtn" onclick="tryCheckout()">✅ 確認購買</button>
</div>

<div class="cart-container">
    <?php if (empty($cartItems)): ?>
        <h2>🛒 我的購物車</h2>
        <p>您的購物車是空的。</p>
    <?php else: ?>
        <form id="cartForm" action="checkout.php" method="POST" onsubmit="return false;">
            <div class="cart-header">
                <h2>🛒 我的購物車</h2>
                <div class="actions">
                    <button type="button" onclick="toggleSelectAll()">全選 / 取消全選</button>
                </div>
            </div>

            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item product-box" data-subtotal="<?php echo $item['subtotal']; ?>">
                    <input type="checkbox" class="product-checkbox" name="selected_products[]" value="<?php echo $item['product_id']; ?>" checked onchange="updateTotal()">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="商品圖片">
                    <div class="cart-item-details">
                        <div><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></div>
                        <div class="cart-item-row">
                            <div class="price">單價：$<?php echo $item['price']; ?></div>
                            <div class="quantity-control">
                                <button type="button" class="qty-btn" onclick="changeQty(this, -1)">−</button>
                                <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['stock_quantity']; ?>" data-price="<?php echo $item['price']; ?>" data-product-id="<?php echo $item['product_id']; ?>" onchange="updateSubtotal(this, event)">
                                <button type="button" class="qty-btn" onclick="changeQty(this, 1)">＋</button>
                            </div>
                            <div class="subtotal">小計：<span class="subtotal-value">$<?php echo $item['subtotal']; ?></span></div>
                            <button type="button" class="delete-btn" onclick="deleteProduct('<?php echo $item['product_id']; ?>', 'delete')">刪除商品</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    <?php endif; ?>
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
    if (document.referrer) {
        window.history.back();
    } else {
        window.location.href = 'index.php'; // 沒有上一頁就回首頁
    }
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    updateTotal();
}

function tryCheckout() {
    const totalText = document.querySelector('#totalDisplay .total-price').innerText;
    const total = parseFloat(totalText.replace('$', ''));

    if (total > 0) {
        document.getElementById('cartForm').submit();
    } else {
        alert('請至少選擇一項商品才能結帳！');
    }
}


function updateTotal() {
    let total = 0;
    document.querySelectorAll('.product-box').forEach(item => {
        const checkbox = item.querySelector('.product-checkbox');
        const qtyInput = item.querySelector('.qty-input');
        const price = parseFloat(qtyInput.dataset.price);
        const qty = parseInt(qtyInput.value);
        const subtotal = qty * price;

        if (checkbox.checked) {
            total += subtotal;
        }

        item.querySelector('.subtotal-value').innerText = '$' + subtotal;
        item.setAttribute('data-subtotal', subtotal);
    });

    document.querySelector('#totalDisplay .total-price').innerText = '$' + total;

    // ✅ 修改按鈕行為與文字
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (total > 0) {
        checkoutBtn.disabled = false;
        checkoutBtn.innerText = '✅ 確認購買';
        checkoutBtn.classList.remove('active'); // ❌ 移除紅色
    } else {
        checkoutBtn.disabled = true;
        checkoutBtn.innerText = '請選擇商品';
        checkoutBtn.classList.add('active'); // ✅ 加紅色 + 動畫
    }
}

function changeQty(button, delta) {
    const input = button.parentElement.querySelector('.qty-input');
    let current = parseInt(input.value);
    const max = parseInt(input.max);
    const min = parseInt(input.min);
    current += delta;
    if (current < min) current = min;
    if (current > max) current = max;
    input.value = current;
    updateSubtotal(input);
}

function updateSubtotal(input, event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const max = parseInt(input.max);
    const productId = input.dataset.productId;
    let val = parseInt(input.value);

    if (val < 0) val = 0;
    if (val > max) {
        alert("❗ 數量超過庫存上限！");
        val = max;
    }
    input.value = val;

    if (val === 0) {
        if (confirm('數量為 0，是否從購物車中移除這項商品？')) {
            deleteProduct(productId);
            return;
        } else {
            input.value = 1;
        }
    }


    // 🔄 發送更新請求到後端
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: val
        })
    }).then(res => res.text())
    .then(console.log) // ✅ 可視需求換成 alert/debug

    updateTotal();
}

function deleteProduct(productId, from = '') {
    if (from === 'delete') {
        if (!confirm('確定要刪除這項商品嗎？')) {
            return;
        }
    }

    // 🔄 使用 fetch 呼叫 update_cart.php，quantity = 0 表示刪除
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 0
        })
    })
    .then(res => res.text())
    .then(msg => {
        console.log(msg);
        // ✅ 從畫面上移除該商品區塊
        const productBox = document.querySelector(`.product-box input[value="${productId}"]`)?.closest('.product-box');
        if (productBox) {
            productBox.remove();
        }

        // ✅ 更新總金額顯示
        updateTotal();
        const remainingItems = document.querySelectorAll('.product-box');
        if (remainingItems.length === 0) {
            const cartContainer = document.querySelector('.cart-container');
            cartContainer.innerHTML = `
                <h2>🛒 我的購物車</h2>
                <p>您的購物車是空的。</p>
            `;

            // ✅ 移除浮動結帳區塊（可選）
            // const summary = document.querySelector('.floating-summary');
            // if (summary) summary.remove();
        }
    })
    .catch(err => {
        console.error('刪除失敗:', err);
        alert('⚠️ 刪除商品時發生錯誤，請稍後再試');
    });
}


document.querySelectorAll('.cart-item.product-box').forEach(box => {
    box.addEventListener('click', function (e) {
        // 防止當你點到某些內部元素時造成重複觸發
        if (e.target.classList.contains('qty-btn') ||
            e.target.classList.contains('qty-input') ||
            e.target.classList.contains('delete-btn')) {
            return; // 忽略這些按鈕或輸入欄位
        }

        const checkbox = this.querySelector('.product-checkbox');
        checkbox.checked = !checkbox.checked;
        updateTotal();
    });
});

window.addEventListener('DOMContentLoaded', function () {
    updateTotal();
});
</script>

</body>
</html>
