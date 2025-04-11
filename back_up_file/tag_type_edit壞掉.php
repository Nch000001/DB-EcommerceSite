
<?php
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_GET['id'])) exit('資料不完整');

$tag_type_id = $_GET['id'];
$type = $conn->query("SELECT * FROM tag_type WHERE tag_type_id = '$tag_type_id'")->fetch_assoc();
if (!$type) exit('資料不存在');

$categories = $conn->query("SELECT category_id, name FROM category ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$related_ids = [];
$res = $conn->query("SELECT category_id FROM tag_category WHERE tag_type_id = '$tag_type_id'");
while ($row = $res->fetch_assoc()) $related_ids[] = $row['category_id'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>編輯 Tag Type</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .category-search {
      margin-bottom: 10px;
    }
  </style>
</head>
<body class="container mt-4">
  <h3 class="mb-4">編輯 Tag Type</h3>
  <form action="tag_type_update.php" method="post">
    <input type="hidden" name="tag_type_id" value="<?= $type['tag_type_id'] ?>">

    <div class="mb-3">
      <label class="form-label">名稱</label>
      <input type="text" name="tag_type_name" class="form-control" value="<?= htmlspecialchars($type['name']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">對應分類</label>
      <input type="text" id="categorySearch" class="form-control category-search" placeholder="搜尋分類...">

      <?php foreach ($categories as $cat): ?>
        <div class="form-check category-option">
          <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['category_id'] ?>"
                 <?= in_array($cat['category_id'], $related_ids) ? 'checked' : '' ?>
                 id="cat<?= $cat['category_id'] ?>">
          <label class="form-check-label" for="cat<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-primary">更新</button>
    <a href="tag_type_manage.php" class="btn btn-secondary">取消</a>
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
