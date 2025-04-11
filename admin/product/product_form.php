<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
  header("Location: ../ecommerce_admin_login.php");
  exit;
}

$categories = $conn->query("SELECT category_id, name FROM category ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$brands = $conn->query("SELECT brand_id, name FROM brand ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$editing = isset($product);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?= $editing ? '編輯商品' : '新增商品' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h2><?= $editing ? '編輯商品' : '新增商品' ?></h2>

<form method="POST" action="<?= $editing ? 'product_manage_update.php' : 'product_add_save.php' ?>">
  <?php if ($editing): ?>
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label">類別</label>
    <select name="category_id" id="category_select" class="form-select" required>
      <option value="">請選擇分類</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['category_id'] ?>" <?= $editing && $product['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">商品代碼</label>
    <input type="text" name="product_id_display" id="product_id" class="form-control"
           value="<?= $editing ? htmlspecialchars($product['product_id']) : '' ?>" readonly required>
    <input type="hidden" name="product_id" id="product_id_hidden"
           value="<?= $editing ? htmlspecialchars($product['product_id']) : '' ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">品牌</label>
    <select name="brand_id" class="form-select" required>
      <?php foreach ($brands as $brand): ?>
        <option value="<?= $brand['brand_id'] ?>" <?= $editing && $product['brand_id'] == $brand['brand_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($brand['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">商品名稱</label>
    <input type="text" name="product_name" class="form-control" value="<?= $editing ? htmlspecialchars($product['product_name']) : '' ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">圖片路徑</label>
    <input type="text" name="image_path" id="image_path" class="form-control" value="<?= $editing ? htmlspecialchars($product['image_path']) : 'img/' ?>" required>
    <button type="button" class="btn btn-outline-secondary mt-2" onclick="previewImage()">預覽圖片</button>
    <div id="image_preview" class="mt-3"></div>
  </div>

  <div class="mb-3">
    <label class="form-label">短描述</label>
    <textarea name="short_description" class="form-control" rows="2"><?= $editing ? htmlspecialchars($product['short_description']) : '' ?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">詳細描述</label>
    <textarea name="detail_description" class="form-control" rows="5"><?= $editing ? htmlspecialchars($product['detail_description']) : '' ?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">價格</label>
    <input type="text" name="price" class="form-control" value="<?= $editing ? htmlspecialchars($product['price']) : '' ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">上架布林</label>
    <input type="text" name="is_active" class="form-control" value="<?= $editing ? htmlspecialchars($product['is_active']) : '1' ?>" required>
  </div>

  <!-- 標籤區塊 -->
  <div id="tag-section" class="mb-3" style="<?= ($editing || isset($_GET['category_id'])) ? '' : 'display: none;' ?>">
    <?php
    $category_id = $editing ? $product['category_id'] : ($_GET['category_id'] ?? '');
    if ($category_id) {
      $_GET['category_id'] = $category_id;
      include 'get_tags_by_category.php';
    }
    ?>
    <button type="button" class="btn btn-outline-primary mt-2" onclick="resetTags()">重設標籤</button>
  </div>

    <div class="mt-4 d-flex gap-2">
    <?php if ($editing): ?>
        <!-- 更新按鈕 -->
        <form method="POST" action="product_manage_update.php" class="m-0">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
        <button type="submit" class="btn btn-success">送出</button>
        </form>

        <!-- 刪除按鈕 -->
        <form method="POST" action="product_delete.php" class="m-0" onsubmit="return confirm('確定要刪除此商品嗎？')">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
        <button type="submit" class="btn btn-danger">刪除</button>
        </form>

        <!-- 返回 -->
        <a href="product_manage.php?category_id=<?= htmlspecialchars($_GET['category_id'] ?? '') ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="btn btn-secondary">返回</a>
    <?php else: ?>
        <!-- 新增模式的表單提交與返回 -->
        <button type="submit" class="btn btn-success">送出</button>
        <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
    <?php endif; ?>
    </div>

<?php if (!$editing): ?>
</form>
<?php endif; ?>


<script>
function previewImage() {
  const path = document.getElementById('image_path').value.trim();
  const preview = document.getElementById('image_preview');
  if (path !== '') {
    preview.innerHTML = `<img src="../../${path}" class="img-fluid" style="max-height:200px">`;
  } else {
    preview.innerHTML = '<span class="text-danger">尚未提供圖片路徑</span>';
  }
}

function resetTags() {
  const radios = document.querySelectorAll('#tag-section input[type="radio"]');
  if (radios.length === 0) {
    alert("目前沒有載入任何標籤可重設，請先選擇分類。");
    return;
  }
  radios.forEach(radio => radio.checked = false);
}

document.addEventListener('DOMContentLoaded', () => {
  const categorySelect = document.getElementById('category_select');
  const editing = <?= $editing ? 'true' : 'false' ?>;
  if (!editing && categorySelect) {
    categorySelect.addEventListener('change', function () {
      const categoryId = this.value;
      if (!categoryId) {
        document.getElementById('tag-section').style.display = 'none';
        document.getElementById('tag-section').innerHTML = '';
        document.getElementById('product_id').value = '';
        return;
      }
      fetch('get_next_product_id.php?category_id=' + categoryId)
        .then(res => res.text())
        .then(data => {
          document.getElementById('product_id').value = data;
          document.getElementById('product_id_hidden').value = data;
        });
      fetch('get_tags_by_category.php?category_id=' + categoryId)
        .then(res => res.text())
        .then(html => {
          const section = document.getElementById('tag-section');
          section.innerHTML = html + section.innerHTML;
          section.style.display = 'block';
        });
    });
  }
});
</script>

</body>
</html>
