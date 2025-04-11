<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);

require_once '../../lib/db.php';
$conn = getDBConnection();

// 權限檢查
if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

// 模糊搜尋條件
$search = $_GET['search'] ?? '';

$sql = "
SELECT tt.tag_type_id, tt.name AS tag_type_name, GROUP_CONCAT(c.name) AS category_names
FROM tag_type tt
JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id
JOIN category c ON tc.category_id = c.category_id
";

$params = [];
$condition = [];

if ($search !== '') {
    $condition[] = "(tt.name LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($condition)) {
    $sql .= " WHERE " . implode(" AND ", $condition);
}

$sql .= " GROUP BY tt.tag_type_id, tt.name ORDER BY tt.tag_type_id";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$tag_types = [];
while ($row = $result->fetch_assoc()) {
    $tag_types[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>tag_type 管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .tag-card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .tag-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .category-list {
      margin-top: 10px;
      font-size: 14px;
      color: #666;
    }
  </style>
</head>
<body class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>tag_type 管理</h3>
    <a href="../ecommerce_admin.php" class="btn btn-secondary">返回首頁</a>
  </div>

  <form method="get" class="mb-4">
    <div class="input-group">
      <input type="text" name="search" class="form-control" placeholder="搜尋 tag_type 或分類名稱..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-outline-primary">搜尋</button>
    </div>
  </form>

  <?php if (count($tag_types) > 0): ?>
    <?php foreach ($tag_types as $type): ?>
      <div class="tag-card">
        <div class="tag-header">
          <strong><?= htmlspecialchars($type['tag_type_id']) ?> - <?= htmlspecialchars($type['tag_type_name']) ?></strong>
          <div>
            <a href="tag_type_edit.php?id=<?= $type['tag_type_id'] ?>" class="btn btn-sm btn-outline-primary">編輯</a>
            <a href="tag_type_delete.php?id=<?= $type['tag_type_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('確定要刪除此 tag_type 及其分類對應嗎？')">刪除</a>
          </div>
        </div>
        <div class="category-list">
          適用分類：<?= htmlspecialchars($type['category_names']) ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>目前沒有符合的 tag_type。</p>
  <?php endif; ?>

</body>
</html>
