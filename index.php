<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './lib/db.php';
$conn = getDBConnection();
 
// (2) 取得大類別 (Category) 資料
// 假設您的 categories 表結構至少包含 category_id、name
$categoryQuery = "SELECT category_id,name FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);

$categories = [];
if($categoryResult && mysqli_num_rows($categoryResult) > 0){
  while($row = mysqli_fetch_assoc($categoryResult)){
    $categories[] = $row;
  }
}


// (3) 隨機取得商品 (Products) 資料
// 假設您的 products 表結構包含 product_id、product_name、product_price、image_path 等欄位
$productQuery = "SELECT product_id, product_name, price, image_path FROM product
                 WHERE  is_active = 1 ORDER BY RAND() ";
$productResult = mysqli_query($conn, $productQuery);

$sqlAd = "SELECT * FROM ad WHERE is_active = 1 AND current_timestamp() BETWEEN start_time AND end_time";
$resultAd = $conn->query($sqlAd);

$adData = [];
if ($resultAd->num_rows > 0) {
    while ($row = $resultAd->fetch_assoc()) {
        $adData[] = $row;
    }
}

$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $stmt->bind_result($cartCount);
    $stmt->fetch();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電商首頁</title>
    <!-- 這裡可以保留原本的 CSS、JS 引用，以下僅示意 -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');
        * { box-sizing: border-box; }

        html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }
        
        body { font-family: 'Noto Sans TC', sans-serif; margin: 0; padding-top: 60px; background-color: #F5F5F5; } 

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

        /* Hero Carousel */
        .hero { position: relative; width: 100%; height: 500px; overflow: hidden; text-align: center; background: #333; color: white; }
        .hero img { width: 100%; height: 100%; object-fit: cover; align-items: center;justify-content: center;display: none;}
        .hero img.active {display: block;}
        .hero .prev, .hero .next {  
            position: absolute; top: 50%; transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5); color: white; padding: 10px;
            cursor: pointer; font-size: 24px;
        }
        .hero .prev { left: 20px; }
        .hero .next { right: 20px; }

        .category-menu {
            display: flex;
            justify-content: center;
            background-color: #DDD;
            padding: 15px 0;
            gap: 100px; /* 調整主類別之間的距離 */
            list-style: none;
            margin: 0;
        }
        .category-menu li {
            list-style: none;
            position: relative; /* 讓下拉絕對定位 */
        }
        .category-menu li a {
            font-size: 18px; cursor: pointer; padding: 10px 20px;
            border-radius: 10px; transition: background-color 0.1s ease;
            text-decoration: none; color: #333;
        }
        .category-menu li:hover > a {
            background-color: #1E3A8A; color: white; 
        }

        /* Products */
        .product {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 40px 10px;
            gap: 20px;
        }

        .product-image {
            width: 100%;
            height: 160px; /* 🔥 你可以依照版面調整，例如 160px 高 */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f8f8; /* 可選，讓背景一致 */
            border-radius: 8px;
            overflow: hidden;
        }

        /* 圖片本身控制大小，不能超過容器，並置中顯示 */
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* 讓圖片縮放但不裁切，保留原比例 */
        }
        .product-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 280px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* 商品圖片 */
        .product-card img {
            width: 50%;
            border-radius: 8px;
            margin: 0 auto;
        }

        /* 商品資訊容器 */
        .product-info {
            padding: 10px 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* 商品名稱固定高度＋多行截斷 */
        .product-name h3 {
            font-size: 18px;
            margin: 0 0 10px;
            line-height: 1.4;
            height: 3.6em; /* 約兩行 */
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* 價格樣式 */
        .price {
            font-size: 18px;
            font-weight: bold;
            color: #D72638;
            margin-bottom: 15px;
        }

        /* 按鈕 */
        .product-card button {
            background-color: #1E3A8A;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.2s ease;
        }

        .product-card button:hover {
            background-color: #16327a;
        }

        .product-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .product-card {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            transition: box-shadow 0.2s ease;
        }

        .product-card:hover {
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            cursor: pointer;
        }


        
        .footer {
            background-color: #333; color: white; text-align: center;
            padding: 20px; font-size: 14px;
        }

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
    <div class="navbar">
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
    </div>

    <div class="hero"> 
        <div class="prev" onclick="prevSlide()">〈</div>
        <?php if(!empty($adData)): ?>
            <?php foreach($adData as $index => $ad): ?>
              <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank">
                <img
                    src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                    class="<?php echo ($index === 0) ? 'active' : ''; ?>" 
                    alt="廣告">
              </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- 若沒有廣告資料，可放一張預設圖 -->
            <img src="img/default_ad.jpg" class="active" alt="預設廣告"
                style="width: 500px; height: auto; display: block; margin: 0 auto;">
        <?php endif; ?>
        <div class="next" onclick="nextSlide()">〉</div>
    </div>

    <ul class="category-menu">
        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $cat): ?>
            <li>
              <a href ="browse.php?category_id=<?php echo $cat['category_id']; ?>" >
                 <?php echo htmlspecialchars($cat['name']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
              <span>目前尚無分類</span>
        <?php endif; ?>
    </ul>

    <main>
    <section class="product">
        <?php if ($productResult && mysqli_num_rows($productResult) > 0): ?>
            <?php while($prod = mysqli_fetch_assoc($productResult)): ?>
                <div class="product-card" onclick="goToProduct('<?php echo $prod['product_id']; ?>')">
                    <!-- 商品圖片 -->
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($prod['image_path']); ?>" alt="商品圖片">
                    </div>
                    <!-- 商品資訊區塊 -->
                    <div class="product-info">
                        <!-- 商品名稱 -->
                        <div class="product-name">
                            <h3><?php echo htmlspecialchars($prod['product_name']); ?></h3>
                        </div>

                        <!-- 商品價格 -->
                        <div class="price">價格：<?php echo $prod['price']; ?></div>
                    </div>

                    <!-- 購買按鈕 -->
                    <a href="add_to_cart.php?product_id=<?php echo urlencode($prod['product_id']); ?>">
                            <button>立即購買</button>
                    </a>
                </div>
                
            <?php endwhile; ?>
        <?php else: ?>
            <p>目前沒有商品。</p>
        <?php endif; ?>
    </section>
    </main>


    <?php if (isset($_SESSION['user_id'])): ?>
    <a href="cart.php" class="floating-cart-btn">
        🛒 購物車 (<?php echo $cartCount; ?>)
    </a>
    <?php endif; ?>


    <div class="footer">
        <div class="contact">
            <span>電話: 0900000000</span>　
            <span>Email: dbecommercesite@gmail.com</span>　
            <span>地址: 臺灣臺中市西屯區文華路100號</span>
        </div>
    </div>

<script>
    let currentIndex = 0;

    function updateSlides(direction) {
        const slides = document.querySelectorAll('.hero img');
        if (slides.length === 0) return;

        // 移除所有 active
        slides[currentIndex].classList.remove('active');

        // 更新 index
        if (direction === 'next') {
            currentIndex = (currentIndex + 1) % slides.length;
        } else if (direction === 'prev') {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        }

        // 加上 active
        slides[currentIndex].classList.add('active');
    }

    function nextSlide() {
        updateSlides('next');
    }

    function prevSlide() {
        updateSlides('prev');
    }

    function goToProduct(productId) {
        window.location.href = 'item.php?product_id=' + encodeURIComponent(productId);
    }
</script>

</body>


</html>

<?php
// 關閉連線（若需要）
mysqli_close($conn);
?>
