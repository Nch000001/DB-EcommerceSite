<?php
// 1. 設定資料庫連線參數
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ecommerce";

// 2. 建立資料庫連線
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 3. 檢查連線是否成功
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 4. 取得廣告資料 (ads) => 輪播用
$sqlAds = "SELECT * FROM ads";
$resultAds = $conn->query($sqlAds);
$adsData = [];
if ($resultAds->num_rows > 0) {
    while ($row = $resultAds->fetch_assoc()) {
        $adsData[] = $row;
    }
}

// 5. 取得所有分類 (categories)
$sqlCat = "SELECT * FROM categories ORDER BY parent_id, category_id";
$resultCat = $conn->query($sqlCat);
$categories = [];
if ($resultCat->num_rows > 0) {
    while ($row = $resultCat->fetch_assoc()) {
        $categories[] = $row;
    }
}

// 6. 取得所有品牌，並用 category_brand 做對應
$sqlBrand = "
    SELECT cb.category_id, b.brand_id, b.brand_name
    FROM category_brand cb
    JOIN brands b ON cb.brand_id = b.brand_id
";
$resultBrand = $conn->query($sqlBrand);
$brandMap = [];  // $brandMap[category_id] = array of (brand_id, brand_name)
if ($resultBrand->num_rows > 0) {
    while ($row = $resultBrand->fetch_assoc()) {
        $catId = $row['category_id'];
        if (!isset($brandMap[$catId])) {
            $brandMap[$catId] = [];
        }
        $brandMap[$catId][] = [
            'brand_id' => $row['brand_id'],
            'brand_name' => $row['brand_name']
        ];
    }
}

// 7. 整理成巢狀結構: parent_id -> child
$catTree = [];
foreach ($categories as $cat) {
    $catTree[$cat['parent_id']][] = $cat;
}

// 8. 取得商品 (items) 資料
$sqlItems = "SELECT * FROM items ORDER BY inserting_time DESC";
$resultItems = $conn->query($sqlItems);
$itemsData = [];
if ($resultItems->num_rows > 0) {
    while ($row = $resultItems->fetch_assoc()) {
        $itemsData[] = $row;
    }
}

