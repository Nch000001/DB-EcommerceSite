<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連線
include 'db.php';
global $conn;

// 查詢所有商品（含分類、品牌、標籤）
$sql = "
    SELECT
        p.product_id,
        p.product_name,
        p.image_path,
        p.price,
        c.name AS category_name,
        b.name AS brand_name,
        t.name AS tag_name,
        tt.name AS tag_type
    FROM product p
    JOIN category c ON p.category_id = c.category_id
    JOIN brand b ON p.brand_id = b.brand_id
    LEFT JOIN product_tag pt ON p.product_id = pt.product_id
    LEFT JOIN tag t ON pt.tag_id = t.tag_id
    LEFT JOIN tag_type tt ON t.tag_type_id = tt.tag_type_id
    ORDER BY p.inserting_time DESC
";

$result = $conn->query($sql);

$productData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pid = $row['product_id'];

        if (!isset($productData[$pid])) {
            $productData[$pid] = [
                'product_name' => $row['product_name'],
                'image_path' => $row['image_path'],
                'price' => $row['price'],
                'category' => $row['category_name'],
                'brand' => $row['brand_name'],
                'tags' => []
            ];
        }

        if ($row['tag_name']) {
            $productData[$pid]['tags'][] = [
                'type' => $row['tag_type'],
                'name' => $row['tag_name']
            ];
        }
    }
}
// 渲染頁面用 $productData 就可以印出所有商品 + 標籤資訊
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>商品列表</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .product-card {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 20px;
      width: 300px;
    }
    .product-card img {
      max-width: 100%;
      border-radius: 6px;
    }
    .tag {
      display: inline-block;
      padding: 4px 8px;
      background-color: #eee;
      border-radius: 12px;
      font-size: 12px;
      margin: 4px 4px 0 0;
    }
    .filter-section {
      margin-bottom: 20px;
    }
    .filter-section select {
      padding: 4px 8px;
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <h1>商品列表</h1>

    <div class="filter-section">
        <label for="tagFilter">依標籤篩選：</label>
        <select id="tagFilter">
        <option value="">-- 全部 --</option>
        </select>
    </div>

    <div id="productContainer"></div>

    <script>
    const products = <?php echo json_encode(array_values($productData), JSON_UNESCAPED_UNICODE); ?>;

    const tagSet = new Set();
    products.forEach(p => {
      p.tags.forEach(tag => tagSet.add(tag.name));
    });

    const tagFilter = document.getElementById('tagFilter');
    tagSet.forEach(tag => {
      const opt = document.createElement('option');
      opt.value = tag;
      opt.textContent = tag;
      tagFilter.appendChild(opt);
    });

    tagFilter.addEventListener('change', renderProducts);

    function renderProducts() {
      const container = document.getElementById('productContainer');
      container.innerHTML = '';
      const selectedTag = tagFilter.value;

      products.forEach(p => {
        if (selectedTag && !p.tags.some(tag => tag.name === selectedTag)) return;

        const card = document.createElement('div');
        card.className = 'product-card';

        card.innerHTML = `
          <img src="${p.image_path}" alt="${p.product_name}">
          <h3>${p.product_name}</h3>
          <p>分類：${p.category}</p>
          <p>品牌：${p.brand}</p>
          <p>價格：$${p.price}</p>
        `;

        p.tags.forEach(tag => {
          const span = document.createElement('span');
          span.className = 'tag';
          span.textContent = `${tag.type}: ${tag.name}`;
          card.appendChild(span);
        });

        container.appendChild(card);
      });
    }

    renderProducts();
  </script>
</body>
</html>
