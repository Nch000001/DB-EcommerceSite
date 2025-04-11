<?php
// lazy_add_tag_type.php
require_once '../../lib/db.php';
require_once '../../lib/log_helper.php';
session_start();
$conn = getDBConnection();

header('Content-Type: application/json');

$super_user_id = $_SESSION['super_user_id'] ?? 'unknown';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => '無效的請求']);
  exit;
}

$tag_type_name = trim($_POST['tag_type_name'] ?? '');
$category_ids = $_POST['category_ids'] ?? [];

if ($tag_type_name === '' || empty($category_ids)) {
  echo json_encode(['success' => false, 'message' => '請填寫名稱並至少選一個分類']);
  exit;
}

// 產生 tag_type_id：單分類取分類 id 前 2 碼，多分類用 "US" 開頭
if (count($category_ids) === 1) {
  $prefix = strtoupper(substr($category_ids[0], 0, 2));
} else {
  $prefix = 'US';
}
$like = $conn->real_escape_string($prefix . '%');

$res = $conn->query("SELECT tag_type_id FROM tag_type WHERE tag_type_id LIKE '$like' ORDER BY tag_type_id DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
  $last_num = (int)substr($row['tag_type_id'], 2);
  $new_num = str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
} else {
  $new_num = '001';
}
$tag_type_id = $prefix . $new_num;

// 插入 tag_type
$stmt = $conn->prepare("INSERT INTO tag_type (tag_type_id, name) VALUES (?, ?)");
$stmt->bind_param("ss", $tag_type_id, $tag_type_name);
$success = $stmt->execute();

if (!$success) {
  echo json_encode(['success' => false, 'message' => '新增失敗：' . $conn->error]);
  exit;
}

// 插入 tag_category 關聯
$stmt_cat = $conn->prepare("INSERT INTO tag_category (tag_type_id, category_id) VALUES (?, ?)");
foreach ($category_ids as $cat_id) {
  $stmt_cat->bind_param("ss", $tag_type_id, $cat_id);
  $stmt_cat->execute();
}

//紀錄log
$cat_names = [];
foreach ($category_ids as $cat_id) {
    $cat_id_safe = $conn->real_escape_string($cat_id);
    $res = $conn->query("SELECT name FROM category WHERE category_id = '$cat_id_safe'");
    if ($row = $res->fetch_assoc()) {
        $cat_names[] = $row['name'];
    }
}

$details = '新增標籤類型：' . $tag_type_name . '（分類：' . implode(', ', $cat_names) . '）';
log_admin_action(
    $conn,
    $super_user_id,
    '新增',
    'tag_type',
    $tag_type_id,
    $details
);
//log結束

echo json_encode([
    'success' => true,
    'tag_type_id' => $tag_type_id,
    'name' => $tag_type_name
]);

exit;