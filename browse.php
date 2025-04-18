<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './lib/db.php';
$conn = getDBConnection();

require_once './lib/product_filter.php';

$category_id = $_GET['category_id'] ?? '';
$selected_tags = $_GET['tag_id'] ?? [];

[$tag_types, $product_result] = getProductFilterResults($conn, $category_id, $selected_tags);

// 撈分類列用的
$categoryQuery = "SELECT category_id, name FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);

$categories = [];
if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $row;
    }
}

?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>商品篩選瀏覽頁</title>
  <style>
    * { box-sizing: border-box; }

    html {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1; /* 🔥 主內容自動撐開，其餘交給 header/footer */
    }

    body { font-family: 'Noto Sans TC', sans-serif; margin: 0; background-color: #f5f5f5; }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 999;
      
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #1E3A8A;
      padding: 15px 20px;
      color: white;
    }
    .navbar .logo a {
      font-size: 24px;
      font-weight: bold;
      color: white;
      text-decoration: none;
    }
    .nav-links {
      display: flex;
      gap: 10px;
    }
    .nav-links a {
      background-color: #D4AF37;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 20px;
    }
    .search-bar { flex-grow: 1; display: flex; justify-content: center; }
    .search-bar input { width: 100%; padding: 8px 12PX; border: 1px solid #CCC; border-radius: 5px; max-width: 600px; font-size: 16px;}
    
    .category-bar {
        margin-top: 70px;
        display: flex;
        justify-content: center;
        background-color: #DDD;
        padding: 5px 0;
        gap: 100px; /* 分類間距 */
        flex-wrap: wrap;
        background-color:rgb(182, 189, 189);
    }

    .category-item {
        display: flex;
    }

    .category-item a {
        font-size: 18px;
        cursor: pointer;
        padding: 10px 20px;
        border-radius: 10px;
        transition: background-color 0.1s ease;
        text-decoration: none;
        color: #333;
    }

    .category-item a:hover {
        background-color: #1E3A8A;
        color: white;
    }


    .container { max-width: 1300px; margin: auto; padding: 20px; padding-top: 60px;}

    .filter-box {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        position: relative; /* 為右下角按鈕做定位 */
    }

    /* 每列顯示三個 tag_type 群組 */
    .tag-type-group {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    /* 每一組 tag_type（觸發結構、連接方式...） */
    .filter-group {
        margin-bottom: 10px;
    }

    /* tag_type 標題 */
    .filter-title {
        font-weight: bold;
        margin-bottom: 8px;
    }

    /* 每組內的 tag 標籤 */
    .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* 單一標籤樣式 */
    .filter-options label {
        background: #f0f0f0;
        padding: 6px 12px;
        border-radius: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    .filter-options input {
        margin-right: 5px;
    }

    /* ✨ 按鈕定位到右下角 */
    .filter-submit {
        position: absolute;
        right: 20px;
        bottom: 20px;
    }
    
    /* 商品區 */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }
    .product-card {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .product-card img {
        width: 100%;
        height: 140px;
        object-fit: contain;
        margin-bottom: 10px;
    }
    .product-card h3 {
        font-size: 16px;
        margin: 0 0 10px;
        height: 3.2em;
        overflow: hidden;
        line-height: 1.6em;
    }
    .product-card .price {
        font-weight: bold;
        color: #d72638;
        margin-bottom: 10px;
    }
    .product-card a {
        display: inline-block;
        padding: 6px 12px;
        background: #1e3a8a;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }

    /* 頁面左右保留邊距 */
    @media (max-width: 1440px) {
      .container { padding: 20px 30px; }
    }

    .footer {
        background-color: #333; color: white; text-align: center;
        padding: 20px; font-size: 14px;
    }

    /* 購物車 */
    .floating-cart-btn {
        position: fixed;
        top: 600px;         /* 與 navbar 有距離 */
        right: 20px;
        background-color: #D4AF37;
        color: white;
        padding: 12px 18px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 1000;
        transition: background-color 0.3s ease;
    }
    .floating-cart-btn:hover {
        background-color: #b18f27;
    }

  </style>
</head>
<body>
    <!-- 頁首區域 -->
    <header class="navbar">
        <div class="logo"><a href="index.php">LOGO</a></div>  
        <div class="search-bar"><input type="text" placeholder="搜尋產品..."></div>   <!-- 搜尋欄  算法待定 -->

        <div class="nav-links">
            <a href="#">會員</a>
            <a href="#">問題</a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php">註冊</a>
                <a href="login.php">登入</a>
            <?php else: ?>
                <a href="logout.php">登出</a>
            <?php endif; ?>
        </div>

    </header>

    <div class="category-bar">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <div class="category-item">
                <a href="browse.php?category_id=<?php echo $cat['category_id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <span>目前尚無分類</span>
        <?php endif; ?>
    </div>

    <div class="container">
        <form method="get" class="filter-box">
            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">

            <div class="tag-type-group">
                <?php foreach ($tag_types as $type): ?>
                    <div>
                    <strong><?php echo htmlspecialchars($type['tag_type_name']); ?></strong>
                    <div class="filter-options">
                        <?php foreach ($type['tags'] as $tag): ?>
                        <label>
                            <input type="checkbox" name="tag_id[]" value="<?php echo $tag['tag_id']; ?>"
                            <?php echo in_array($tag['tag_id'], $selected_tags) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary filter-submit">套用篩選</button>
        </form>

        <div class="product-grid">
            <?php if ($product_result && $product_result->num_rows > 0): ?>
                <?php while ($product = $product_result->fetch_assoc()): ?>

                    <div class="product-card" onclick="goToProduct('<?php echo $product['product_id']; ?>')">

                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="商品圖片">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>

                        <div class="price">$<?php echo number_format($product['price']); ?></div>
                        <a href="add_to_cart.php?product_id=<?php echo urlencode($product['product_id']); ?>">立即購買</a>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>目前沒有符合的商品。</p>
            <?php endif; ?>
        </div>

    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="cart.php" class="floating-cart-btn">
            🛒 購物車 (0)
        </a>
    <?php endif; ?>

    <div class="footer">
        <div class="contact">
            <span>電話: 0900000000</span>　
            <span>Email: dbecommercesite@gmail.com</span>　
            <span>地址: 407802臺中市西屯區文華路100號</span>
        </div>
    </div>

<script>
    function goToProduct(productId) {
        window.location.href = 'item.php?product_id=' + encodeURIComponent(productId);
    }



    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.buy-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault(); // 阻止預設跳轉，先做 fetch 再跳

                const productId = this.dataset.productId;
                const targetUrl = this.href;

                // 發送加入購物車請求
                fetch('add_to_cart.php?product_id=' + encodeURIComponent(productId))
                    .then(res => res.text()) // 原本 add_to_cart.php 沒回應 JSON，可以忽略內容
                    .then(() => {
                        // 更新購物車數量
                        return fetch('get_cart_count.php');
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            updateCartCountDisplay(data.cartCount);
                        }
                        // ✅ 確保更新完後再跳轉
                        window.location.href = targetUrl;
                    })
                    .catch(err => {
                        console.error('加入購物車錯誤:', err);
                        window.location.href = targetUrl; // 即使失敗也照樣跳轉
                    });
            });
        });
    });

    function updateCartCountDisplay(count) {
        const cartBtn = document.querySelector('.floating-cart-btn');
        if (cartBtn) {
            cartBtn.innerHTML = `🛒 購物車 (${count})`;
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        fetch('get_cart_count.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateCartCountDisplay(data.cartCount);
                }
            });
    });
</script>

</body>
</html>
