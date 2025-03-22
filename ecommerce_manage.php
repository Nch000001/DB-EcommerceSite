<?php
include 'db.php';
session_start();

if(!isset($_SESSION['super_user_account'])) {
    header("Location: ecommerce_admin_login.php");
    exit;
}

if (!isset($_GET['table'])) {
  header("Location: ecommerce_admin.php");
  exit;
}

$table = $conn->real_escape_string($_GET['table']);

// 取得欄位資訊
$columns = [];
$result = $conn->query("DESCRIBE $table");
while ($row = $result->fetch_assoc()) {
  $columns[] = $row;
}

// 處理新增
if (isset($_POST['insert'])) {
  $fields = [];
  $values = [];
  foreach ($columns as $column) {
    if ($column['Extra'] !== 'auto_increment') { // 跳過自動遞增 id
      $field = $column['Field'];
      $fields[] = $field;
      $values[] = "'" . $conn->real_escape_string($_POST[$field]) . "'";
    }
  }
  $fields_str = implode(",", $fields);
  $values_str = implode(",", $values);
  $conn->query("INSERT INTO $table ($fields_str) VALUES ($values_str)");
  header("Location: ecommerce_manage.php?table=$table");
  exit;
}

// 處理更新
if (isset($_POST['update_id'])) {
  $update_id = intval($_POST['update_id']);
  $updates = [];
  foreach ($columns as $column) {
    if ($column['Extra'] !== 'auto_increment') {
      $field = $column['Field'];
      $value = $conn->real_escape_string($_POST[$field]);
      $updates[] = "$field='$value'";
    }
  }
  $updates_str = implode(",", $updates);
  $conn->query("UPDATE $table SET $updates_str WHERE id=$update_id");
  header("Location: ecommerce_manage.php?table=$table");
  exit;
}

// 刪除
if (isset($_GET['delete_id'])) {
  $delete_id = intval($_GET['delete_id']);
  $conn->query("DELETE FROM $table WHERE id=$delete_id");
  header("Location: ecommerce_manage.php?table=$table");
  exit;
}

// 查詢資料
$result = $conn->query("SELECT * FROM $table");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>管理表格: <?= $table ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2>管理表格: <?= $table ?></h2>
<a href="ecommerce_admin.php" class="btn btn-secondary mb-3">回到表格清單</a>
<a href="logout.php" class="btn btn-danger mb-3">登出</a>

<!-- 新增資料 -->
<h4>新增資料</h4>
<form method="POST" class="row g-3 mb-4">
  <?php foreach ($columns as $column): ?>
    <?php if ($column['Extra'] !== 'auto_increment'): ?>
      <div class="col-md-4">
        <label class="form-label"><?= $column['Field'] ?></label>
        <input type="text" name="<?= $column['Field'] ?>" class="form-control" required>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  <div class="col-12">
    <button type="submit" name="insert" class="btn btn-success">新增</button>
  </div>
</form>

<!-- 資料表 -->
<h4>資料列表</h4>
<table class="table table-bordered">
  <thead class="table-light">
    <tr>
      <?php foreach ($columns as $column): ?>
        <th><?= $column['Field'] ?></th>
      <?php endforeach; ?>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <form method="POST">
          <?php foreach ($columns as $column): ?>
            <td>
              <?php if ($column['Extra'] === 'auto_increment'): ?>
                <?= htmlspecialchars($row[$column['Field']]) ?>
                <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
              <?php else: ?>
                <input type="text" name="<?= $column['Field'] ?>" value="<?= htmlspecialchars($row[$column['Field']]) ?>" class="form-control">
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
          <td>
            <button type="submit" class="btn btn-primary btn-sm">更新</button>
            <a href="ecommerce_manage.php?table=<?= $table ?>&delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('確定刪除？')">刪除</a>
          </td>
        </form>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>