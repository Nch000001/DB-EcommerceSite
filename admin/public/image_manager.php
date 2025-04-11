<?php
$imgDir = realpath(__DIR__ . '/../../img');  // 你的圖片資料夾
$files = array_diff(scandir($imgDir), ['.', '..']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = basename($_POST['filename']); // 防止目錄穿越攻擊
    $filePath = "$imgDir/$filename";
    if (is_file($filePath)) {
        unlink($filePath);
        echo "<p style='color:green'>✅ 已刪除：$filename</p>";
    } else {
        echo "<p style='color:red'>❌ 找不到檔案：$filename</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>圖片管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <h2 class="mb-4">🖼 圖片管理（img 資料夾）</h2>

  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($files as $file): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="/ecommerce/img/<?= htmlspecialchars($file) ?>" class="card-img-top" style="object-fit: contain; max-height: 200px;">
          <div class="card-body">
            <p class="card-text"><?= htmlspecialchars($file) ?></p>
            <form method="POST" onsubmit="return confirm('確定要刪除 <?= htmlspecialchars($file) ?> 嗎？')">
              <input type="hidden" name="filename" value="<?= htmlspecialchars($file) ?>">
              <button type="submit" class="btn btn-danger btn-sm">刪除</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</body>
</html>
