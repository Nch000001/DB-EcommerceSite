<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);

require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_GET['id'])) {
    exit('缺少 tag_type ID');
}

$tag_type_id = $_GET['id'];
$tag_type = $conn->query("SELECT * FROM tag_type WHERE tag_type_id = '$tag_type_id'")->fetch_assoc();

if (!$tag_type) exit('找不到該 tag_type');

$categories = $conn->query("SELECT category_id, name FROM category ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// 取得此 tag_type 的所有已關聯的 category_id
$existing_cats = [];
$result = $conn->query("SELECT category_id FROM tag_category WHERE tag_type_id = '$tag_type_id'");
while ($row = $result->fetch_assoc()) {
    $existing_cats[] = $row['category_id'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>編輯標籤類型</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    function filterCategories() {
      const keyword = document.getElementById('categorySearch').value.toLowerCase();
      document.querySelectorAll('.form-check').forEach(div => {
        const label = div.innerText.toLowerCase();
        div.style.display = label.includes(keyword) ? 'block' : 'none';
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        const tagTypeId = "<?= $tag_type_id ?>";
        if (tagTypeId.startsWith('US')) {
        form.addEventListener('submit', function (e) {
            const checked = document.querySelectorAll('input[name="category_ids[]"]:checked');
            if (checked.length === 1) {
            const confirmed = confirm("⚠️ 此通用（US 開頭）標籤目前僅關聯一個分類，建議您考慮是否要拆分為分類專用。\n\n是否仍要儲存？ \n");
            if (!confirmed) {
                e.preventDefault(); // 停止表單提交
            }
            }
        });
        }
    });

  </script>
</head>
<body class="container mt-4">
  <h2 class="mb-4">編輯標籤類型：<?= htmlspecialchars($tag_type_id) ?></h2>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <form method="POST" action="tag_type_update.php">
    <input type="hidden" name="tag_type_id" value="<?= htmlspecialchars($tag_type_id) ?>">

    <div class="mb-3">
      <label class="form-label">標籤類型名稱</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tag_type['name']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">搜尋分類</label>
      <input type="text" id="categorySearch" class="form-control" onkeyup="filterCategories()" placeholder="輸入關鍵字篩選分類...">
    </div>

    <div class="mb-3">
      <label class="form-label">適用分類（可多選）</label>
      <?php foreach ($categories as $cat): ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $cat['category_id'] ?>"
            id="cat<?= $cat['category_id'] ?>" <?= in_array($cat['category_id'], $existing_cats) ? 'checked' : '' ?>>
          <label class="form-check-label" for="cat<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-primary">儲存變更</button>
    <a href="tag_type_manage.php" class="btn btn-secondary">返回</a>
  </form>
</body>
</html>
