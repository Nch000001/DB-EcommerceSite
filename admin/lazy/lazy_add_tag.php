<?php
require_once '../../lib/db.php';
require_once '../../lib/log_helper.php';
session_start();

header('Content-Type: application/json');

$conn = getDBConnection();

$super_user_id = $_SESSION['super_user_id'] ?? 'unknown';

$tag_type_id = $_POST['tag_type_id'] ?? '';
$tag_name = trim($_POST['tag_name'] ?? '');

if (!$tag_type_id || !$tag_name) {
    echo json_encode(['success' => false, 'error' => '請填寫完整資訊']);
    exit;
}

// 檢查是否已有相同名稱
$stmt = $conn->prepare("SELECT tag_id FROM tag WHERE tag_type_id = ? AND name = ?");
$stmt->bind_param("ss", $tag_type_id, $tag_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => '該標籤已存在']);
    exit;
}
$stmt->close();

// 自動產生 tag_id：取 tag_type_id 前2碼 + 3位流水號
$prefix = substr($tag_type_id, 0, 2);
$result = $conn->query("SELECT tag_id FROM tag WHERE tag_id LIKE '$prefix%' ORDER BY tag_id DESC LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $last = intval(substr($row['tag_id'], 2)) + 1;
} else {
    $last = 1;
}
$tag_id = $prefix . str_pad($last, 3, '0', STR_PAD_LEFT);

// 寫入資料
$stmt = $conn->prepare("INSERT INTO tag (tag_id, tag_type_id, name) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $tag_id, $tag_type_id, $tag_name);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'tag_id' => $tag_id,
        'tag_name' => $tag_name,
        'tag_type_id' => $tag_type_id
    ]);
    //紀錄log
    $type_stmt = $conn->prepare("SELECT name FROM tag_type WHERE tag_type_id = ?");
    $type_stmt->bind_param("s", $tag_type_id);
    $type_stmt->execute();
    $type_result = $type_stmt->get_result();
    $type_row = $type_result->fetch_assoc();
    $tag_type_name = $type_row['name'] ?? '未知類型';

    $details = "新增標籤細項：{$tag_name}（標籤類型：{$tag_type_name}）";
    log_admin_action($conn, $_SESSION['super_user_id'], '新增', 'tag', $tag_id, $details);
    //紀錄log結束
} else {
    echo json_encode(['success' => false, 'error' => '資料庫寫入失敗']);
}
exit;
