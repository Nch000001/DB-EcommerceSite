<?php //編輯頁面  以form做模板
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_SESSION['super_user_account'])) {
    header("Location: ../ecommerce_admin_login.php");
    exit;
}

if (!isset($_GET['id'])) exit('缺少商品 ID');
$product_id = $_GET['id'];
$product = $conn->query("SELECT * FROM product WHERE product_id = '$product_id'")->fetch_assoc();
if (!$product) exit('商品不存在');

// 查出該商品已勾選的 tag_type 對應 tag_id
$selected_tags_assoc = [];
$tag_info_result = $conn->query("
  SELECT pt.tag_id, t.tag_type_id 
  FROM product_tag pt
  JOIN tag t ON pt.tag_id = t.tag_id 
  WHERE pt.product_id = '$product_id'
");
while ($row = $tag_info_result->fetch_assoc()) {
  $selected_tags_assoc[$row['tag_type_id']] = $row['tag_id'];
}
$_POST['selected_tags'] = $selected_tags_assoc;
$_POST['category_id'] = $product['category_id']; // 為了載入該分類的 tag_type

include 'product_form.php';
