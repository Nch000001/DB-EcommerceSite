<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

// 查詢條件
$title = $_GET['title'] ?? '';
$is_active = isset($_GET['is_active']) ? intval($_GET['is_active']) : 1;

// 查詢廣告資料（帶條件）
$sql = "SELECT * FROM ad WHERE title LIKE ? AND is_active = ? ORDER BY ad_id DESC";
$stmt = $conn->prepare($sql);
$search_title = '%' . $title . '%';
$stmt->bind_param("si", $search_title, $is_active);
$stmt->execute();
$ads = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>廣告管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .ad-card {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 30px;
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 12px;
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .ad-card img {
      width: 300px;
      height: auto;
      border-radius: 10px;
      object-fit: contain;
    }
    .ad-form {
      flex-grow: 1;
    }
    .ad-form .form-group label {
      font-weight: bold;
    }
    .ad-form .form-control,
    .ad-form .form-select {
      font-size: 14px;
    }
    .btn-group {
      margin-top: 15px;
    }
  </style>
</head>
<body class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0">目前廣告列表：</h4>
    <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
  </div>

  <form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-md-4">
      <label for="title" class="form-label">廣告標題</label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="is_active" class="form-label">啟用狀態</label>
      <select name="is_active" id="is_active" class="form-select">
        <option value="1" <?= $is_active === 1 ? 'selected' : '' ?>>啟用</option>
        <option value="0" <?= $is_active === 0 ? 'selected' : '' ?>>停用</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">搜尋</button>
    </div>
  </form>

  <?php while ($row = $ads->fetch_assoc()): ?>
    <div class="ad-card">
      <img src="../../<?= htmlspecialchars($row['image_path']) ?>" alt="廣告圖片">

      <div class="ad-form w-100">
        <div class="row g-3">
          <div class="col-md-4">
            <label>標題</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($row['title']) ?>" form="update_form_<?= $row['ad_id'] ?>">
          </div>
          <div class="col-md-4">
            <label>圖片檔名</label>
            <input type="text" class="form-control" name="image_path" value="<?= htmlspecialchars($row['image_path']) ?>" form="update_form_<?= $row['ad_id'] ?>">
          </div>
          <div class="col-md-4">
            <label>連結網址</label>
            <input type="text" class="form-control" name="link_url" value="<?= htmlspecialchars($row['link_url']) ?>" form="update_form_<?= $row['ad_id'] ?>">
          </div>

          <div class="col-md-4">
            <label>是否啟用</label>
            <select name="is_active" class="form-select" form="update_form_<?= $row['ad_id'] ?>">
              <option value="1" <?= $row['is_active'] ? 'selected' : '' ?>>啟用</option>
              <option value="0" <?= !$row['is_active'] ? 'selected' : '' ?>>停用</option>
            </select>
          </div>
          <div class="col-md-4">
            <label>開始時間</label>
            <input type="text" class="form-control" value="<?= $row['start_time'] ?>" readonly>
          </div>
          <div class="col-md-4">
            <label>結束時間</label>
            <input type="datetime-local" class="form-control"
              name="end_time"
              value="<?= $row['end_time'] ? date('Y-m-d\TH:i', strtotime($row['end_time'])) : '' ?>"
              form="update_form_<?= $row['ad_id'] ?>">
          </div>
        </div>

        <!-- 更新表單 -->
        <form method="POST" action="ad_update.php" id="update_form_<?= $row['ad_id'] ?>" class="mt-3 d-inline-block">
          <input type="hidden" name="ad_id" value="<?= $row['ad_id'] ?>">
          <button type="submit" class="btn btn-primary">更新</button>
        </form>

        <!-- 刪除表單 -->
        <form method="POST" action="ad_delete.php" class="mt-3 d-inline-block" onsubmit="return confirm('確定要刪除此廣告嗎？')">
          <input type="hidden" name="ad_id" value="<?= $row['ad_id'] ?>">
          <button type="submit" class="btn btn-danger">刪除</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>

</body>
</html>
