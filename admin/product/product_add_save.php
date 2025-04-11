<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

$category_id = $_POST['category_id']; // 必須放在這前面
require_once '../../lib/get_next_product_id.php';
$product_id = getNextProductId($conn, $category_id); // ✅ 正確時機點

$brand_id = $_POST['brand_id'] ?? '';
$product_name = $_POST['product_name'];
$image_path = $_POST['image_path'];
$short_desc = $_POST['short_description'];
$detail_desc = $_POST['detail_description'];
$price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
$insert_time = date('Y-m-d H:i:s');

// 儲存資料
$sql = "INSERT INTO product (product_id, category_id, brand_id, product_name, image_path, short_description, detail_description, price, is_active, inserting_time) 
        VALUES ('$product_id', '$category_id', '$brand_id', '$product_name', '$image_path', '$short_desc', '$detail_desc', $price, $is_active, '$insert_time')";
$conn->query($sql);

// 清除舊 tag 關聯（如果有）
$conn->query("DELETE FROM product_tag WHERE product_id = '$product_id'");

// 處理 tag 勾選
if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
    foreach ($_POST['tags'] as $type_id => $tag_id) {
        $tag_id = $conn->real_escape_string($tag_id);
        $conn->query("INSERT INTO product_tag (product_id, tag_id) VALUES ('$product_id', '$tag_id')");
    }
}

// 紀錄 log
$category_name = $conn->query("SELECT name FROM category WHERE category_id = '$category_id'")->fetch_assoc()['name'] ?? '未知分類';
$brand_name = $conn->query("SELECT name FROM brand WHERE brand_id = '$brand_id'")->fetch_assoc()['name'] ?? '未知品牌';

$details = "新增 $category_name [$product_name, $brand_name]";

require_once '../../lib/log_helper.php';
log_admin_action($conn, $_SESSION['super_user_id'], '新增', 'product', $product_id, $details);
// 紀錄 log 結束

header("Location: ../ecommerce_admin.php");
exit;
