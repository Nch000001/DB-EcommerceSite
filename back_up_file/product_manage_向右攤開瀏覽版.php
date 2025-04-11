<?php
session_start();
require_once '../../lib/db.php';
require_once '../../lib/product_filter.php';

$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

$mode = 'manage';

$category_id = $_GET['category_id'] ?? '';
$selected_tags = $_GET['tag_id'] ?? [];

[$tag_types, $product_result] = getProductFilterResults($conn, $category_id, $selected_tags);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>產品管理</title>
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
    .product-scroll-container {
      overflow-x: auto;
      white-space: nowrap;
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .product-scroll-inner {
      display: inline-flex;
      gap: 1rem;
      align-items: flex-start;
    }
    .product-scroll-inner img {
      width: 120px;
      height: auto;
      object-fit: contain;
      border: 1px solid #ddd;
      border-radius: 6px;
    }
    .product-scroll-inner .form-control,
    .product-scroll-inner .form-select {
      min-width: 200px;
    }
    .product-scroll-inner .button-group {
      display: flex;
      justify-content: flex-end;   /* 右下角：flex-end。若要置中改為 center */
      margin-top: auto;            /* 推到底部 */
      gap: 10px;
    }
  </style>
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2>產品管理</h2>
  <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
</div>

<form method="get" class="mb-4 border p-3 rounded">
  <input type="hidden" name="mode" value="manage">
  <div class="mb-3">
    <label>分類</label>
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

  <div class="tag-type-group">
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
  <button type="submit" class="btn btn-outline-primary mt-3">套用篩選</button>
</form>

<?php if ($product_result && $product_result->num_rows > 0): ?>
  <?php while ($row = $product_result->fetch_assoc()): ?>
    <form method="POST" action="product_manage_update.php" class="product-scroll-container">
      <div class="product-scroll-inner">
        <img src="../../<?= htmlspecialchars($row['image_path']) ?>" alt="商品圖片">

        <input type="hidden" name="product_id" value="<?= htmlspecialchars($row['product_id']) ?>">

        <div>
          <label>商品編號</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['product_id']) ?>" disabled>
        </div>

        <div>
          <label>商品名稱</label>
          <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($row['product_name']) ?>">
        </div>

        <div>
          <label>圖片路徑</label>
          <input type="text" name="image_path" class="form-control" value="<?= htmlspecialchars($row['image_path']) ?>">
        </div>

        <div>
          <label>簡短描述</label>
          <input type="text" name="short_description" class="form-control" value="<?= htmlspecialchars($row['short_description'] ?? '') ?>">
        </div>

        <div>
          <label>詳細描述</label>
          <input type="text" name="detail_description" class="form-control" value="<?= htmlspecialchars($row['detail_description'] ?? '') ?>">
        </div>

        <div>
          <label>價格</label>
          <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($row['price']) ?>">
        </div>

        <div>
          <label>上架狀態</label>
          <select name="is_active" class="form-select">
            <option value="1" <?= $row['is_active'] ? 'selected' : '' ?>>上架</option>
            <option value="0" <?= !$row['is_active'] ? 'selected' : '' ?>>下架</option>
          </select>
        </div>

        <div>
          <label>建立時間</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['inserting_time']) ?>" disabled>
        </div>

        <div class="right-column d-flex flex-column justify-content-end align-items-end" style="min-width: 120px;">
          <div class="button-group mt-2">
            <button type="submit" name="update" class="btn btn-primary btn-sm">更新</button>
            <button type="submit" name="delete" class="btn btn-danger btn-sm"
                    onclick="return confirm('確定要刪除這筆商品嗎？')">刪除</button>
          </div>
        </div>

      </div>
    </form>
  <?php endwhile; ?>
<?php else: ?>
  <p>目前沒有符合條件的商品。</p>
<?php endif; ?>

</body>
</html>