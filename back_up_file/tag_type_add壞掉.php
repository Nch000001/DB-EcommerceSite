
<?php
require_once '../../lib/db.php';
$conn = getDBConnection();

$categories = $conn->query("SELECT category_id, name FROM category ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>新增 Tag Type</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .category-search {
      margin-bottom: 10px;
    }
  </style>
</head>
<body class="container mt-4">
  <h3 class="mb-4">新增 Tag Type</h3>
  <form action="tag_type_add_save.php" method="post">
    <div class="mb-3">
      <label class="form-label">名稱</label>
      <input type="text" name="tag_type_name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">選擇適用分類</label>
      <input type="text" id="categorySearch" class="form-control category-search" placeholder="搜尋分類...">

      <div class="form-check">
        <input type="checkbox" name="universal" value="1" id="universal" class="form-check-input">
        <label for="universal" class="form-check-label">通用（所有分類可用）</label>
      </div>

      <?php foreach ($categories as $cat): ?>
        <div class="form-check category-option">
          <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['category_id'] ?>" id="cat<?= $cat['category_id'] ?>">
          <label class="form-check-label" for="cat<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-success">新增</button>
    <a href="tag_type_manage.php" class="btn btn-secondary">返回</a>
  </form>

  <script>
    const input = document.getElementById('categorySearch');
    input.addEventListener('input', function () {
      const keyword = this.value.toLowerCase();
      document.querySelectorAll('.category-option').forEach(opt => {
        const text = opt.innerText.toLowerCase();
        opt.style.display = text.includes(keyword) ? 'block' : 'none';
      });
    });
  </script>
</body>
</html>
