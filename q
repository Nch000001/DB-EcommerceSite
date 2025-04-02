[1mdiff --git a/browse.php b/browse.php[m
[1mindex ec6dffe..2e808f9 100644[m
[1m--- a/browse.php[m
[1m+++ b/browse.php[m
[36m@@ -7,7 +7,7 @@[m [mini_set('display_errors', 1);[m
 include 'db.php';[m
 global $conn;[m
 [m
[31m-$categoryQuery = "SELECT category_id, name FROM category";[m
[32m+[m[32m$categoryQuery = "SELECT category_id,name FROM category";[m
 $categoryResult = mysqli_query($conn, $categoryQuery);[m
 [m
 $categories = [];[m
[36m@@ -21,31 +21,30 @@[m [m$category_id = $_GET['category_id'] ?? '';[m
 $selected_tags = $_GET['tag_id'] ?? [];[m
 [m
 // 撈 tag_type 與對應 tag（篩選器用）[m
[31m-    $tag_types = [];[m
[31m-[m
[31m-    if ($category_id) { [m
[31m-        $tag_type_sql = "SELECT tt.tag_type_id, tt.name AS tag_type_name[m
[31m-                        FROM tag_type tt[m
[31m-                        JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id[m
[31m-                        WHERE tc.category_id = ?";[m
[31m-        $stmt = $conn->prepare($tag_type_sql);[m
[31m-        $stmt->bind_param("s", $category_id);[m
[31m-        $stmt->execute();[m
[31m-        $tag_type_result = $stmt->get_result();[m
[31m-        while ($row = $tag_type_result->fetch_assoc()) {[m
[31m-            $tag_id_sql = "SELECT tag_id, name FROM tag WHERE tag_type_id = ?";[m
[31m-            $tag_stmt = $conn->prepare($tag_id_sql);[m
[31m-            $tag_stmt->bind_param("s", $row['tag_type_id']);[m
[31m-            $tag_stmt->execute();[m
[31m-            $tag_result = $tag_stmt->get_result();[m
[31m-            $tags = [];[m
[31m-            while ($tag = $tag_result->fetch_assoc()) {[m
[31m-                $tags[] = $tag;[m
[31m-            }[m
[31m-            $row['tags'] = $tags;[m
[31m-            $tag_types[] = $row;[m
[32m+[m[32m$tag_types = [];[m
[32m+[m[32mif ($category_id) {[m
[32m+[m[32m    $tag_type_sql = "SELECT tt.tag_type_id, tt.name AS tag_type_name[m
[32m+[m[32m                     FROM tag_type tt[m
[32m+[m[32m                     JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id[m
[32m+[m[32m                     WHERE tc.category_id = ?";[m
[32m+[m[32m    $stmt = $conn->prepare($tag_type_sql);[m
[32m+[m[32m    $stmt->bind_param("s", $category_id);[m
[32m+[m[32m    $stmt->execute();[m
[32m+[m[32m    $tag_type_result = $stmt->get_result();[m
[32m+[m[32m    while ($row = $tag_type_result->fetch_assoc()) {[m
[32m+[m[32m        $tag_id_sql = "SELECT tag_id, name FROM tag WHERE tag_type_id = ?";[m
[32m+[m[32m        $tag_stmt = $conn->prepare($tag_id_sql);[m
[32m+[m[32m        $tag_stmt->bind_param("s", $row['tag_type_id']);[m
[32m+[m[32m        $tag_stmt->execute();[m
[32m+[m[32m        $tag_result = $tag_stmt->get_result();[m
[32m+[m[32m        $tags = [];[m
[32m+[m[32m        while ($tag = $tag_result->fetch_assoc()) {[m
[32m+[m[32m            $tags[] = $tag;[m
         }[m
[32m+[m[32m        $row['tags'] = $tags;[m
[32m+[m[32m        $tag_types[] = $row;[m
     }[m
[32m+[m[32m}[m
 [m
     if (!empty($selected_tags)) {[m
         $product_sql = "[m
[36m@@ -94,43 +93,33 @@[m [m$selected_tags = $_GET['tag_id'] ?? [];[m
     }[m
 [m
     body { font-family: 'Noto Sans TC', sans-serif; margin: 0; background-color: #f5f5f5; }[m
[31m-[m
[31m-    .navbar {[m
[31m-      position: fixed;[m
[31m-      top: 0;[m
[31m-      left: 0;[m
[31m-      width: 100%;[m
[31m-      z-index: 999;[m
[31m-      [m
[31m-      display: flex;[m
[31m-      justify-content: space-between;[m
[31m-      align-items: center;[m
[31m-      background-color: #1E3A8A;[m
[31m-      padding: 15px 20px;[m
[31m-      color: white;[m
[31m-    }[m
[31m-    .navbar .logo a {[m
[31m-      font-size: 24px;[m
[31m-      font-weight: bold;[m
[31m-      color: white;[m
[31m-      text-decoration: none;[m
[31m-    }[m
[31m-    .nav-links {[m
[31m-      display: flex;[m
[31m-      gap: 10px;[m
[32m+[m[32m    .navbar {  position: fixed;[m
[32m+[m[32m        top: 0;[m
[32m+[m[32m        left: 0;[m
[32m+[m[32m        width: 100%;[m
[32m+[m[32m        height: 60px;[m
[32m+[m[32m        background-color: #1e3a8a; /* 深藍色 */[m
[32m+[m[32m        display: flex; /* 使用 flexbox */[m
[32m+[m[32m        align-items: center; /* 垂直置中 */[m
[32m+[m[32m        justify-content: center;[m
[32m+[m[32m        padding: 0px; /* 添加內距 */[m
[32m+[m[32m        z-index: 1000;[m
     }[m
[31m-    .nav-links a {[m
[31m-      background-color: #D4AF37;[m
[31m-      color: white;[m
[31m-      text-decoration: none;[m
[31m-      padding: 10px 15px;[m
[31m-      border-radius: 20px;[m
[32m+[m[32m    .navbar .logo {[m
[32m+[m[32m        font-size: 24px;[m
[32m+[m[32m        font-weight: bold;[m
[32m+[m[32m        color: white;[m
[32m+[m[32m        text-decoration: none;[m
[32m+[m[32m        margin-right: 0px;[m[41m [m
     }[m
[32m+[m[32m    .navbar .logo a { font-size: 24px; font-weight: bold; color: white; text-decoration: none; }[m
[32m+[m[32m    .nav-links { display: flex; gap: 10px; }[m
[32m+[m[32m    .nav-links a { background-color: #D4AF37; color: white; text-decoration: none; padding: 10px 15px; border-radius: 20px; }[m
     .search-bar { flex-grow: 1; display: flex; justify-content: center; }[m
     .search-bar input { width: 100%; padding: 8px 12PX; border: 1px solid #CCC; border-radius: 5px; max-width: 600px; font-size: 16px;}[m
     [m
     .category-bar {[m
[31m-        margin-top: 70px;[m
[32m+[m[32m        margin-top: 60px;[m
         display: flex;[m
         justify-content: center;[m
         background-color: #DDD;[m
[1mdiff --git a/index.php b/index.php[m
[1mindex e316e9c..36ff59f 100644[m
[1m--- a/index.php[m
[1m+++ b/index.php[m
[36m@@ -22,8 +22,11 @@[m [mif($categoryResult && mysqli_num_rows($categoryResult) > 0){[m
 [m
 // (3) 隨機取得商品 (Products) 資料[m
 // 假設您的 products 表結構包含 product_id、product_name、product_price、image_path 等欄位[m
[31m-$productQuery = "SELECT product_id, product_name, price, image_path FROM product[m
[31m-                 WHERE  is_active = 1 ORDER BY RAND() ";[m
[32m+[m[32m// 這裡示範取 6 筆隨機商品[m
[32m+[m[32m$productQuery = "SELECT product_id, product_name, price, image_path[m
[32m+[m[32m                 FROM product[m
[32m+[m[32m                 ORDER BY RAND()[m
[32m+[m[32m                 LIMIT 6";[m
 $productResult = mysqli_query($conn, $productQuery);[m
 [m
 $sqlAd = "SELECT * FROM ad";[m
[36m@@ -51,37 +54,29 @@[m [mif ($resultAd->num_rows > 0) {[m
         [m
         body { font-family: 'Noto Sans TC', sans-serif; margin: 0; padding-top: 60px; background-color: #F5F5F5; } [m
 [m
[31m-        .navbar {[m
[31m-            position: fixed;[m
[32m+[m[32m        .navbar {  position: fixed;[m
             top: 0;[m
             left: 0;[m
             width: 100%;[m
[31m-            z-index: 999;[m
[31m-            [m
[31m-            display: flex;[m
[31m-            justify-content: space-between;[m
[31m-            align-items: center;[m
[31m-            background-color: #1E3A8A;[m
[31m-            padding: 15px 20px;[m
[31m-            color: white;[m
[32m+[m[32m            height: 60px;[m
[32m+[m[32m            background-color: #1e3a8a; /* 深藍色 */[m
[32m+[m[32m            display: flex; /* 使用 flexbox */[m
[32m+[m[32m            align-items: center; /* 垂直置中 */[m
[32m+[m[32m            justify-content: center;[m
[32m+[m[32m            padding: 0px; /* 添加內距 */[m
[32m+[m[32m            z-index: 1000;[m
         }[m
[31m-        .navbar .logo a {[m
[32m+[m[32m        .navbar .logo {[m
             font-size: 24px;[m
             font-weight: bold;[m
             color: white;[m
             text-decoration: none;[m
[32m+[m[32m            margin-right: 0px;[m[41m [m
         }[m
[31m-        .nav-links {[m
[31m-            display: flex;[m
[31m-            gap: 10px;[m
[31m-        }[m
[31m-        .nav-links a {[m
[31m-            background-color: #D4AF37;[m
[31m-            color: white;[m
[31m-            text-decoration: none;[m
[31m-            padding: 10px 15px;[m
[31m-            border-radius: 20px;[m
[31m-        }[m
[32m+[m
[32m+[m[32m        .navbar .logo a { font-size: 24px; font-weight: bold; color: white; text-decoration: none; }[m
[32m+[m[32m        .nav-links { display: flex; gap: 10px; }[m
[32m+[m[32m        .nav-links a { background-color: #D4AF37; color: white; text-decoration: none; padding: 10px 15px; border-radius: 20px; }[m
         .search-bar { flex-grow: 1; display: flex; justify-content: center; }[m
         .search-bar input { width: 100%; padding: 8px 12PX; border: 1px solid #CCC; border-radius: 5px; max-width: 600px; font-size: 16px;}[m
 [m
[36m@@ -241,7 +236,8 @@[m [mif ($resultAd->num_rows > 0) {[m
                 <img[m
                     src="<?php echo htmlspecialchars($ad['image_path']); ?>" [m
                     class="<?php echo ($index === 0) ? 'active' : ''; ?>" [m
[31m-                    alt="廣告">[m
[32m+[m[32m                    alt="廣告"[m
[32m+[m[32m                >[m
               </a>[m
             <?php endforeach; ?>[m
         <?php else: ?>[m
[1mdiff --git a/item.php b/item.php[m
[1mindex 6da0844..c1c3c6a 100644[m
[1m--- a/item.php[m
[1m+++ b/item.php[m
[36m@@ -6,379 +6,61 @@[m [mini_set('display_errors', 1);[m
 include 'db.php';[m
 global $conn;[m
 [m
[31m-$product_id = $_GET['product_id'] ?? null;[m
[32m+[m[32m// 2. 取得網址上的 category_id[m
[32m+[m[32m$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;[m
 [m
[31m-if (!$product_id) {[m
[31m-    echo "找不到商品";[m
[32m+[m[32m// 3. 若沒帶 category_id，跳回首頁或顯示錯誤[m
[32m+[m[32mif (!$category_id) {[m
[32m+[m[32m    echo "未指定分類。";[m
     exit;[m
 }[m
 [m
[31m-// 查詢目前這筆商品的資料[m
[31m-$productQuery = "SELECT * FROM product WHERE product_id = ?";[m
[31m-$stmt = $conn->prepare($productQuery);[m
[31m-$stmt->bind_param("s", $product_id);[m
[31m-$stmt->execute();[m
[31m-$result = $stmt->get_result();[m
[31m-$prod = $result->fetch_assoc();[m
[31m-[m
[31m-if (!$prod) {[m
[31m-    echo "商品不存在";[m
[31m-    exit;[m
[31m-}[m
[31m-[m
[31m-//取得類別ID 用於顯示類別名稱[m
[31m-$categoryQuery = "SELECT category_id,name FROM category";[m
[31m-$categoryResult = mysqli_query($conn, $categoryQuery);[m
[31m-[m
[31m-$categories = [];[m
[31m-if($categoryResult && mysqli_num_rows($categoryResult) > 0){[m
[31m-  while($row = mysqli_fetch_assoc($categoryResult)){[m
[31m-    $categories[] = $row;[m
[31m-  }[m
[32m+[m[32m// 4. 查詢該分類的商品[m
[32m+[m[32m$sql = "SELECT * FROM products WHERE category_id = ?";[m
[32m+[m[32m$stmt = mysqli_prepare($conn, $sql);[m
[32m+[m[32mmysqli_stmt_bind_param($stmt, "s", $category_id);[m
[32m+[m[32mmysqli_stmt_execute($stmt);[m
[32m+[m[32m$result = mysqli_stmt_get_result($stmt);[m
[32m+[m
[32m+[m[32m// 5. (可選) 查詢該分類名稱（顯示在標題）[m
[32m+[m[32m$category_name = "";[m
[32m+[m[32m$cat_sql = "SELECT category_name FROM categories WHERE category_id = ?";[m
[32m+[m[32m$cat_stmt = mysqli_prepare($conn, $cat_sql);[m
[32m+[m[32mmysqli_stmt_bind_param($cat_stmt, "s", $category_id);[m
[32m+[m[32mmysqli_stmt_execute($cat_stmt);[m
[32m+[m[32m$cat_result = mysqli_stmt_get_result($cat_stmt);[m
[32m+[m[32mif ($row = mysqli_fetch_assoc($cat_result)) {[m
[32m+[m[32m    $category_name = $row['category_name'];[m
 }[m
[31m-[m
[31m-// 撈其他商品作為「猜你也喜歡」[m
[31m-$otherProductQuery = "SELECT * FROM product WHERE product_id != ? AND is_active = 1 ORDER BY RAND() LIMIT 10";[m
[31m-$stmt2 = $conn->prepare($otherProductQuery);[m
[31m-$stmt2->bind_param("s", $product_id);[m
[31m-$stmt2->execute();[m
[31m-$otherResult = $stmt2->get_result();[m
 ?>[m
[31m-[m
 <!DOCTYPE html>[m
[31m-<html lang="en">[m
[32m+[m[32m<html lang="zh-Hant">[m
 <head>[m
[31m-  <meta charset="UTF-8">[m
[31m-  <title><?php echo htmlspecialchars($prod['product_name']); ?> - 商品頁面</title>[m
[31m-  <style>[m
[31m-[m
[31m-    body {[m
[31m-      padding: 0;[m
[31m-      margin: 0;[m
[31m-      font-family: 'Noto Sans TC', sans-serif;[m
[31m-      background: #f5f5f5;[m
[31m-    }[m
[31m-[m
[31m-    .navbar {[m
[31m-      position: fixed;[m
[31m-      top: 0;[m
[31m-      left: 0;[m
[31m-      width: 100%;[m
[31m-      z-index: 999;[m
[31m-      [m
[31m-      display: flex;[m
[31m-      justify-content: space-between;[m
[31m-      align-items: center;[m
[31m-      background-color: #1E3A8A;[m
[31m-      padding: 15px 20px;[m
[31m-      color: white;[m
[31m-    }[m
[31m-    .navbar .logo a {[m
[31m-      font-size: 24px;[m
[31m-      font-weight: bold;[m
[31m-      color: white;[m
[31m-      text-decoration: none;[m
[31m-    }[m
[31m-    .nav-links {[m
[31m-      display: flex;[m
[31m-      gap: 10px;[m
[31m-    }[m
[31m-    .nav-links a {[m
[31m-      background-color: #D4AF37;[m
[31m-      color: white;[m
[31m-      text-decoration: none;[m
[31m-      padding: 10px 15px;[m
[31m-      border-radius: 20px;[m
[31m-    }[m
[31m-    .search-bar { flex-grow: 1; display: flex; justify-content: center; }[m
[31m-    .search-bar input { width: 100%; padding: 8px 12PX; border: 1px solid #CCC; border-radius: 5px; max-width: 600px; font-size: 16px;}[m
[31m-    [m
[31m-    .category-bar {[m
[31m-        margin-top: 70px;[m
[31m-        display: flex;[m
[31m-        justify-content: center;[m
[31m-        background-color: #DDD;[m
[31m-        padding: 5px 0;[m
[31m-        gap: 100px; /* 分類間距 */[m
[31m-        flex-wrap: wrap;[m
[31m-        background-color:rgb(182, 189, 189);[m
[31m-    }[m
[31m-[m
[31m-    .category-item {[m
[31m-        display: flex;[m
[31m-    }[m
[31m-[m
[31m-    .category-item a {[m
[31m-        font-size: 18px;[m
[31m-        cursor: pointer;[m
[31m-        padding: 10px 20px;[m
[31m-        border-radius: 10px;[m
[31m-        transition: background-color 0.1s ease;[m
[31m-        text-decoration: none;[m
[31m-        color: #333;[m
[31m-    }[m
[31m-[m
[31m-    .category-item a:hover {[m
[31m-        background-color: #1E3A8A;[m
[31m-        color: white;[m
[31m-    }[m
[31m-    .container { max-width: 1300px; margin: auto; padding: 20px; padding-top: 60px;}[m
[31m-    .hero-ad {[m
[31m-      display: flex;[m
[31m-      height: 320px;[m
[31m-      background: #fff;[m
[31m-      margin-top: 60px;[m
[31m-      max-width: 1200px;  /* 限制整體最大寬度 */[m
[31m-      margin-inline: auto;[m
[31m-    }[m
[31m-[m
[31m-    .hero-left {[m
[31m-      flex: 1;[m
[31m-      display: flex;[m
[31m-      align-items: center;[m
[31m-      justify-content: center;[m
[31m-      padding: 20px;[m
[31m-      max-width: 600px; /* 🔥 圖片區最大寬度限制 */[m
[31m-    }[m
[31m-[m
[31m-    .hero-left img {[m
[31m-      width: 100%;[m
[31m-      height: auto;[m
[31m-      max-height: 300px; /* 🔥 讓圖片在區塊內保持比例縮放 */[m
[31m-      object-fit: contain;[m
[31m-    }[m
[31m-[m
[31m-    .hero-right {[m
[31m-      flex: 1;[m
[31m-      padding: 20px;[m
[31m-      font-size: 16px;[m
[31m-      line-height: 1.8;[m
[31m-      border-left: 2px solid #ccc;[m
[31m-      display: flex;[m
[31m-      flex-direction: column;[m
[31m-      justify-content: center;[m
[31m-      max-width: 600px;[m
[31m-    }[m
[31m-[m
[31m-    .hero-right .price {[m
[31m-      color: #d72638;[m
[31m-      font-size: 20px;[m
[31m-      font-weight: bold;[m
[31m-      margin: 15px 0;[m
[31m-    }[m
[31m-    [m
[31m-    .button {[m
[31m-      display: inline-block;[m
[31m-      padding: 10px 20px;[m
[31m-      font-size: 16px;[m
[31m-      border-radius: 5px;[m
[31m-      text-decoration: none;[m
[31m-      text-align: center;[m
[31m-      cursor: pointer;[m
[31m-      border: none;[m
[31m-    }[m
[31m-[m
[31m-    .button.primary {[m
[31m-      background-color: #1e3a8a;[m
[31m-      color: white;[m
[31m-    }[m
[31m-[m
[31m-    .button.gray {[m
[31m-      background-color: #ccc;[m
[31m-      color: black;[m
[31m-    }[m
[31m-[m
[31m-    .product-detail-box {[m
[31m-      max-width: 900px;[m
[31m-      margin: 30px auto;[m
[31m-      background: white;[m
[31m-      padding: 20px;[m
[31m-      border-radius: 10px;[m
[31m-    }[m
[31m-[m
[31m-    .product-detail-box .description {[m
[31m-      max-height: 140px;[m
[31m-      overflow: hidden;[m
[31m-      position: relative;[m
[31m-    }[m
[31m-[m
[31m-    .product-detail-box .description.expanded {[m
[31m-      max-height: none;[m
[31m-    }[m
[31m-[m
[31m-    .toggle-btn {[m
[31m-      margin-top: 10px;[m
[31m-      color: #1e3a8a;[m
[31m-      cursor: pointer;[m
[31m-    }[m
[31m-[m
[31m-    .recommend-carousel {[m
[31m-      margin-top: 50px;[m
[31m-      background: #fff;[m
[31m-      padding: 20px;[m
[31m-      position: relative;[m
[31m-      overflow: hidden;[m
[31m-      max-width: 1200px;[m
[31m-      margin-inline: auto;[m
[31m-      border-radius: 8[m
[31m-    }[m
[31m-[m
[31m-    .carousel-track {[m
[31m-      display: flex;[m
[31m-      flex-wrap: nowrap; /* 🔥 防止自動換行 */[m
[31m-      transition: transform 0.3s ease;[m
[31m-      will-change: transform;[m
[31m-    }[m
[31m-[m
[31m-    .carousel-card {[m
[31m-      flex: 0 0 250px; /* 🔥 固定每張卡片寬度 */[m
[31m-      margin-right: 20px;[m
[31m-      background: #fafafa;[m
[31m-      padding: 10px;[m
[31m-      border-radius: 10px;[m
[31m-      box-shadow: 0 2px 6px rgba(0,0,0,0.1);[m
[31m-      text-align: center;[m
[31m-      box-sizing: border-box;[m
[31m-    }[m
[31m-[m
[31m-    .carousel-card h4 {[m
[31m-      font-size: 16px;[m
[31m-      margin: 10px 0;[m
[31m-      height: 3.2em; /* 兩行字的高度 */[m
[31m-      overflow: hidden;[m
[31m-      text-overflow: ellipsis;[m
[31m-      display: -webkit-box;[m
[31m-      -webkit-line-clamp: 2;   /* 限制最多兩行 */[m
[31m-      -webkit-box-orient: vertical;[m
[31m-    }[m
[31m-    .carousel-nav {[m
[31m-      position: absolute;[m
[31m-      top: 50%;[m
[31m-      transform: translateY(-50%);[m
[31m-      font-size: 24px;[m
[31m-      background: rgba(0,0,0,0.5);[m
[31m-      color: white;[m
[31m-      padding: 5px 10px;[m
[31m-      cursor: pointer;[m
[31m-      z-index: 10;[m
[31m-      user-select: none;[m
[31m-    }[m
[31m-[m
[31m-    .carousel-nav.prev { left: 10px; }[m
[31m-    .carousel-nav.next { right: 10px; }[m
[31m-[m
[31m-    .footer {[m
[31m-        background-color: #333; color: white; text-align: center;[m
[31m-        padding: 20px; font-size: 14px;[m
[31m-    }[m
[31m-[m
[31m-  </style>[m
[32m+[m[32m    <meta charset="UTF-8">[m
[32m+[m[32m    <title><?php echo htmlspecialchars($category_name); ?> 商品列表</title>[m
[32m+[m[32m    <link rel="stylesheet" href="css/style.css">[m
 </head>[m
 <body>[m
[31m-[m
[31m-    <!-- 頁首區域 -->[m
[31m-    <header class="navbar">[m
[31m-        <div class="logo"><a href="index.php">LOGO</a></div>  [m
[31m-        <div class="search-bar"><input type="text" placeholder="搜尋產品..."></div>   <!-- 搜尋欄  算法待定 -->[m
[31m-[m
[31m-        <div class="nav-links">[m
[31m-            <a href="#">會員</a>[m
[31m-            <a href="#">問題</a>[m
[31m-            <a href="register.php">註冊</a>[m
[31m-            <a href="login.php">登入</a>[m
[31m-        </div>[m
[31m-[m
[31m-    </header>[m
[31m-[m
[31m-    <div class="category-bar">[m
[31m-        <?php if (!empty($categories)): ?>[m
[31m-            <?php foreach ($categories as $cat): ?>[m
[31m-            <div class="category-item">[m
[31m-                <a href="browse.php?category_id=<?php echo $cat['category_id']; ?>">[m
[31m-                    <?php echo htmlspecialchars($cat['name']); ?>[m
[31m-                </a>[m
[31m-            </div>[m
[31m-            <?php endforeach; ?>[m
[32m+[m[32m    <h1><?php echo htmlspecialchars($category_name); ?></h1>[m
[32m+[m
[32m+[m[32m    <div class="product-list">[m
[32m+[m[32m        <?php if ($result && mysqli_num_rows($result) > 0): ?>[m
[32m+[m[32m            <?php while($prod = mysqli_fetch_assoc($result)): ?>[m
[32m+[m[32m                <div class="product-item">[m
[32m+[m[32m                    <img src="<?php echo htmlspecialchars($prod['image_path']); ?>"[m[41m [m
[32m+[m[32m                         alt="<?php echo htmlspecialchars($prod['product_name']); ?>">[m
[32m+[m[32m                    <h3><?php echo htmlspecialchars($prod['product_name']); ?></h3>[m
[32m+[m[32m                    <p>價格：<?php echo number_format($prod['product_price']); ?> 元</p>[m
[32m+[m[32m                    <a href="item.php?product_id=<?php echo $prod['product_id']; ?>">立即購買</a>[m
[32m+[m[32m                </div>[m
[32m+[m[32m            <?php endwhile; ?>[m
         <?php else: ?>[m
[31m-            <span>目前尚無分類</span>[m
[32m+[m[32m            <p>這個分類下還沒有商品喔！</p>[m
         <?php endif; ?>[m
     </div>[m
[31m-[m
[31m-  <!-- 商品主圖 + 簡短說明 -->[m
[31m-  <div class="hero-ad">[m
[31m-    <div class="hero-left">[m
[31m-      <img src="<?php echo htmlspecialchars($prod['image_path']); ?>" alt="商品圖片">[m
[31m-    </div>[m
[31m-    <div class="hero-right">[m
[31m-      <h2><?php echo htmlspecialchars($prod['product_name']); ?></h2>[m
[31m-      <?php echo nl2br(htmlspecialchars($prod['short_description'])); ?>[m
[31m-      <div class="price">NT$<?php echo number_format($prod['price']); ?></div>[m
[31m-      <div class="buttons">[m
[31m-        <a href="#" class="button gray">加入購物車</a>[m
[31m-        <a href="#" class="button primary">立即購買</a>[m
[31m-      </div>[m
[31m-    </div>[m
[31m-  </div>[m
[31m-[m
[31m-[m
[31m-  <!-- 商品詳情 -->[m
[31m-  <div class="product-detail-box">[m
[31m-    <h2><?php echo htmlspecialchars($prod['product_name']); ?></h2>[m
[31m-    <div id="descBox" class="description">[m
[31m-      <?php echo nl2br(htmlspecialchars($prod['detail_description'])); ?>[m
[31m-    </div>[m
[31m-    <div id="toggleBtn" class="toggle-btn" onclick="toggleDescription()">查看更多</div>[m
[31m-  </div>[m
[31m-[m
[31m-  <br>[m
[31m-<!-- 猜你也喜歡 -->[m
[31m-[m
[31m-  <div class="recommend-carousel">[m
[31m-  [m
[31m-    <div class="carousel-nav prev" onclick="scrollCarousel(-1)">〈</div>[m
[31m-    <div class="carousel-nav next" onclick="scrollCarousel(1)">〉</div>[m
[31m-    <div id="carouselTrack" class="carousel-track">[m
[31m-      <?php while($p = $otherResult->fetch_assoc()): ?>[m
[31m-      <div class="carousel-card">[m
[31m-        <img src="<?php echo htmlspecialchars($p['image_path']); ?>" style="width:100%; height:140px; object-fit:contain;">[m
[31m-        <h4><?php echo htmlspecialchars($p['product_name']); ?></h4>[m
[31m-        <a href="item.php?product_id=<?php echo $p['product_id']; ?>" class="button primary">前往商品</a>[m
[31m-      </div>[m
[31m-      <?php endwhile; ?>[m
[31m-    </div>[m
[31m-  </div>[m
[31m-      [m
[31m-  <script>[m
[31m-    function toggleDescription() {[m
[31m-      const box = document.getElementById('descBox');[m
[31m-      const btn = document.getElementById('toggleBtn');[m
[31m-      box.classList.toggle('expanded');[m
[31m-      btn.textContent = box.classList.contains('expanded') ? '收起' : '查看更多';[m
[31m-    }[m
[31m-[m
[31m-    let scrollIndex = 0;[m
[31m-    function scrollCarousel(dir) {[m
[31m-      const track = document.getElementById('carouselTrack');[m
[31m-      const cardWidth = 300;[m
[31m-      scrollIndex += dir;[m
[31m-      scrollIndex = Math.max(0, scrollIndex);[m
[31m-      track.style.transform = `translateX(-${scrollIndex * cardWidth}px)`;[m
[31m-    }[m
[31m-  </script>[m
[31m-[m
[31m-<!-- 保留原 footer -->[m
[31m-<div class="footer">[m
[31m-  <div class="contact">[m
[31m-    <span>電話: 123-456-789</span>　[m
[31m-    <span>Email: example@mail.com</span>　[m
[31m-    <span>地址: 台北市XX區XX路</span>[m
[31m-  </div>[m
[31m-</div>[m
[31m-[m
 </body>[m
 </html>[m
 [m
[31m-[m
 <?php[m
 // 關閉連線[m
 mysqli_close($conn);[m
