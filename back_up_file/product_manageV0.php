<?php
session_start();
require_once '../../lib/db.php';
require_once '../../lib/product_filter.php';

$conn = getDBConnection();

$category_id = $_GET['category_id'] ?? '';
$selected_tags = $_GET['tag_id'] ?? [];
$search_keyword = $_GET['search'] ?? '';

// 執行篩選與撈資料邏輯（套用 AND 條件與模糊搜尋）
[$tag_types, $product_result] = getProductFilterResults($conn, $category_id, $selected_tags, $search_keyword);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>產品管理 - 瀏覽模式</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .tag-type-group {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }
    .tag-options {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }
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
    .product-card h5 {
      font-size: 16px;
      margin-bottom: 10px;
      height: 3.2em;
      overflow: hidden;
      line-height: 1.6em;
    }
  </style>
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2>產品瀏覽</h2>
  <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
</div>

<form method="get" class="mb-4 border p-3 rounded bg-light">
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">分類</label>
      <select name="category_id" class="form-select" onchange="this.form.submit()">
        <option value="">所有分類</option>
        <?php
        $cats = $conn->query("SELECT category_id, name FROM category");
        while ($cat = $cats->fetch_assoc()):
        ?>
          <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $category_id ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">產品名稱搜尋</label>
      <div class="input-group">
        <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search_keyword) ?>" placeholder="輸入產品名稱關鍵字">
        <button class="btn btn-outline-primary" type="submit">搜尋</button>
        <a href="product_manage.php" class="btn btn-outline-secondary">重設</a>
      </div>
    </div>
  </div>

  <?php if (!empty($tag_types)): ?>
    <div class="tag-type-group mb-3">
      <?php foreach ($tag_types as $type): ?>
        <div>
          <strong><?= htmlspecialchars($type['tag_type_name']) ?></strong>
          <div class="tag-options">
            <?php foreach ($type['tags'] as $tag): ?>
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="tag_id[]" value="<?= $tag['tag_id'] ?>"
                       <?= in_array($tag['tag_id'], $selected_tags) ? 'checked' : '' ?>>
                <?= htmlspecialchars($tag['name']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-sm btn-outline-primary">套用篩選</button>
  <?php endif; ?>
</form>

<p class="text-muted mb-3">符合條件的商品數量：<?= $product_result->num_rows ?> 筆</p>

<div class="product-grid">
  <?php if ($product_result && $product_result->num_rows > 0): ?>
    <?php while ($row = $product_result->fetch_assoc()): ?>
      <div class="product-card">
        <img src="../../<?= htmlspecialchars($row['image_path']) ?>" alt="商品圖片">
        <h5><?= htmlspecialchars($row['product_name']) ?></h5>
        <a href="product_edit.php?id=<?= $row['product_id'] ?>" class="btn btn-outline-primary btn-sm">修改</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>目前沒有符合的商品。</p>
  <?php endif; ?>
</div>

</body>
</html>
