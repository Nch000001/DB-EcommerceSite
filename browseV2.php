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

$category_id = $_GET['category_id'] ?? '';
$selected_tags = $_GET['tag_id'] ?? [];

// 撈 tag_type 與對應 tag（篩選器用）
$tag_types = [];
if ($category_id) {
    $tag_type_sql = "SELECT tt.tag_type_id, tt.name AS tag_type_name
                     FROM tag_type tt
                     JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id
                     WHERE tc.category_id = ?";
    $stmt = $conn->prepare($tag_type_sql);
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $tag_type_result = $stmt->get_result();
    while ($row = $tag_type_result->fetch_assoc()) {
        $tag_id_sql = "SELECT tag_id, name FROM tag WHERE tag_type_id = ?";
        $tag_stmt = $conn->prepare($tag_id_sql);
        $tag_stmt->bind_param("s", $row['tag_type_id']);
        $tag_stmt->execute();
        $tag_result = $tag_stmt->get_result();
        $tags = [];
        while ($tag = $tag_result->fetch_assoc()) {
            $tags[] = $tag;
        }
        $row['tags'] = $tags;
        $tag_types[] = $row;
    }
}

// 撈符合條件的商品
$product_sql = "SELECT DISTINCT p.* FROM product p
                LEFT JOIN product_tag pt ON p.product_id = pt.product_id
                WHERE p.category_id = ?";
$params = [$category_id];
$types = "s";

if (!empty($selected_tags)) {
    $in_clause = implode(',', array_fill(0, count($selected_tags), '?'));
    $product_sql .= " AND pt.tag_id IN ($in_clause)";
    $params = array_merge($params, $selected_tags);
    $types .= str_repeat("s", count($selected_tags));
}

$stmt = $conn->prepare($product_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$product_result = $stmt->get_result();
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
    
    .category-bar {
        margin-top: 60px;
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


    .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }

  .filter-box {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    border: 1px solid #ddd;
  }

  .filter-group {
    margin-bottom: 20px;
  }

  .filter-title {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 5px;
  }

  .filter-options {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .filter-label {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 20px;
    background-color: #f4f4f4;
    border: 1px solid #ccc;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .filter-label:hover {
    background-color: #e0e0e0;
    border-color: #999;
  }

  .filter-label input[type="checkbox"] {
    margin-right: 6px;
  }

  .filter-submit {
    background-color: #1e3a8a;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 15px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
  }

  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
  }

  .product-card {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    width: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
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

    <div class="container">
        <form method="get" class="filter-box">
            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">

            <?php foreach ($tag_types as $type): ?>
                <div class="filter-group">
                    <div class="filter-title"><?php echo htmlspecialchars($type['tag_type_name']); ?></div>
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
            <button type="submit">套用篩選</button>
        </form>

        <div class="product-grid">
            <?php if ($product_result && $product_result->num_rows > 0): ?>
                <?php while ($product = $product_result->fetch_assoc()): ?>

                    <div class="product-card">

                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="商品圖片">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>

                        <div class="price">$<?php echo number_format($product['price']); ?></div>
                        <a href="item.php?product_id=<?php echo $product['product_id']; ?>">立即購買</a>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>目前沒有符合的商品。</p>
            <?php endif; ?>
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
