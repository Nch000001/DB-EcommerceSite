<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

if (!isset($_POST['ad_id'])) {
    exit('缺少廣告 ID');
}

$ad_id = $conn->real_escape_string($_POST['ad_id']);

// 撈資料供 log 使用
$info_sql = "SELECT title, is_active, start_time, end_time FROM ad WHERE ad_id = '$ad_id'";
$info = $conn->query($info_sql)->fetch_assoc();

$title = $info['title'] ?? '未知';
$is_active = $info['is_active'] ?? '未知';
$start_time = $info['start_time'] ?? '未知';
$end_time = $info['end_time'] ?? '未知';

$detail_text = "刪除廣告 [ $title ] 狀態 : $is_active , 開始時間 : $start_time , 結束時間 : $end_time";

// 刪除
$conn->query("DELETE FROM ad WHERE ad_id = '$ad_id'");

// 紀錄 log
require_once '../../lib/log_helper.php';
log_admin_action($conn, $_SESSION['super_user_id'], '刪除', 'ad', $ad_id, $detail_text);

header("Location: ../ecommerce_admin.php?mode=manage");
exit;
