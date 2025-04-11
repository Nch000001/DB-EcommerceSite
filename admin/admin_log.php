<?php
require_once '../lib/auth_helper.php';
requireLevel(3);

require_once '../lib/db.php';
$conn = getDBConnection();

$admin_id = $_GET['admin_id'] ?? '';
$table_name = $_GET['table'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// 查詢語法組裝
$where = "1";
if ($admin_id !== '') {
  $admin_id_escaped = $conn->real_escape_string($admin_id);
  $where .= " AND l.admin_id LIKE '%$admin_id_escaped%'";
}
if ($table_name !== '') {
  $table_name_escaped = $conn->real_escape_string($table_name);
  $where .= " AND l.target_table LIKE '%$table_name_escaped%'";
}
if ($from !== '') {
  $where .= " AND l.action_time >= '$from'";
}
if ($to !== '') {
  $where .= " AND l.action_time <= '$to'";
}

$sql = "SELECT l.* FROM admin_action_log l
        WHERE $where
        ORDER BY l.action_time DESC";
$logs = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>管理員操作紀錄</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    td.details-col {
      white-space: pre-wrap;
      word-break: break-word;
    }
  </style>
</head>
<body class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">管理員操作紀錄</h3>
    <a href="ecommerce_admin.php" class="btn btn-secondary">返回後台</a>
  </div>

  <form method="GET" class="mb-4">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">管理員 ID</label>
        <input type="text" name="admin_id" class="form-control" value="<?= htmlspecialchars($admin_id) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">資料表名稱</label>
        <input type="text" name="table" class="form-control" value="<?= htmlspecialchars($table_name) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">起始時間</label>
        <input type="datetime-local" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">結束時間</label>
        <div class="input-group">
          <input type="datetime-local" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
          <button type="submit" class="btn btn-primary">搜尋</button>
        </div>
      </div>
    </div>
  </form>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>時間</th>
        <th>帳號</th>
        <th>動作</th>
        <th>資料表</th>
        <th>對象 ID</th>
        <th>詳細資訊</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($log = $logs->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($log['action_time']) ?></td>
          <td><?= htmlspecialchars($log['admin_id']) ?></td>
          <td><?= htmlspecialchars($log['action_type']) ?></td>
          <td><?= htmlspecialchars($log['target_table']) ?></td>
          <td><?= htmlspecialchars($log['target_id']) ?></td>
          <td class="details-col"><?= nl2br(htmlspecialchars($log['details'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
