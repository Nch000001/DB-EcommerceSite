<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

date_default_timezone_set('Asia/Taipei');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>新增廣告</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h2>新增廣告</h2>
<form method="POST" action="ad_add_save.php">

  <div class="mb-3">
    <label class="form-label">標題</label>
    <input type="text" name="title" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">圖片檔名</label>
    <input type="text" name="image_path" class="form-control" value="ad_img/" required>
  </div>

  <div class="mb-3">
    <label class="form-label">連結網址</label>
    <input type="url" name="link_url" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">是否啟用</label>
    <select name="is_active" class="form-select">
      <option value="1">啟用</option>
      <option value="0">停用</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">開始時間</label>
    <input type="datetime-local" name="start_time" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">結束時間</label>
    <input type="datetime-local" name="end_time" class="form-control">
  </div>

  <button type="submit" class="btn btn-success">送出</button>
  <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
</form>
</body>
</html>
