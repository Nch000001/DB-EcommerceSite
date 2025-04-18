<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
require_once '../../lib/log_helper.php';

// session_start();

$conn = getDBConnection();

$product_id = $_POST['product_id'];
if (!$product_id) exit('缺少商品 ID');

// 取得舊資料
$old_info_sql = "
  SELECT p.*, c.name AS category_name, b.name AS brand_name
  FROM product p
  JOIN category c ON p.category_id = c.category_id
  JOIN brand b ON p.brand_id = b.brand_id
  WHERE p.product_id = '$product_id'
";
$old_info = $conn->query($old_info_sql)->fetch_assoc();
if (!$old_info) exit('找不到商品');

// 取得舊標籤
$old_tags = [];
$res = $conn->query("
  SELECT t.tag_id, t.name, tt.name AS tag_type_name 
  FROM product_tag pt
  JOIN tag t ON pt.tag_id = t.tag_id
  JOIN tag_type tt ON t.tag_type_id = tt.tag_type_id
  WHERE pt.product_id = '$product_id'
");
while ($row = $res->fetch_assoc()) {
  $old_tags[$row['tag_type_name']] = $row['name'];
}

//新資料開始
$category_id = $_POST['category_id'];
$brand_id = $_POST['brand_id'];
$product_name = $_POST['product_name'];
$image_path = $_POST['image_path'];
$short_desc = $_POST['short_description'];
$detail_desc = $_POST['detail_description'];
$price = (int)$_POST['price'];
$is_active = (int)$_POST['is_active'];
$tags = $_POST['tags'] ?? [];

$fields_changed = [];

// 比較欄位變化
function compare($label, $old, $new) {
  return ($old != $new) ? "$label: [$old] → [$new]" : '';
}

$fields_changed[] = compare('商品名稱', $old_info['product_name'], $product_name);
$fields_changed[] = compare('圖片路徑', $old_info['image_path'], $image_path);
$fields_changed[] = compare(
  '短描述',
  str_replace(["\r", "\n"], ['', ' \\n '], $old_info['short_description']),
  str_replace(["\r", "\n"], ['', ' \\n '], $_POST['short_description'])
);

$fields_changed[] = compare('價格', $old_info['price'], $price);
$fields_changed[] = compare('是否上架', $old_info['is_active'], $is_active);

// 查詢分類與品牌名稱
$cat_name = $conn->query("SELECT name FROM category WHERE category_id = '$category_id'")->fetch_assoc()['name'] ?? '未知分類';
$brand_name = $conn->query("SELECT name FROM brand WHERE brand_id = '$brand_id'")->fetch_assoc()['name'] ?? '未知品牌';
$fields_changed[] = compare('分類', $old_info['category_name'], $cat_name);
$fields_changed[] = compare('品牌', $old_info['brand_name'], $brand_name);

// 處理圖片淘汰
$extra_log = [];
if ($old_info['image_path'] && $old_info['image_path'] !== $image_path && file_exists("../../" . $old_info['image_path'])) {
  unlink("../../" . $old_info['image_path']);
  $extra_log[] = '更新主照片';
}
$old_imgs = array_filter(explode("\n", $old_info['detail_description']));
$new_imgs = array_filter(explode("\n", $detail_desc));
$imgs_to_delete = array_diff($old_imgs, $new_imgs);
foreach ($imgs_to_delete as $img) {
  $img = trim($img);
  if (strpos($img, 'img/') === 0 && file_exists("../../" . $img)) {
    unlink("../../" . $img);
  }
}
if (!empty($imgs_to_delete)) {
  $extra_log[] = '更新描述照片';
}

// 更新資料
$update_sql = "
  UPDATE product SET 
    category_id = '$category_id',
    brand_id = '$brand_id',
    product_name = '$product_name',
    image_path = '$image_path',
    short_description = '$short_desc',
    detail_description = '$detail_desc',
    price = $price,
    is_active = $is_active
  WHERE product_id = '$product_id'
";
$conn->query($update_sql);

// 處理新標籤
$new_tags = [];
$conn->query("DELETE FROM product_tag WHERE product_id = '$product_id'");
$tag_count = 0;
foreach ($tags as $type_id => $tag_ids) {
  foreach ((array)$tag_ids as $tag_id) {
    $tag_id = $conn->real_escape_string($tag_id);
    if ($tag_id) {
      $tag_count++;
      $conn->query("INSERT INTO product_tag (product_id, tag_id) VALUES ('$product_id', '$tag_id')");

      $tag_res = $conn->query("
        SELECT t.name AS tag_name, tt.name AS tag_type_name 
        FROM tag t 
        JOIN tag_type tt ON t.tag_type_id = tt.tag_type_id 
        WHERE t.tag_id = '$tag_id'
      ");
      $tag_data = $tag_res->fetch_assoc();
      $new_tags[$tag_data['tag_type_name']] = $tag_data['tag_name'];
    }
  }
}
if ($tag_count === 0) {
  exit('請至少選擇一個標籤');
}

// 比較標籤
$tag_changes = [];
foreach (array_unique(array_merge(array_keys($old_tags), array_keys($new_tags))) as $type_name) {
  $old = $old_tags[$type_name] ?? '（無）';
  $new = $new_tags[$type_name] ?? '（無）';
  if ($old != $new) {
    $tag_changes[] = "$type_name: [$old] → [$new]";
  }
}

// 紀錄 log
$detail_text = "更新 {$cat_name} [$product_name, $brand_name]";
if ($fields_changed = array_filter($fields_changed)) {
  $detail_text .= "\n欄位：\n" . implode("\n", $fields_changed);
}
if ($tag_changes) {
  $detail_text .= "\n標籤：\n" . implode("\n", $tag_changes);
}
if (!empty($extra_log)) {
  $detail_text .= "\n圖片：\n" . implode("、", $extra_log);
}
log_admin_action($conn, $_SESSION['super_user_id'], '更新', 'product', $product_id, $detail_text);

header("Location: product_manage.php?update=success");
exit;
