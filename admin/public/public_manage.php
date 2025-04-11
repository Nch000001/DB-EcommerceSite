<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
require_once '../../lib/log_helper.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account']) || !isset($_GET['table'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

$table = $conn->real_escape_string($_GET['table']);

// 欄位資料
$columns = [];
$result = $conn->query("DESCRIBE $table");
while ($row = $result->fetch_assoc()) {
    $columns[] = $row;
}

// Primary Key
$primary_keys = [];
$pk_result = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND CONSTRAINT_NAME = 'PRIMARY'");
while ($pk = $pk_result->fetch_assoc()) {
    $primary_keys[] = $pk['COLUMN_NAME'];
}

// Foreign Key 關聯
$foreign_keys = [];
$fk_result = $conn->query("SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND REFERENCED_TABLE_NAME IS NOT NULL");
while ($fk = $fk_result->fetch_assoc()) {
    $foreign_keys[$fk['COLUMN_NAME']] = [
        'parent_table' => $fk['REFERENCED_TABLE_NAME'],
        'parent_column' => $fk['REFERENCED_COLUMN_NAME'],
    ];
}

// 更新
if (isset($_POST['update'])) {
    $updates = [];
    foreach ($columns as $column) {
        if ($column['Extra'] !== 'auto_increment' && $column['Default'] !== 'current_timestamp()') {
            $field = $column['Field'];
            $value = $conn->real_escape_string($_POST[$field]);
            $updates[] = "$field='$value'";
        }
    }
    $where = [];
    foreach ($primary_keys as $pk) {
        $pk_value = $conn->real_escape_string($_POST[$pk]);
        $where[] = "$pk='$pk_value'";
    }

    $conn->query("UPDATE $table SET " . implode(',', $updates) . " WHERE " . implode(' AND ', $where));

    // 紀錄更新
    $log_id = implode('-', array_map(fn($pk) => $_POST[$pk], $primary_keys));
    log_admin_action($conn, $_SESSION['super_user_id'], '更新', $table, $log_id, "更新 $table 資料：$log_id");

    header("Location: public_manage.php?table=$table");
    exit;
}

// 刪除
if (isset($_GET['delete'])) {
    $where = [];
    foreach ($primary_keys as $pk) {
        $pk_value = $conn->real_escape_string($_GET[$pk]);
        $where[] = "$pk='$pk_value'";
    }

    // 紀錄刪除
    $log_id = implode('-', array_map(fn($pk) => $_GET[$pk], $primary_keys));
    log_admin_action($conn, $_SESSION['super_user_id'], '刪除', $table, $log_id, "刪除 $table 資料：$log_id");

    $conn->query("DELETE FROM $table WHERE " . implode(' AND ', $where));
    header("Location: public_manage.php?table=$table");
    exit;
}

// 撈資料
$data_result = $conn->query("SELECT * FROM $table");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>管理表格: <?= htmlspecialchars($table) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    table th, table td {
      min-width: 200px;
      text-align: center;
      vertical-align: middle;
    }
    table {
      table-layout: fixed;
    }
  </style>
</head>
<body class="container mt-4">
<h2 class="mb-3">管理表格：<?= htmlspecialchars($table) ?></h2>

<div class="table-responsive">
  <table class="table table-bordered align-middle text-center">
    <thead class="table-light">
      <tr>
        <?php foreach ($columns as $column): ?>
          <th><?= htmlspecialchars($column['Field']) ?></th>
        <?php endforeach; ?>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $data_result->fetch_assoc()): ?>
        <tr>
          <form method="POST">
            <?php foreach ($columns as $column): ?>
              <td>
                <?php
                  $field = $column['Field'];
                  $value = htmlspecialchars($row[$field]);

                  if ($column['Extra'] === 'auto_increment') {
                      echo $value;
                      echo "<input type='hidden' name='$field' value='$value'>";
                  } elseif (isset($foreign_keys[$field])) {
                      $parent_table = $foreign_keys[$field]['parent_table'];
                      $parent_column = $foreign_keys[$field]['parent_column'];
                      $res = $conn->query("SELECT $parent_column FROM $parent_table");

                      echo "<select name='$field' class='form-select'>";
                      while ($opt = $res->fetch_assoc()) {
                          $selected = ($opt[$parent_column] == $row[$field]) ? 'selected' : '';
                          echo "<option value='{$opt[$parent_column]}' $selected>{$opt[$parent_column]}</option>";
                      }
                      echo "</select>";
                  } else {
                      echo "<input type='text' name='$field' value='$value' class='form-control'>";
                  }

                  if (in_array($field, $primary_keys)) {
                      echo "<input type='hidden' name='$field' value='$value'>";
                  }
                ?>
              </td>
            <?php endforeach; ?>
            <td>
              <div class="d-grid gap-2">
                <button type="submit" name="update" class="btn btn-primary btn-sm">更新</button>
                <a href="public_manage.php?table=<?= $table ?>&delete=1<?php foreach ($primary_keys as $pk) echo "&$pk=" . urlencode($row[$pk]); ?>"
                   onclick="return confirm('確定要刪除？')" class="btn btn-danger btn-sm">刪除</a>
              </div>
            </td>
          </form>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a href="../ecommerce_admin.php" class="btn btn-secondary mb-3">返回</a>
</div>
</body>
</html>
