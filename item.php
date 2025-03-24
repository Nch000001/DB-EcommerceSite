<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
global $conn;

// 2. 取得網址上的 category_id
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

// 3. 若沒帶 category_id，跳回首頁或顯示錯誤
if (!$category_id) {
    echo "未指定分類。";
    exit;
}

// 4. 查詢該分類的商品
$sql = "SELECT * FROM products WHERE category_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $category_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 5. (可選) 查詢該分類名稱（顯示在標題）
$category_name = "";
$cat_sql = "SELECT category_name FROM categories WHERE category_id = ?";
$cat_stmt = mysqli_prepare($conn, $cat_sql);
mysqli_stmt_bind_param($cat_stmt, "s", $category_id);
mysqli_stmt_execute($cat_stmt);
$cat_result = mysqli_stmt_get_result($cat_stmt);
if ($row = mysqli_fetch_assoc($cat_result)) {
    $category_name = $row['category_name'];
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($category_name); ?> 商品列表</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($category_name); ?></h1>

    <div class="product-list">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while($prod = mysqli_fetch_assoc($result)): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($prod['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($prod['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($prod['product_name']); ?></h3>
                    <p>價格：<?php echo number_format($prod['product_price']); ?> 元</p>
                    <a href="item.php?product_id=<?php echo $prod['product_id']; ?>">立即購買</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>這個分類下還沒有商品喔！</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// 關閉連線
mysqli_close($conn);
?>
