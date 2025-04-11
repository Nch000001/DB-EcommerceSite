<?php
session_start();
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_GET['id'])) {
    exit('資料不完整');
}

$tag_type_id = $_GET['id'];

// 撈出 tag_type 主資料
$type_sql = "SELECT * FROM tag_type WHERE tag_type_id = ?";
$stmt = $conn->prepare($type_sql);
$stmt->bind_param("s", $tag_type_id);
$stmt->execute();
$type_result = $stmt->get_result();

if ($type_result->num_rows === 0) {
    exit('資料不存在');
}

$tag_type = $type_result->fetch_assoc();

// 撈出所有 category
$categories = $conn->query("SELECT category_id, name FROM category")->fetch_all(MYSQLI_ASSOC);

// 撈出目前此 tag_type 對應的 category_id
$current_categories = [];
$cat_result = $conn->query("SELECT category_id FROM tag_category WHERE tag_type_id = '$tag_type_id'");
while ($row = $cat_result->fetch_assoc()) {
    $current_categories[] = $row['category_id'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>編輯 Tag Type</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h3>編輯 Tag Type</h3>
  <form action="tag_type_update.php" method="POST">
    <input type="hidden" name="tag_type_id" value="<?= htmlspecialchars($tag_type['tag_type_id']) ?>">

    <div class="mb-3">
      <label>名稱</label>
      <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($tag_type['name']) ?>">
    </div>

    <div class="mb-3">
      <label>適用分類 (至少選一個)</label><br>
      <?php foreach ($categories as $cat): ?>
        <div class="form-check form-check-inline">
          <input type="checkbox" class="form-check-input" name="categories[]" value="<?= $cat['category_id'] ?>"
            <?= in_array($cat['category_id'], $current_categories) ? 'checked' : '' ?>>
          <label class="form-check-label"><?= htmlspecialchars($cat['name']) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-primary">儲存修改</button>
    <a href="tag_type_manage.php" class="btn btn-secondary">返回</a>
  </form>
</body>
</html>
