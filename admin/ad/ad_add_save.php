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

$ad_id = $conn->real_escape_string($_POST['ad_id']);
$title = $conn->real_escape_string($_POST['title']);
$image_path = $conn->real_escape_string($_POST['image_path']);
$link_url = $conn->real_escape_string($_POST['link_url']);
$is_active = intval($_POST['is_active']);
$start_time = $conn->real_escape_string($_POST['start_time']);
$end_time = !empty($_POST['end_time']) ? "'" . $conn->real_escape_string($_POST['end_time']) . "'" : "NULL";

$sql = "INSERT INTO ad (title, image_path, link_url, is_active, start_time, end_time)
        VALUES ('$title', '$image_path', '$link_url', $is_active, '$start_time', $end_time)";
$conn->query($sql);


//紀錄log
require_once '../../lib/log_helper.php';

$detail_text = "新增廣告 [ $title ] 狀態 : $is_active , 開始時間 : $start_time , 結束時間 : $end_time";

log_admin_action($conn, $_SESSION['super_user_id'], '新增', 'ad', $ad_id, $detail_text);
//紀錄log結束

header("Location: ../ecommerce_admin.php?mode=manage");
exit;
