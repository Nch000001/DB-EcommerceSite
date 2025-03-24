<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
global $conn;

$categoryQuery = "SELECT category_id,name FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);

$categories = [];
if($categoryResult && mysqli_num_rows($categoryResult) > 0){
  while($row = mysqli_fetch_assoc($categoryResult)){
    $categories[] = $row;
  }
}

// ✅ 接收分類 ID
$category_id = $_GET['category_id'] ?? null;
if (!$category_id) {
    echo "未指定分類"; exit;
}

// ✅ 接收使用者勾選的 tag_id[]
$selected_tags = $_GET['tag_id'] ?? [];

// ✅ 從 tag_category 找出這個分類對應的 tag_type
$tag_types_stmt = $conn->prepare(
    "SELECT tt.tag_type_id, tt.name 
     FROM tag_type tt
     JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id
     WHERE tc.category_id = ?"
);
$tag_types_stmt->bind_param("s", $category_id);
$tag_types_stmt->execute();
$tag_types_result = $tag_types_stmt->get_result();

$tag_types = [];
while ($row = $tag_types_result->fetch_assoc()) {
    // 取得 tag_type 底下的 tag 選項
    $tags_stmt = $conn->prepare("SELECT tag_id, name FROM tag WHERE tag_type_id = ?");
    $tags_stmt->bind_param("s", $row['tag_type_id']);
    $tags_stmt->execute();
    $tags_result = $tags_stmt->get_result();
    $tags = [];
    while ($tag = $tags_result->fetch_assoc()) {
        $tags[] = $tag;
    }

    $tag_types[] = [
        'tag_type_id' => $row['tag_type_id'],
        'tag_type_name' => $row['name'],
        'tags' => $tags
    ];
}

// ✅ 查詢符合所有 tag 的商品 ID
$product_ids = [];

if (!empty($selected_tags)) {
    $placeholders = implode(',', array_fill(0, count($selected_tags), '?'));
    $types = str_repeat('s', count($selected_tags));

    $stmt = $conn->prepare("
        SELECT product_id
        FROM product_tag
        WHERE tag_id IN ($placeholders)
        GROUP BY product_id
        HAVING COUNT(DISTINCT tag_id) = ?
    ");

    $params = [...$selected_tags, count($selected_tags)];
    $stmt->bind_param($types . 'i', ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $product_ids[] = $row['product_id'];
    }

    // 撈出這些商品的資料
    $products = [];
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $types = str_repeat('s', count($product_ids));
        $stmt = $conn->prepare("SELECT * FROM product WHERE product_id IN ($placeholders)");
        $stmt->bind_param($types, ...$product_ids);
        $stmt->execute();
        $products = $stmt->get_result();
    } else {
        $products = [];
    }
} else {
    // 若無 tag 篩選 → 撈該分類全部商品
    $stmt = $conn->prepare("SELECT * FROM product WHERE category_id = ?");
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $products = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>商品篩選瀏覽頁</title>
  <style>
    * { box-sizing: border-box; }

    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1; /* 🔥 主內容自動撐開，其餘交給 header/footer */
    }

    body { font-family: 'Noto Sans TC', sans-serif; margin: 0; background-color: #f5f5f5; }
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
    
    .category-menu {
        margin-top: 60px;
        display: flex;
        justify-content: center;
        background-color: #DDD;
        padding: 15px 0;
        gap: 100px; /* 調整主類別之間的距離 */
        list-style: none;
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


    .container { max-width: 1300px; margin: auto; padding: 20px; padding-top: 60px;}

    

    /* 篩選區 */
    .filter-box {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .filter-group {
        margin-bottom: 15px;
    }
    .filter-title {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .filter-options label {
        background: #f0f0f0;
        padding: 5px 10px;
        border-radius: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .filter-options input {
        margin-right: 5px;
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

    <div class="container">

        <form method="get" class="filter-box">
            <input type="hidden" name="category_id" value="KEYBO">

        <!-- 範例 filter group：請用 PHP 動態生成對應 tag_type 和 tag -->
        <div class="filter-group">
        <div class="filter-title">連接方式</div>
        <div class="filter-options">
            <label><input type="checkbox" name="tag_id[]" value="US001"> 有線</label>
            <label><input type="checkbox" name="tag_id[]" value="US002"> 2.4G無線 (9)</label>
            <label><input type="checkbox" name="tag_id[]" value="US003"> 藍牙 (6)</label>
            <label><input type="checkbox" name="tag_id[]" value="US004"> 未分類 (1)</label>
        </div>
        </div>

        <div class="filter-group">
            <div class="filter-title">顏色</div>
            <div class="filter-options">
                <label><input type="checkbox" name="tag_id[]" value="KE001"> 青 (2)</label>
                <label><input type="checkbox" name="tag_id[]" value="KE002"> 茶 (1)</label>
                <label><input type="checkbox" name="tag_id[]" value="KE003"> 紅 (15)</label>
                <label><input type="checkbox" name="tag_id[]" value="KE004"> 銀 (3)</label>
            </div>
        </div>

        <button type="submit">套用篩選</button>
        </form>

        <div class="product-grid">
            <!-- 商品區塊：請用 PHP while 印出每筆資料 -->
            <div class="product-card">
            <img src="images/demo.jpg" alt="商品圖片">
            <h3>黑色機械式鍵盤 RZ03-XXX</h3>
            <div class="price">$2,990</div>
            <a href="#">立即購買</a>
            </div>
            <div class="product-card">
            <img src="images/demo2.jpg" alt="商品圖片">
            <h3>銀軸 RGB 無線鍵盤 高效輸入設計</h3>
            <div class="price">$3,690</div>
            <a href="#">立即購買</a>
            </div>
        </div>

    </div>

    <div class="footer">
        <div class="contact">
            <span>電話: 123-456-789</span>　
            <span>Email: example@mail.com</span>　
            <span>地址: 台北市XX區XX路</span>
        </div>
    </div>

</body>
</html>
