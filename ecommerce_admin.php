<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
global $conn;


if(!isset($_SESSION['super_user_account'])) {
    header("Location: ecommerce_admin_login.php");
    exit;
}
  
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
  $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>後台管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">資料庫表格清單</h2>
    <a href="logout.php" class="btn btn-danger">登出</a>
</div>
<ul class="list-group">
  <?php foreach ($tables as $table): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <?= $table ?>
      <a href="ecommerce_manage.php?table=<?= $table ?>" class="btn btn-primary btn-sm">管理</a>
    </li>
  <?php endforeach; ?>
</ul>

</body>
</html>