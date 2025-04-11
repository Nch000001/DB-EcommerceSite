<?php
require_once '../../lib/db.php';
require_once '../../lib/get_next_product_id.php';
require_once '../../lib/log_helper.php';
session_start();
header('Content-Type: application/json');

$conn = getDBConnection();

$product_names = $_POST['product_name'] ?? [];
$category_ids = $_POST['category_id'] ?? [];
$brand_ids = $_POST['brand_id'] ?? [];
$prices = $_POST['price'] ?? [];
$stocks = $_POST['stock_quantity'] ?? [];
$short_descriptions = $_POST['short_description'] ?? [];
$detail_descriptions = $_POST['detail_description'] ?? [];
$image_paths = $_POST['image_path'] ?? [];
$tags = $_POST['tags'] ?? [];

$now = date('Y-m-d H:i:s');
$super_user_id = $_SESSION['super_user_id'] ?? 'unknown';

$success_count = 0;
$error_messages = [];

for ($i = 0; $i < count($product_names); $i++) {
    $name = trim($product_names[$i]);
    $category_id = $category_ids[$i] ?? '';
    $brand_id = $brand_ids[$i] ?? '';
    $price = intval($prices[$i] ?? 0);
    $stock = intval($stocks[$i] ?? 0);
    $short_desc = trim($short_descriptions[$i] ?? '');
    $detail_desc = trim($detail_descriptions[$i] ?? '');
    $image_path = $image_paths[$i] ?? '';

    if ($name === '' || $category_id === '' || $brand_id === '') {
        $error_messages[] = "第 " . ($i+1) . " 筆商品資料不完整，略過。";
        continue;
    }

    // 使用 get_next_product_id
    $product_id = getNextProductID($conn, $category_id);

    $stmt = $conn->prepare("INSERT INTO product (
        product_id, product_name, category_id, brand_id,
        price, inserting_time, short_description,
        detail_description, stock_quantity, is_active, image_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");

    $stmt->bind_param(
        "sssissssis",
        $product_id,
        $name,
        $category_id,
        $brand_id,
        $price,
        $now,
        $short_desc,
        $detail_desc,
        $stock,
        $image_path
    );

    if ($stmt->execute()) {
        $success_count++;

        // 插入標籤
        if (!empty($tags)) {
            foreach ($tags as $tag_type_id => $tag_array) {
                if (isset($tag_array[$i])) {
                    foreach ((array)$tag_array[$i] as $tag_id) {
                        $stmt_tag = $conn->prepare("INSERT INTO product_tag (product_id, tag_id) VALUES (?, ?)");
                        $stmt_tag->bind_param("ss", $product_id, $tag_id);
                        $stmt_tag->execute();
                    }
                }
            }
        }

        // 紀錄 log
        $category_name = $conn->query("SELECT name FROM category WHERE category_id = '$category_id'")->fetch_assoc()['name'] ?? '未知分類';
        $brand_name = $conn->query("SELECT name FROM brand WHERE brand_id = '$brand_id'")->fetch_assoc()['name'] ?? '未知品牌';
        $details = "懶人-新增 $category_name [$name, $brand_name]";
        log_admin_action($conn, $super_user_id, '新增', 'product', $product_id, $details);
        // 紀錄 log 結束
    } else {
        $error_messages[] = "第 " . ($i+1) . " 筆商品新增失敗：" . $stmt->error;
    }
}

if ($success_count > 0) {
    echo json_encode([
        'success' => true,
        'message' => "成功新增 {$success_count} 筆商品",
        'errors' => $error_messages
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '沒有任何商品成功新增',
        'errors' => $error_messages
    ]);
}
exit;
