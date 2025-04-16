<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();
require_once '../../lib/log_helper.php';

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
$end_time = !empty($_POST['end_time']) ? $conn->real_escape_string($_POST['end_time']) : null;

// 先撈舊資料
$original = $conn->query("SELECT * FROM ad WHERE ad_id = '$ad_id'")->fetch_assoc();

$updates = [];
$log_changes = [];

// 判斷是否變更
if ($title !== $original['title']) {
    $updates[] = "title = '$title'";
    $log_changes[] = "標題：{$original['title']} → $title";
}
if ($image_path !== $original['image_path']) {
    $updates[] = "image_path = '$image_path'";
}
if ($link_url !== $original['link_url']) {
    $updates[] = "link_url = '$link_url'";
}
if ((int)$is_active !== (int)$original['is_active']) {
    $updates[] = "is_active = $is_active";
    $log_changes[] = "狀態：{$original['is_active']} → $is_active";
}

$original_end = $original['end_time'] ?? null;

// 統一格式（假設你輸入的是 '2025-04-17T00:31'）
if (!empty($end_time)) {
    $end_time_normalized = date('Y-m-d H:i', strtotime($end_time));
} else {
    $end_time_normalized = null;
}

$original_end_normalized = !empty($original_end) ? date('Y-m-d H:i', strtotime($original_end)) : null;

if ($end_time_normalized !== $original_end_normalized) {
    if ($end_time_normalized !== null) {
        $updates[] = "end_time = '$end_time_normalized'";
        $log_changes[] = "結束：{$original_end_normalized} → $end_time_normalized";
    } else {
        $updates[] = "end_time = NULL";
        $log_changes[] = "結束：{$original_end_normalized} → NULL";
    }
}

// 紀錄log : 變更才更新
if (!empty($updates)) {
    $update_sql = "UPDATE ad SET " . implode(', ', $updates) . " WHERE ad_id = '$ad_id'";
    $conn->query($update_sql);

    // Log
    $log_text = "更新廣告 [ $title ]";
    if (!empty($log_changes)) {
        $log_text .= "\n" . implode("\n", $log_changes);
    }

    log_admin_action($conn, $_SESSION['super_user_id'], '更新', 'ad', $ad_id, $log_text);
}
//紀錄log結束

header("Location: ../ecommerce_admin.php?mode=manage");
exit;
