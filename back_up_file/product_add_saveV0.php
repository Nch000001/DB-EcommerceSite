<?php
session_start();
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_POST['product_id'])) exit('錯誤');

$product_id = $_POST['product_id'];
$category_id = $_POST['category_id'];
$brand_id = $_POST['brand_id'];
$product_name = $_POST['product_name'];
$image_path = $_POST['image_path'];
$short_desc = $_POST['short_description'];
$detail_desc = $_POST['detail_description'];
$price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
$insert_time = date('Y-m-d H:i:s');

$sql = "INSERT INTO product (product_id, category_id, brand_id, product_name, image_path, short_description, detail_description, price, is_active, inserting_time) 
VALUES ('$product_id', '$category_id', '$brand_id', '$product_name', '$image_path', '$short_desc', '$detail_desc', $price, $is_active, '$insert_time')";

$conn->query($sql);

// 標籤處理（每個 tag_type 只能有一個 tag）
if (isset($_POST['tags']) && is_array($_POST['tags'])) {
  foreach ($_POST['tags'] as $type_id => $tag_id) {
    $tag_id = $conn->real_escape_string($tag_id);
    $conn->query("INSERT INTO product_tag (product_id, tag_id) VALUES ('$product_id', '$tag_id')");
  }
}

header("Location: ../ecommerce_manage.php?table=product");
exit;