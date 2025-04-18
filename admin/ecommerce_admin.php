<?php
require_once '../lib/auth_helper.php';
requireLevel(1);

require_once '../lib/db.php';

$conn = getDBConnection();

$sql = "SELECT admin_id, level FROM super_admin WHERE admin_account = '" . $_SESSION['super_user_account'] . "'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$_SESSION['user_level'] = $row['level'];
$_SESSION['super_user_id'] = $row['admin_id'];

$mode = isset($_GET['mode']) && $_GET['mode'] === 'manage' ? 'manage' : 'add';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>後台管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .user-info {
      background-color: #f8f9fa;
      padding: 8px 12px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      font-size: 18px;
      margin-right: 10%;
      margin-left: 20px;
    }
  </style>
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold">資料庫表格清單</h4>
  <div class="user-info">
    使用者: <?= htmlspecialchars($_SESSION['super_user_id']) ?>　
    權限等級: <?= htmlspecialchars($_SESSION['user_level']) ?>
  </div>
  <a href="../logout.php" class="btn btn-danger">登出</a>
</div>

<div class="text-center mb-4">
  <a href="?mode=add" class="btn btn-<?= $mode === 'add' ? 'success' : 'outline-success' ?>">新增模式</a>
  <a href="?mode=manage" class="btn btn-<?= $mode === 'manage' ? 'primary' : 'outline-primary' ?>">管理模式</a>
</div>

<div class="d-flex justify-content-center flex-wrap gap-3 mt-3">
  <a href="<?= $mode === 'add' ? 'lazy/lazy_form.php' : 'product/product_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">產品</a>
  <a href="<?= $mode === 'add' ? 'ad/ad_form.php' : 'ad/ad_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">廣告</a>
  <a href="ecommerce_manage.php?table=user" class="btn btn-outline-dark px-4 py-2">用戶</a>
  <a href="<?= $mode === 'add' ? 'tag/tag_type_add.php' : 'tag/tag_type_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">標籤</a>
  <a href="<?= $mode === 'add' ? 'tag/tag_add.php' : 'tag/tag_manage.php' ?>" class="btn btn-outline-dark px-4 py-2">標籤細項</a>
  <a href="<?= $mode === 'add' ? 'public/public_add.php?table=category' : 'public/public_manage.php?table=category' ?>" class="btn btn-outline-dark px-4 py-2">分類</a>
  <a href="<?= $mode === 'add' ? 'public/public_add.php?table=brand' : 'public/public_manage.php?table=brand' ?>" class="btn btn-outline-dark px-4 py-2">品牌</a>
  <a href="<?= $mode === 'add' ? 'public/upload_image.php' : 'public/image_manager.php' ?>"  class="btn btn-outline-dark px-4 py-2">圖片</a>
  <?php if ($_SESSION['user_level'] == 3): ?>
  <a href="admin_log.php" class="btn btn-outline-dark px-4 py-2">Log</a>
  <?php endif; ?>
</div>

</body>
</html>
