<?php
session_start();
require_once '../../lib/db.php';
$conn = getDBConnection();

$product_id = $_POST['product_id'] ?? '';
if (!$product_id) exit('缺少商品 ID');

if (isset($_POST['delete'])) {
  $conn->query("DELETE FROM product_tag WHERE product_id = '$product_id'");
  $conn->query("DELETE FROM product WHERE product_id = '$product_id'");
  header("Location: product_manage.php?deleted=1");
  exit;
}

$product_name = $_POST['product_name'];
$image_path = $_POST['image_path'];
$short_desc = $_POST['short_description'];
$detail_desc = $_POST['detail_description'];
$price = (int)$_POST['price'];
$is_active = (int)$_POST['is_active'];

$conn->query("
  UPDATE product SET 
    product_name = '$product_name',
    image_path = '$image_path',
    short_description = '$short_desc',
    detail_description = '$detail_desc',
    price = $price,
    is_active = $is_active
  WHERE product_id = '$product_id'
");

// 標籤更新
$conn->query("DELETE FROM product_tag WHERE product_id = '$product_id'");
if (isset($_POST['tags']) && is_array($_POST['tags'])) {
  foreach ($_POST['tags'] as $type_id => $tag_id) {
    $tag_id = $conn->real_escape_string($tag_id);
    if ($tag_id) {
      $conn->query("INSERT INTO product_tag (product_id, tag_id) VALUES ('$product_id', '$tag_id')");
    }
  }
}

header("Location: product_manage.php?update=success");
exit;