// 關閉資料庫連線
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電商首頁</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');
        
        body { font-family: 'Noto Sans TC', sans-serif; margin: 0; padding-top: 60px; background-color: #F5F5F5; } 

        .navbar {  position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #1e3a8a; /* 深藍色 */
            display: flex; /* 使用 flexbox */
            align-items: center; /* 垂直置中 */
            justify-content: center;
            padding: 0px; /* 添加內距 */
            z-index: 1000;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            margin-right: 0px; 
        }

        .navbar .logo a { font-size: 24px; font-weight: bold; color: white; text-decoration: none; }
        .nav-links { display: flex; gap: 10px; }
        .nav-links a { background-color: #D4AF37; color: white; text-decoration: none; padding: 10px 15px; border-radius: 20px; }

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

        /* Category Menu (主類別 + 下拉) */
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

        /* 下拉的大容器 (包含子類別與品牌) */
        .category-menu li .dropdown {
            display: none; /* 預設隱藏 */
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #FFF;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            padding: 20px;
            min-width: 400px; /* 可自行調整寬度 */
            /* 用 flex 讓子類別、品牌左右並排 */
            gap: 40px;
        }
        /* 滑到 li 時，顯示下拉 */
        .category-menu li:hover .dropdown {
            display: flex;
            
        }
        .subcats, .brands {
            flex: 1;
        }
        .subcats h4, .brands h4 {
            margin-top: 0;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subcats ul, .brands ul {
            list-style: none;
            padding: px;
            margin: 0;
        }
        .subcats li, .brands li {
            margin-bottom: 15px;
        }
        .subcats li a, .brands li a {
            display: inline;
            color: #333;
            text-decoration: none;
            padding: 0px 0px;
        }
        .subcats li a:hover, .brands li a:hover { 
            color:rgb(5, 178, 247);
            background-color: transparent;
            text-decoration: none;
            border-radius: 0;
        }

        /* Products */
        .products { display: flex; flex-wrap: wrap; justify-content: center; padding: 40px 10px; gap: 20px; }
        .product-card {
            background-color: white; padding: 20px; border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); width: 280px; text-align: center;
        }
        .product-card img { width: 50%; border-radius: 8px; }
        .product-info { padding: 10px 0; }
        .product-card h3 { font-size: 18px; margin: 0; }
        .product-card .price { font-size: 18px; font-weight: bold; color: #D72638; }
        .product-card button {
            background-color: #1E3A8A; color: white; border: none;
            padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px;
        }

        .footer {
            background-color: #333; color: white; text-align: center;
            padding: 20px; font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- 導覽列 -->
    <div class="navbar">
        <div class="logo"><a href="index.php">LOGO</a></div>  

        <div class="search-bar">
        <input type="text" placeholder="搜尋產品...">

        </div>
        <div class="nav-links">
            <a href="#">會員</a>
            <a href="#">問題</a>
            <a href="register.html">註冊</a>
            <a href="login.html">登入</a>
        </div>
    </div>
    
    <!-- 廣告輪播區 (Hero) -->
    <div class="hero">
        <div class="prev" onclick="prevSlide()">〈</div>
        <?php if(!empty($adsData)): ?>
            <?php foreach($adsData as $index => $ad): ?>
                <img 
                    src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                    class="<?php echo ($index === 0) ? 'active' : ''; ?>" 
                    alt="廣告"
                >
            <?php endforeach; ?>
        <?php else: ?>
            <!-- 若沒有廣告資料，可放一張預設圖 -->
            <img src="img/default_ad.jpg" class="active" alt="預設廣告">
        <?php endif; ?>
        <div class="next" onclick="nextSlide()">〉</div>
    </div>

    <!-- 分類選單 (含子分類與品牌) -->
    <ul class="category-menu">
        <?php
        // 只顯示 parent_id 為 NULL (或 0) 的最上層分類
        if (isset($catTree[null])) {
            foreach ($catTree[null] as $topCat) {
                $topCatId = $topCat['category_id'];
                echo '<li>';
                echo '<a href="#">'.htmlspecialchars($topCat['category_name']).'</a>';

                // 建立一個下拉區塊，裡面分兩欄：子類別、品牌
                echo '<div class="dropdown">';

                // 左邊 - 子類別
                echo '<div class="subcats">';
                echo '<h4>相關分類</h4>';
                echo '<ul>';
                // 列出該主類別的子分類
                if (isset($catTree[$topCatId])) {
                    foreach ($catTree[$topCatId] as $childCat) {
                        echo '<li><a href="#">' . htmlspecialchars($childCat['category_name']) . '</a></li>';
                    }
                }
                echo '</ul>';
                echo '</div>'; // end subcats

                // 右邊 - 品牌
                echo '<div class="brands">';
                echo '<h4>品牌</h4>';
                echo '<ul>';
                // 列出該主類別對應的品牌 (brandMap)
                if (isset($brandMap[$topCatId])) {
                    foreach ($brandMap[$topCatId] as $b) {
                        echo '<li><a href="#">' . htmlspecialchars($b['brand_name']) . '</a></li>';
                    }
                }
                echo '</ul>';
                echo '</div>'; // end brands

                echo '</div>'; // end dropdown

                echo '</li>';
            }
        }
        ?>
    </ul>


    <!-- 商品展示 -->
    <div class="products">
        <?php foreach($itemsData as $item): ?>
            <div class="product-card">
                <img 
                    src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                    alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                >
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                    <p class="price">$<?php echo number_format($item['price']); ?></p>
                </div>
                <!-- 假設要連到單一商品頁，可以帶 item_id -->
                <a href="item.php?item_id=<?php echo urlencode($item['item_id']); ?>">
                    <button>立即購買</button>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

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

