<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();


if (!isset($_SESSION['super_user_account']) || !isset($_GET['table'])) {
    header("Location: ecommerce_admin_login.php");
    exit;
}

$table = $conn->real_escape_string($_GET['table']);

$columns = [];
$result = $conn->query("DESCRIBE $table");
while ($row = $result->fetch_assoc()) {
    $columns[] = $row;
}

$foreign_keys = [];
$fk_result = $conn->query("SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND REFERENCED_TABLE_NAME IS NOT NULL");
while ($fk = $fk_result->fetch_assoc()) {
    $foreign_keys[$fk['COLUMN_NAME']] = [
        'parent_table' => $fk['REFERENCED_TABLE_NAME'],
        'parent_column' => $fk['REFERENCED_COLUMN_NAME'],
    ];
}

// 新增
if (isset($_POST['insert'])) {
    $fields = [];
    $values = [];
    foreach ($columns as $column) {
        if ($column['Extra'] === 'auto_increment' || $column['Default'] === 'current_timestamp()') continue;
        $field = $column['Field'];
        $value = $conn->real_escape_string($_POST[$field]);

        if (strpos($column['Type'], 'datetime') !== false && empty($value)) {
            date_default_timezone_set('Asia/Taipei');
            $value = date('Y-m-d H:i:s');
        }
        $fields[] = $field;
        $values[] = "'$value'";
    }
    $fields_str = implode(',', $fields);
    $values_str = implode(',', $values);
    $conn->query("INSERT INTO $table ($fields_str) VALUES ($values_str)");
    header("Location: ecommerce_add.php?table=$table");
    exit;
}
?>

<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>新增 <?= htmlspecialchars($table) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-4">
<h2>新增 <?= htmlspecialchars($table) ?> 資料</h2>
<form method="POST" class="row g-3 mb-4">
<?php foreach ($columns as $column): if ($column['Extra'] !== 'auto_increment' && $column['Default'] !== 'current_timestamp()'): ?>
  <div class="col-md-4">
    <label class="form-label"><?= $column['Field'] ?></label>
    <?php if (isset($foreign_keys[$column['Field']])): 
        $pt = $foreign_keys[$column['Field']]['parent_table'];
        $pc = $foreign_keys[$column['Field']]['parent_column'];
        $res = $conn->query("SELECT $pc FROM $pt"); ?>
      <select name="<?= $column['Field'] ?>" class="form-select" required>
        <option value="">請選擇</option>
        <?php while ($r = $res->fetch_assoc()): ?>
          <option value="<?= $r[$pc] ?>"><?= $r[$pc] ?></option>
        <?php endwhile; ?>
      </select>
    <?php elseif (strpos($column['Type'], 'datetime') !== false): ?>
      <input type="datetime-local" name="<?= $column['Field'] ?>" class="form-control">
    <?php elseif (strpos($column['Type'], 'date') !== false): ?>
      <input type="date" name="<?= $column['Field'] ?>" class="form-control">
    <?php else: ?>
      <input type="text" name="<?= $column['Field'] ?>" class="form-control" required>
    <?php endif; ?>
  </div>
<?php endif; endforeach; ?>
  <div class="col-12">
    <button type="submit" name="insert" class="btn btn-success">新增</button>
    <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
  </div>
</form>

</body></html>