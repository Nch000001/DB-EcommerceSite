<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
global $conn;

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

$sqlAd = "SELECT * FROM ad";
$resultAd = $conn->query($sqlAd);

$adData = [];
if ($resultAd->num_rows > 0) {
    while ($row = $resultAd->fetch_assoc()) {
        $adData[] = $row;
    }
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
        .hero img { width: 100%; height: 100%; object-fit: cover; align-items: center;justify-content: center;}
        .hero img.active { display: block; }
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


        
        .footer {
            background-color: #333; color: white; text-align: center;
            padding: 20px; font-size: 14px;
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
            <a href="register.php">註冊</a>
            <a href="login.php">登入</a>
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
            <img src="img/default_ad.jpg" class="active" alt="預設廣告">
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
                <div class="product-card">
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
                    <a href="item.php?product_id=<?php echo $prod['product_id']; ?>">
                        <button>立即購買</button>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>目前沒有商品。</p>
        <?php endif; ?>
    </section>
    </main>


    <div class="footer">
        <div class="contact">
            <span>電話: 123-456-789</span>　
            <span>Email: example@mail.com</span>　
            <span>地址: 台北市XX區XX路</span>
        </div>
    </div>

</body>
</html>

<?php
// 關閉連線（若需要）
mysqli_close($conn);
?>
