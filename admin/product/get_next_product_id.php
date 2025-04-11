<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
require_once '../../lib/get_next_product_id.php';

$conn = getDBConnection();

if (!isset($_GET['category_id'])) {
    exit("缺少分類 ID");
}

$category_id = $_GET['category_id'];
echo getNextProductId($conn, $category_id);

