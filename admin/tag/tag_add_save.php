<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_POST['tag_type_id']) || !isset($_POST['name']) || !isset($_POST['tag_id'])) {
    exit('缺少必要欄位');
}

$tag_type_id = $_POST['tag_type_id'];
$tag_id = $_POST['tag_id']; 
$name = $conn->real_escape_string($_POST['name']);

// 插入資料
$sql = "INSERT INTO tag (tag_id, tag_type_id, name) VALUES ('$tag_id', '$tag_type_id', '$name')";
$conn->query($sql);

//紀錄log
require_once '../../lib/log_helper.php';

$type_stmt = $conn->prepare("SELECT name FROM tag_type WHERE tag_type_id = ?");
$type_stmt->bind_param("s", $tag_type_id);
$type_stmt->execute();
$type_result = $type_stmt->get_result();
$type_row = $type_result->fetch_assoc();
$tag_type_name = $type_row['name'] ?? '未知類型';

$details = "新增標籤細項：{$name}（標籤：{$tag_type_name}）";
log_admin_action($conn, $_SESSION['super_user_id'], '新增', 'tag', $tag_id, $details);
//紀錄log結束

// 回到管理頁
header("Location: ../ecommerce_admin.php?mode=manage");
exit;
