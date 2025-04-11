<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();
require_once '../../lib/log_helper.php';

// 搜尋邏輯（模糊搜尋）
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// SQL 組合
$sql = "SELECT t.tag_id, t.name AS tag_name, t.tag_type_id, tt.name AS tag_type_name
        FROM tag t
        JOIN tag_type tt ON t.tag_type_id = tt.tag_type_id";

if (!empty($search)) {
    $sql .= " WHERE t.name LIKE '%$search%' OR tt.name LIKE '%$search%'";
}

$sql .= " ORDER BY t.tag_type_id, t.tag_id";
$result = $conn->query($sql);

// 更新
if (isset($_POST['update'])) {
  $tag_id = $conn->real_escape_string($_POST['tag_id']);
  $name = $conn->real_escape_string($_POST['name']);

  // 先取得原本資料
  $get_old = $conn->query("
      SELECT t.name AS tag_name, tt.name AS tag_type_name 
      FROM tag t 
      JOIN tag_type tt ON t.tag_type_id = tt.tag_type_id 
      WHERE t.tag_id = '$tag_id'
  ");

  $old = $get_old->fetch_assoc();
  $origin_name = $old['tag_name'] ?? '未知';
  $tag_type_name = $old['tag_type_name'] ?? '未知';

  $conn->query("UPDATE tag SET name = '$name' WHERE tag_id = '$tag_id'");

  $details = "更新標籤細項：$tag_type_name [$origin_name] → [$name]";
  log_admin_action($conn, $_SESSION['super_user_id'], '更新', 'tag', $tag_id, $details);

  header("Location: tag_manage.php");
  exit;
}

// 刪除
if (isset($_GET['delete']) && isset($_GET['tag_id'])) {
  $tag_id = $conn->real_escape_string($_GET['tag_id']);

  // 拿到要刪除的 tag 的名稱與類型
  $get_info = $conn->query("
      SELECT t.name AS tag_name, tt.name AS tag_type_name 
      FROM tag t 
      JOIN tag_type tt ON t.tag_type_id = tt.tag_type_id 
      WHERE t.tag_id = '$tag_id'
  ");
  $info = $get_info->fetch_assoc();
  $tag_name = $info['tag_name'] ?? '未知';
  $tag_type_name = $info['tag_type_name'] ?? '未知';

  $conn->query("DELETE FROM tag WHERE tag_id = '$tag_id'");

  $details = "刪除標籤細項：{$tag_name}（標籤：{$tag_type_name}）";
  
  log_admin_action($conn, $_SESSION['super_user_id'], '刪除', 'tag', $tag_id, $details);

  header("Location: tag_manage.php");
  exit;
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>管理標籤</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="m-0">管理標籤</h2>
  <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
</div>

<!-- 搜尋 -->
<form method="get" class="input-group mb-3">
  <input type="text" name="search" class="form-control" placeholder="tag_type_name || tag_name..." value="<?= htmlspecialchars($search) ?>">
  <button type="submit" class="btn btn-outline-primary">搜尋</button>
</form>

<table class="table table-bordered text-center">
  <thead class="table-light">
    <tr>
      <th>Tag 類型 (tag_type)</th>
      <th>Tag ID</th>
      <th>Tag 名稱</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <form method="POST">
          <td>
            <input type="text" class="form-control" value="<?= htmlspecialchars($row['tag_type_name']) ?>" readonly>
          </td>
          <td>
            <input type="text" class="form-control" name="tag_id" value="<?= htmlspecialchars($row['tag_id']) ?>" readonly>
          </td>
          <td>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['tag_name']) ?>">
          </td>
          <td>
            <div class="d-flex justify-content-center gap-2">
              <button type="submit" name="update" class="btn btn-primary btn-sm">更新</button>
              <a href="?delete=1&tag_id=<?= urlencode($row['tag_id']) ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('確定要刪除這筆標籤嗎？')">刪除</a>
            </div>
          </td>
        </form>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
