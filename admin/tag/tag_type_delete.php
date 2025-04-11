
<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();
require_once '../../lib/log_helper.php';

if (!isset($_GET['id'])) {
    exit('缺少 tag_type_id');
}

$tag_type_id = $_GET['id'];

//紀錄log
$tag = $conn->query("SELECT name FROM tag_type WHERE tag_type_id = '$tag_type_id'")->fetch_assoc();
$name = $tag['name'] ?? '未知';

$cat_names = [];
$res = $conn->query("SELECT c.name FROM tag_category tc JOIN category c ON tc.category_id = c.category_id WHERE tc.tag_type_id = '$tag_type_id'");
while ($row = $res->fetch_assoc()) {
    $cat_names[] = $row['name'];
}

require_once '../../lib/log_helper.php';

log_admin_action(
    $conn,
    $_SESSION['super_user_id'],
    '刪除',
    'tag_type',
    $tag_type_id,
    '刪除標籤：' . $name . '（分類：' . implode(', ', $cat_names) . '）'
);
//紀錄log結束

// 刪除相關資料
$conn->query("DELETE FROM tag WHERE tag_type_id = '$tag_type_id'");
$conn->query("DELETE FROM tag_category WHERE tag_type_id = '$tag_type_id'");
$conn->query("DELETE FROM tag_type WHERE tag_type_id = '$tag_type_id'");

header("Location: tag_type_manage.php");
exit;

?>
