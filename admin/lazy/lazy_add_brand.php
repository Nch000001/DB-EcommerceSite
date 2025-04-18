<?php
// lazy_add_brand.php
require_once '../../lib/db.php';
require_once '../../lib/log_helper.php';
session_start();
header('Content-Type: application/json');

$conn = getDBConnection();
$super_user_id = $_SESSION['super_user_id'] ?? 'unknown';

$name = trim($_POST['name'] ?? '');
if ($name === '') {
  echo json_encode(['success' => false, 'error' => '請輸入品牌名稱']);
  exit;
}

// 檢查品牌是否已存在
$stmt = $conn->prepare("SELECT brand_id FROM brand WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  echo json_encode(['success' => false, 'error' => '品牌名稱已存在']);
  exit;
}
$stmt->close();

// 插入品牌
$stmt = $conn->prepare("INSERT INTO brand (name) VALUES (?)");
$stmt->bind_param("s", $name);
$success = $stmt->execute();

if ($success) {
  $brand_id = $conn->insert_id;
  echo json_encode(['success' => true, 'brand_id' => (int)$brand_id, 'name' => $name]);

  // 紀錄 log
  $details = "新增品牌 [ $name ]";
  log_admin_action($conn, $super_user_id, '新增', 'brand', $brand_id, $details);
  // Log end
} else {
  echo json_encode(['success' => false, 'error' => '資料庫錯誤']);
}
exit;
