<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);

require_once '../../lib/db.php';
$conn = getDBConnection();
require_once '../../lib/log_helper.php';

if (!isset($_POST['tag_type_id'], $_POST['name'], $_POST['category_ids']) || !is_array($_POST['category_ids'])) {
    exit('資料不完整');
}

$tag_type_id = $_POST['tag_type_id'];
$name = trim($_POST['name']);
$categories = $_POST['category_ids']; 

if (empty($name) || empty($categories)) {
    exit('請填寫名稱並至少選擇一個分類');
}

// 查舊的分類名稱
$old_result = $conn->query("
  SELECT c.name 
  FROM tag_category tc 
  JOIN category c ON tc.category_id = c.category_id 
  WHERE tc.tag_type_id = '$tag_type_id'
");

$old_names = [];
while ($row = $old_result->fetch_assoc()) {
    $old_names[] = $row['name'];
}

// 查新的分類名稱
$escaped_ids = array_map(function($id) use ($conn) {
    return "'" . $conn->real_escape_string($id) . "'";
}, $categories);
$id_list = implode(',', $escaped_ids);

$new_names = [];
if ($id_list) {
    $new_result = $conn->query("SELECT name FROM category WHERE category_id IN ($id_list)");
    while ($row = $new_result->fetch_assoc()) {
        $new_names[] = $row['name'];
    }
}

// 更新名稱
$update_sql = "UPDATE tag_type SET name = ? WHERE tag_type_id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("ss", $name, $tag_type_id);
$stmt->execute();

// 更新 category 關聯：先刪後加
$conn->query("DELETE FROM tag_category WHERE tag_type_id = '$tag_type_id'");
foreach ($categories as $cat_id) {
    $cat_id = $conn->real_escape_string($cat_id);
    $conn->query("INSERT INTO tag_category (tag_type_id, category_id) VALUES ('$tag_type_id', '$cat_id')");
}

// 組合詳細紀錄
$old_str = implode(', ', $old_names);
$new_str = implode(', ', $new_names);
$details = "更新標籤：$name\n分類變更：[$old_str] → [$new_str]";

// 寫入 log
log_admin_action($conn, $_SESSION['super_user_id'], '更新', 'tag_type', $tag_type_id, $details);

header("Location: tag_type_manage.php");
exit;
