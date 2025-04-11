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

$stmt = $conn->prepare("SELECT brand_id FROM brand WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  echo json_encode(['success' => false, 'error' => '品牌名稱已存在']);
  exit;
}
$stmt->close();

$prefix = strtoupper(substr(preg_replace('/\\s+/', '', $name), 0, 3));
$res = $conn->query("SELECT brand_id FROM brand WHERE brand_id LIKE '$prefix%' ORDER BY brand_id DESC LIMIT 1");
$seq = $res->num_rows > 0 ? str_pad((intval(substr($res->fetch_assoc()['brand_id'], strlen($prefix))) + 1), 2, '0', STR_PAD_LEFT) : '01';
$brand_id = $prefix . $seq;

$stmt = $conn->prepare("INSERT INTO brand (brand_id, name) VALUES (?, ?)");
$stmt->bind_param("ss", $brand_id, $name);
if ($stmt->execute()) {
  echo json_encode(['success' => true, 'brand_id' => $brand_id, 'name' => $name]);

  // 紀錄 log
  $details = "新增品牌 [ $name ]";
  log_admin_action($conn, $super_user_id, '新增', 'brand', $brand_id, $details);
  // 紀錄 log 結束
} else {
  echo json_encode(['success' => false, 'error' => '資料庫錯誤']);
}
exit;
