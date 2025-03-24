<?php
// index.php

// 資料庫連線參數（請依實際環境修改）
$host = '127.0.0.1';
$user = 'root';
$password = '';
$dbName = 'ecommerce';

// 建立資料庫連線
$conn = new mysqli($host, $user, $password, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 讀取廣告區資料（ads）
$adsQuery = "SELECT ad_id, image_path, short_link FROM ads ORDER BY ad_id ASC";
$adsResult = $conn->query($adsQuery);
$ads = [];
if ($adsResult && $adsResult->num_rows > 0) {
    while ($row = $adsResult->fetch_assoc()) {
        $ads[] = $row;
    }
}

// 讀取推薦商品資料（items）
$itemsQuery = "SELECT item_id, item_name, category_id, brand_id, image_path, short_description, detail_description, price, inserting_time FROM items ORDER BY item_id ASC";
$itemsResult = $conn->query($itemsQuery);
$items = [];
if ($itemsResult && $itemsResult->num_rows > 0) {
    while ($row = $itemsResult->fetch_assoc()) {
        $items[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>電商首頁</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');

    body { font-family: 'Noto Sans TC', sans-serif; margi7n: 0; padding: 0; background-color: #F5F5F5; }
    /* 固定頁首 */
    .navbar { display: flex; justify-content: space-between; align-items: center; background-color: #1E3A8A; padding: 15px 20px; color: white; position: fixed; top: 0; left: 0;width: 100%; z-index: 1000;}
    .navbar .logo a { font-size: 24px; font-weight: bold; color: white; text-decoration: none; }
    .nav-links { display: flex; gap: 10px; }
    .nav-links a { background-color: #D4AF37; color: white; text-decoration: none; padding: 10px 15px; border-radius: 20px; }
    /* 搜尋列（補足固定頁首高度） */
    .search-bar { display: flex; justify-content: center; padding: 70px 10px 10px; background-color: white; }
    .search-bar input { width: 80%; padding: 10px; border: 1px solid #CCC; border-radius: 5px; }
    /* 廣告區 (Hero 輪播) */
    .hero { position: relative; width: 100%; height: 500px; overflow: hidden; text-align: center; background: #333; color: white; }
    .hero img { width: 100%; height: 100%; object-fit: cover; display: none; }
    .hero img.active { display: block; }
    .hero .prev, .hero .next { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0, 0, 0, 0.5); color: white; padding: 10px; cursor: pointer; font-size: 24px; }
    .hero .prev { left: 20px; }
    .hero .next { right: 20px; }
    /* 多層下拉式分類選單 */
    .category-menu { background-color: #DDD;  padding: 15px 0; }
    .category-menu ul { list-style: none; margin: 0; padding: 0; }
    .category-menu > ul > li { display: inline-block; position: relative; padding: 10px 20px; }
    .category-menu li a { color: #333; text-decoration: none; padding: 5px 10px; display: block; }
    .category-menu > ul > li:hover > a { background-color: #1E3A8A; color: white;    }
    .submenu { display: none; position: absolute; top: 100%; left: 0; background-color: #f5f5f5; min-width: 150px; z-index: 1000; border: 1px solid #ccc; }
    .category-menu li:hover > .submenu { display: block; }
    .submenu li { position: relative; }
    .submenu li a:hover { background-color: #ddd; }
    .subsubmenu { display: none; position: absolute; top: 0; left: 100%; background-color: #f5f5f5; min-width: 150px; z-index: 1000; border: 1px solid #ccc; }
    .submenu li:hover > .subsubmenu { display: block; }
    /* 商品區塊 */
    .items { display: flex;  flex-wrap: wrap; justify-content: center; padding: 40px 10px; gap: 20px; }
    .item-card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); width: 280px; text-align: center; }
    .item-card img { width: 50%;  border-radius: 8px; }
    .item-info { padding: 10px 0; }
    .item-card h3 { font-size: 18px;  margin: 0; }
    .item-card .price { font-size: 18px;  font-weight: bold;  color: #D72638;     }
    .item-card button { background-color: #1E3A8A; color: white; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px; }
    /* 頁尾 */
    .footer { background-color: #333; color: white; text-align: center; padding: 20px; font-size: 14px; }
  </style>
</head>
<body>
  <!-- 固定頁首 -->
  <div class="navbar">
    <div class="logo"><a href="index.php">LOGO</a></div>
    <div class="nav-links">
      <a href="index.php">會員</a>
      <a href="index.php">問題</a>
      <a href="register.php">註冊</a>
      <a href="login.php">登入</a>
    </div>
  </div>

  <!-- 搜尋列 -->
  <div class="search-bar">
    <input type="text" placeholder="搜尋產品...">
  </div>

  <!-- 廣告區 (Hero 輪播) -->
  <div class="hero">
    <div class="prev" onclick="prevSlide()">〈</div>
    <?php foreach ($ads as $index => $ad): ?>
      <!-- 以短連結包覆廣告圖片 -->
      <a href="<?php echo $ad['short_link']; ?>">
        <img src="<?php echo $ad['image_path']; ?>" class="<?php echo $index == 0 ? 'active' : ''; ?>" alt="廣告<?php echo $ad['id']; ?>">
      </a>
    <?php endforeach; ?>
    <div class="next" onclick="nextSlide()">〉</div>
  </div>

  <!-- 分類選單 (多層下拉) -->
  <nav class="category-menu">
    <ul>
      <li>
        <a href="#">分類1</a>
        <ul class="submenu">
          <li>
            <a href="#">鍵盤</a>
            <ul class="subsubmenu">
              <li><a href="#">品牌A</a></li>
              <li><a href="#">品牌B</a></li>
              <li><a href="#">品牌C</a></li>
            </ul>
          </li>
          <li>
            <a href="#">滑鼠</a>
            <ul class="subsubmenu">
              <li><a href="#">品牌X</a></li>
              <li><a href="#">品牌Y</a></li>
              <li><a href="#">品牌Z</a></li>
            </ul>
          </li>
          <li>
            <a href="#">耳機</a>
            <ul class="subsubmenu">
              <li><a href="#">品牌1</a></li>
              <li><a href="#">品牌2</a></li>
              <li><a href="#">品牌3</a></li>
            </ul>
          </li>
          <li>
            <a href="#">椅子</a>
            <ul class="subsubmenu">
              <li><a href="#">品牌甲</a></li>
              <li><a href="#">品牌乙</a></li>
              <li><a href="#">品牌丙</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <!-- 其他分類同理，這裡僅示範一個分類 -->
    </ul>
  </nav>

  <!-- 推薦商品區 -->
  <div class="items">
    <?php foreach ($items as $item): ?>
      <div class="item-card">
        <img src="<?php echo $item['image_path']; ?>" alt="產品圖片<?php echo $item['id']; ?>">
        <div class="item-info">
          <h3><?php echo $item['item_name']; ?></h3>
          <p class="price">$<?php echo number_format($item['price']); ?></p>
          <!-- 若需要產品敘述可額外處理顯示 -->
        </div>
        <a href="item.php?id=<?php echo $item['id']; ?>"><button>立即購買</button></a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- 頁尾 -->
  <div class="footer">
    <div class="contact">
      <span>電話: 123-456-789</span>
      <span>Email: example@mail.com</span>
      <span>地址: 台北市XX區XX路</span>
    </div>
  </div>

  <script>
    let currentIndex = 0;
    const slides = document.querySelectorAll('.hero img');
    function showSlide(index) {
      slides.forEach((slide, i) => slide.classList.toggle('active', i === index));
    }
    function prevSlide() {
      currentIndex = (currentIndex === 0) ? slides.length - 1 : currentIndex - 1;
      showSlide(currentIndex);
    }
    function nextSlide() {
      currentIndex = (currentIndex === slides.length - 1) ? 0 : currentIndex + 1;
      showSlide(currentIndex);
    }
  </script>
</body>
</html>
