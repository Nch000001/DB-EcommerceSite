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

// 找圖片資料
$res = $conn->query("SELECT image_path, detail_description, product_name FROM product WHERE product_id = '$product_id'");
if ($res && $res->num_rows > 0) {
  $row = $res->fetch_assoc();
  $image_path = $row['image_path'];
  $detail_description = $row['detail_description'];
  $product_name = $row['product_name'];

  // 刪除主圖
  if ($image_path && file_exists("../../" . $image_path)) {
    unlink("../../" . $image_path);
  }

  // 刪除描述中的圖
  $lines = explode("\n", $detail_description);
  foreach ($lines as $line) {
    $trimmed = trim($line);
    if (strpos($trimmed, 'img/') === 0 && file_exists("../../" . $trimmed)) {
      unlink("../../" . $trimmed);
    }
  }
}


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