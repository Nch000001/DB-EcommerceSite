<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../lib/db.php';
require_once '../lib/product_filter.php';

$conn = getDBConnection();

if(!isset($_SESSION['super_user_account'])) {
    header("Location: ecommerce_admin_login.php");
    exit;
}

$sql = "SELECT admin_id, level FROM super_admin WHERE admin_account = '" . $_SESSION['super_user_account'] . "'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$_SESSION['user_level'] = $row['level'];
$_SESSION['super_user_id'] = $row['admin_id'];

$mode = isset($_GET['mode']) && $_GET['mode'] === 'manage' ? 'manage' : 'add';

$category_id = $_GET['category_id'] ?? '';
$selected_tags = $_GET['tag_id'] ?? [];

[$tag_types, $product_result] = getProductFilterResults($conn, $category_id, $selected_tags);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>後台管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .user-info {
      background-color: #f8f9fa;
      padding: 8px 12px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      font-size: 18px;
      margin-right: 10%;
      margin-left: 20px;
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
    .tag-type-group {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* 每列三個 */
      gap: 1rem;
    }
    .tag-options {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

  </style>
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold">資料庫表格清單</h4>
  <div class="user-info">
    使用者: <?= htmlspecialchars($_SESSION['super_user_id']) ?>　
    權限等級: <?= htmlspecialchars($_SESSION['user_level']) ?>
  </div>
  <a href="../logout.php" class="btn btn-danger">登出</a>
</div>

<div class="text-center mb-4">
  <a href="?mode=add" class="btn btn-<?= $mode === 'add' ? 'success' : 'outline-success' ?>">新增模式</a>
  <a href="?mode=manage" class="btn btn-<?= $mode === 'manage' ? 'primary' : 'outline-primary' ?>">管理模式</a>
</div>

<div class="d-flex justify-content-center flex-wrap gap-3 mt-3">
  <a href="<?= $mode === 'add' ? 'product/product_form.php' : 'product/product_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">產品</a>
  <a href="<?= $mode === 'add' ? 'ad/ad_form.php' : 'ad/ad_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">廣告</a>
  <a href="ecommerce_manage.php?table=user" class="btn btn-outline-dark px-4 py-2">用戶</a>
  <a href="<?= $mode === 'add' ? 'tag/tag_type_add.php' : 'tag/tag_type_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">標籤</a>
  <a href="<?= $mode === 'add' ? 'tag/tag_add.php' : 'tag/tag_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">標籤細項</a>
  <a href="<?= $mode === 'add' ? 'ecommerce_add.php?table=category' : 'ecommerce_manage.php?table=category' ?>" class="btn btn-outline-dark px-4 py-2">分類</a>
  <a href="<?= $mode === 'add' ? 'ecommerce_add.php?table=brand' : 'ecommerce_manage.php?table=brand' ?>" class="btn btn-outline-dark px-4 py-2">品牌</a>
</div>

<?php if ($mode === 'manage'): ?>
  <hr>

    <form method="get" class="filter-box">
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

      <button type="submit" class="btn btn-sm btn-outline-primary mt-2">套用篩選</button>
    </form>

    <!-- 展示商品 -->
    <div class="product-grid mt-4">
      <?php if ($product_result && $product_result->num_rows > 0): ?>
        <?php while ($row = $product_result->fetch_assoc()): ?>
          <div class="product-card">
            <img src="../<?= htmlspecialchars($row['image_path']) ?>" alt="商品圖片">
            <h5 class="mt-2"><?= htmlspecialchars($row['product_name']) ?></h5>
            <a href="product/product_edit.php?id=<?= $row['product_id'] ?>" class="btn btn-outline-primary btn-sm mt-2">修改</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>目前沒有符合的商品。</p>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

</body>
</html>
