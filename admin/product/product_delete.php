<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

if (!isset($_POST['product_id'])) {
    exit('缺少商品 ID');
}

$product_id = $conn->real_escape_string($_POST['product_id']);

//紀錄log
require_once '../../lib/log_helper.php'; 
$info_sql = "
  SELECT p.product_name, c.name AS category_name, b.name AS brand_name
  FROM product p
  JOIN category c ON p.category_id = c.category_id
  JOIN brand b ON p.brand_id = b.brand_id
  WHERE p.product_id = '$product_id'
";
$info = $conn->query($info_sql)->fetch_assoc();

$product_name = $info['product_name'] ?? '未知商品';
$category_name = $info['category_name'] ?? '未知分類';
$brand_name = $info['brand_name'] ?? '未知品牌';

$details = "刪除 $category_name [$product_name, $brand_name]";

// 刪除標籤
$conn->query("DELETE FROM product_tag WHERE product_id = '$product_id'");

// 刪除商品
$conn->query("DELETE FROM product WHERE product_id = '$product_id'");

log_admin_action($conn, $_SESSION['super_user_id'], '刪除', 'product', $product_id, $details);
//紀錄log結束

header("Location: product_manage.php?mode=manage");
exit;