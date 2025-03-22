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

// 取得欄位結構
$columns = [];
$result = $conn->query("DESCRIBE $table");
while ($row = $result->fetch_assoc()) {
    $columns[] = $row;
}

// 查找 Foreign Key
$foreign_keys = [];
$fk_result = $conn->query("
    SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = '$table'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");

while ($fk = $fk_result->fetch_assoc()) {
    $foreign_keys[$fk['COLUMN_NAME']] = [
        'parent_table' => $fk['REFERENCED_TABLE_NAME'],
        'parent_column' => $fk['REFERENCED_COLUMN_NAME'],
    ];
}

// 新增資料
if (isset($_POST['insert'])) {
    $fields = [];
    $values = [];
    foreach ($columns as $column) {
        if ($column['Extra'] !== 'auto_increment' && $column['Default'] !== 'current_timestamp()') {
            $field = $column['Field'];
            $fields[] = $field;
            $values[] = "'" . $conn->real_escape_string($_POST[$field]) . "'";
        }
    }
    $fields_str = implode(",", $fields);
    $values_str = implode(",", $values);
    $conn->query("INSERT INTO $table ($fields_str) VALUES ($values_str)");
    header("Location: test.php?table=$table");
    exit;
}

// 更新資料
if (isset($_POST['update_id'])) {
    $update_id = $conn->real_escape_string($_POST['update_id']);
    $updates = [];
    foreach ($columns as $column) {
        if ($column['Extra'] !== 'auto_increment' && $column['Default'] !== 'current_timestamp()') {
            $field = $column['Field'];
            $value = $conn->real_escape_string($_POST[$field]);
            $updates[] = "$field='$value'";
        }
    }
    $updates_str = implode(",", $updates);
    $primary_key = $columns[0]['Field']; // 假設第一欄是 PK
    $conn->query("UPDATE $table SET $updates_str WHERE $primary_key = '$update_id'");
    header("Location: test.php?table=$table");
    exit;
}

// 刪除資料
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $primary_key = $columns[0]['Field'];
    $conn->query("DELETE FROM $table WHERE $primary_key = '$delete_id'");
    header("Location: test.php?table=$table");
    exit;
}

// 查詢所有資料
$data_result = $conn->query("SELECT * FROM $table");

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>管理表格: <?= htmlspecialchars($table) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .logout-btn {
      position: absolute;
      top: 10px;
      right: 20px;
    }
  </style>
</head>
<body class="container mt-4 position-relative">

<h2>管理表格: <?= $table ?></h2>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
    <a href="ecommerce_admin.php" class="btn btn-secondary mb-3">回到表格清單</a>
    <a href="logout.php" class="btn btn-danger mb-3">登出</a>
</div>

<!-- 新增資料 -->
<h4>新增資料</h4>
<form method="POST" class="row g-3 mb-4">
  <?php foreach ($columns as $column): ?>
    <?php 
        // echo("$column");
        // echo $column['Default'];
        if (
            $column['Extra'] !== 'auto_increment'
            // && !(stripos($field, 'time') !== false && 
            // stripos($column['Default'], 'CURRENT_TIMESTAMP') !== false)
            && $column['Default'] !== 'current_timestamp()'
        ):      
    ?>
      <div class="col-md-4">
        <label class="form-label"><?= $column['Field'] ?></label>

        <?php if (isset($foreign_keys[$column['Field']])): ?>
          <!-- Foreign Key 下拉 -->
          <select name="<?= $column['Field'] ?>" class="form-select" required>
            <option value="">請選擇</option>
            <?php
            $parent_table = $foreign_keys[$column['Field']]['parent_table'];
            $parent_column = $foreign_keys[$column['Field']]['parent_column'];
            $parent_result = $conn->query("SELECT $parent_column FROM $parent_table");
            while ($parent_row = $parent_result->fetch_assoc()):
            ?>
              <option value="<?= $parent_row[$parent_column] ?>"><?= $parent_row[$parent_column] ?></option>
            <?php endwhile; ?>
          </select>
        <?php else: ?>
          <input type="text" name="<?= $column['Field'] ?>" class="form-control" required>
        <?php endif; ?>

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
    <?php while ($row = $data_result->fetch_assoc()): ?>
      <tr>
        <form method="POST">
          <?php foreach ($columns as $column): ?>
            <td>
              <?php if ($column['Extra'] === 'auto_increment'): ?>
                <?= htmlspecialchars($row[$column['Field']]) ?>
                <input type="hidden" name="update_id" value="<?= $row[$column['Field']] ?>">
              <?php else: ?>
                <?php if (isset($foreign_keys[$column['Field']])): ?>
                  <!-- Foreign Key 下拉 -->
                  <select name="<?= $column['Field'] ?>" class="form-select" required>
                    <?php
                    $parent_table = $foreign_keys[$column['Field']]['parent_table'];
                    $parent_column = $foreign_keys[$column['Field']]['parent_column'];
                    $parent_result = $conn->query("SELECT $parent_column FROM $parent_table");
                    while ($parent_row = $parent_result->fetch_assoc()):
                    ?>
                      <option value="<?= $parent_row[$parent_column] ?>" <?= ($parent_row[$parent_column] == $row[$column['Field']]) ? 'selected' : '' ?>>
                        <?= $parent_row[$parent_column] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                <?php else: ?>
                  <input type="text" name="<?= $column['Field'] ?>" value="<?= htmlspecialchars($row[$column['Field']]) ?>" class="form-control">
                <?php endif; ?>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
          <td>
            <button type="submit" class="btn btn-primary btn-sm">更新</button>
            <a href="test.php?table=<?= $table ?>&delete_id=<?= $row[$columns[0]['Field']] ?>" class="btn btn-danger btn-sm" onclick="return confirm('確定刪除？')">刪除</a>
          </td>
        </form>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>