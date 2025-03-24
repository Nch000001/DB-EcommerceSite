<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
global $conn;

$product_id = $_GET['product_id'] ?? null;

if (!$product_id) {
    echo "找不到商品";
    exit;
}

// 查詢目前這筆商品的資料
$productQuery = "SELECT * FROM product WHERE product_id = ?";
$stmt = $conn->prepare($productQuery);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$prod = $result->fetch_assoc();

if (!$prod) {
    echo "商品不存在";
    exit;
}

//取得類別ID 用於顯示類別名稱
$categoryQuery = "SELECT category_id,name FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);

$categories = [];
if($categoryResult && mysqli_num_rows($categoryResult) > 0){
  while($row = mysqli_fetch_assoc($categoryResult)){
    $categories[] = $row;
  }
}

// 撈其他商品作為「猜你也喜歡」
$otherProductQuery = "SELECT * FROM product WHERE product_id != ? AND is_active = 1 ORDER BY RAND() LIMIT 10";
$stmt2 = $conn->prepare($otherProductQuery);
$stmt2->bind_param("s", $product_id);
$stmt2->execute();
$otherResult = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($prod['product_name']); ?> - 商品頁面</title>
  <style>

    body {
      padding: 0;
      margin: 0;
      font-family: 'Noto Sans TC', sans-serif;
      background: #f5f5f5;
    }

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
    .hero-ad {
      display: flex;
      height: 320px;
      background: #fff;
      margin-top: 60px;
      max-width: 1200px;  /* 限制整體最大寬度 */
      margin-inline: auto;
    }

    .hero-left {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      max-width: 600px; /* 🔥 圖片區最大寬度限制 */
    }

    .hero-left img {
      width: 100%;
      height: auto;
      max-height: 300px; /* 🔥 讓圖片在區塊內保持比例縮放 */
      object-fit: contain;
    }

    .hero-right {
      flex: 1;
      padding: 20px;
      font-size: 16px;
      line-height: 1.8;
      border-left: 2px solid #ccc;
      display: flex;
      flex-direction: column;
      justify-content: center;
      max-width: 600px;
    }

    .hero-right .price {
      color: #d72638;
      font-size: 20px;
      font-weight: bold;
      margin: 15px 0;
    }
    
    .button {
      display: inline-block;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      text-decoration: none;
      text-align: center;
      cursor: pointer;
      border: none;
    }

    .button.primary {
      background-color: #1e3a8a;
      color: white;
    }

    .button.gray {
      background-color: #ccc;
      color: black;
    }

    .product-detail-box {
      max-width: 900px;
      margin: 30px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
    }

    .product-detail-box .description {
      max-height: 140px;
      overflow: hidden;
      position: relative;
    }

    .product-detail-box .description.expanded {
      max-height: none;
    }

    .toggle-btn {
      margin-top: 10px;
      color: #1e3a8a;
      cursor: pointer;
    }

    .recommend-carousel {
      margin-top: 50px;
      background: #fff;
      padding: 20px;
      position: relative;
      overflow: hidden;
      max-width: 1200px;
      margin-inline: auto;
      border-radius: 8
    }

    .carousel-track {
      display: flex;
      flex-wrap: nowrap; /* 🔥 防止自動換行 */
      transition: transform 0.3s ease;
      will-change: transform;
    }

    .carousel-card {
      flex: 0 0 250px; /* 🔥 固定每張卡片寬度 */
      margin-right: 20px;
      background: #fafafa;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: center;
      box-sizing: border-box;
    }

    .carousel-card h4 {
      font-size: 16px;
      margin: 10px 0;
      height: 3.2em; /* 兩行字的高度 */
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;   /* 限制最多兩行 */
      -webkit-box-orient: vertical;
    }
    .carousel-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      background: rgba(0,0,0,0.5);
      color: white;
      padding: 5px 10px;
      cursor: pointer;
      z-index: 10;
      user-select: none;
    }

    .carousel-nav.prev { left: 10px; }
    .carousel-nav.next { right: 10px; }

    .footer {
        background-color: #333; color: white; text-align: center;
        padding: 20px; font-size: 14px;
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
            <a href="register.php">註冊</a>
            <a href="login.php">登入</a>
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

  <!-- 商品主圖 + 簡短說明 -->
  <div class="hero-ad">
    <div class="hero-left">
      <img src="<?php echo htmlspecialchars($prod['image_path']); ?>" alt="商品圖片">
    </div>
    <div class="hero-right">
      <h2><?php echo htmlspecialchars($prod['product_name']); ?></h2>
      <?php echo nl2br(htmlspecialchars($prod['short_description'])); ?>
      <div class="price">NT$<?php echo number_format($prod['price']); ?></div>
      <div class="buttons">
        <a href="#" class="button gray">加入購物車</a>
        <a href="#" class="button primary">立即購買</a>
      </div>
    </div>
  </div>


  <!-- 商品詳情 -->
  <div class="product-detail-box">
    <h2><?php echo htmlspecialchars($prod['product_name']); ?></h2>
    <div id="descBox" class="description">
      <?php echo nl2br(htmlspecialchars($prod['detail_description'])); ?>
    </div>
    <div id="toggleBtn" class="toggle-btn" onclick="toggleDescription()">查看更多</div>
  </div>

  <br>
<!-- 猜你也喜歡 -->

  <div class="recommend-carousel">
  
    <div class="carousel-nav prev" onclick="scrollCarousel(-1)">〈</div>
    <div class="carousel-nav next" onclick="scrollCarousel(1)">〉</div>
    <div id="carouselTrack" class="carousel-track">
      <?php while($p = $otherResult->fetch_assoc()): ?>
      <div class="carousel-card">
        <img src="<?php echo htmlspecialchars($p['image_path']); ?>" style="width:100%; height:140px; object-fit:contain;">
        <h4><?php echo htmlspecialchars($p['product_name']); ?></h4>
        <a href="item.php?product_id=<?php echo $p['product_id']; ?>" class="button primary">前往商品</a>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
      
  <script>
    function toggleDescription() {
      const box = document.getElementById('descBox');
      const btn = document.getElementById('toggleBtn');
      box.classList.toggle('expanded');
      btn.textContent = box.classList.contains('expanded') ? '收起' : '查看更多';
    }

    let scrollIndex = 0;
    function scrollCarousel(dir) {
      const track = document.getElementById('carouselTrack');
      const cardWidth = 300;
      scrollIndex += dir;
      scrollIndex = Math.max(0, scrollIndex);
      track.style.transform = `translateX(-${scrollIndex * cardWidth}px)`;
    }
  </script>

<!-- 保留原 footer -->
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
// 關閉連線
mysqli_close($conn);
?>
