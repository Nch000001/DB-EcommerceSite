<?php
// lazy_add_category.php
require_once '../../lib/db.php';
require_once '../../lib/log_helper.php';
session_start();
header('Content-Type: application/json');

$conn = getDBConnection();
$super_user_id = $_SESSION['super_user_id'] ?? 'unknown';

$category_id = strtoupper(trim($_POST['category_id'] ?? ''));
$name = trim($_POST['name'] ?? '');



if (!preg_match('/^[A-Z]{5}$/', $category_id)) {
  echo json_encode(['success' => false, 'error' => '分類 ID 格式錯誤']);
  exit;
}

if ($name === '') {
  echo json_encode(['success' => false, 'error' => '請輸入分類名稱']);
  exit;
}

$stmt = $conn->prepare("SELECT category_id FROM category WHERE category_id = ? OR name = ?");
$stmt->bind_param("ss", $category_id, $name);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  echo json_encode(['success' => false, 'error' => '分類 ID 或名稱已存在']);
  exit;
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO category (category_id, name) VALUES (?, ?)");
$stmt->bind_param("ss", $category_id, $name);
if ($stmt->execute()) {
  echo json_encode(['success' => true, 'category_id' => $category_id, 'name' => $name]);
  // 紀錄 log
  $details = "新增分類 [ $name ]";
  log_admin_action($conn, $super_user_id, '新增', 'category', $category_id, $details);
  // 紀錄 log 結束
} else {
  echo json_encode(['success' => false, 'error' => '資料庫錯誤']);
}

exit;

