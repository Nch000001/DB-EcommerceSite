<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_GET['tag_type_id'])) exit;

$tag_type_id = $_GET['tag_type_id'];
$prefix = strtoupper(substr($tag_type_id, 0, 2)); 

// 找出目前該 prefix 已經有幾筆
$res = $conn->query("SELECT tag_id FROM tag WHERE tag_id LIKE '$prefix%' ORDER BY tag_id DESC LIMIT 1");
if ($res && $res->num_rows > 0) {
    $last_id = $res->fetch_assoc()['tag_id'];
    $num = intval(substr($last_id, 2)) + 1;
} else {
    $num = 1;
}

$next_id = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
echo $next_id;

