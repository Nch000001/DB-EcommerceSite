<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadFolder = $_POST['folder'] ?? 'img';
    $uploadDir = dirname(__DIR__, 2) . "/$uploadFolder/";

    if (!is_dir($uploadDir) ) {
        echo "❌ 資料夾不存在 $uploadDir";
    }
    if( !is_writable($uploadDir)){
      echo "不可寫入";
      exit;
    }
    $success = 0;
    $failed = [];

    foreach ($_FILES['images']['name'] as $i => $name) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['images']['tmp_name'][$i];
            $safeName = basename($name);
            $target = $uploadDir . $safeName;

            if (move_uploaded_file($tmp, $target)) {
                $success++;
                echo "✅ 上傳成功：<a href='../../$uploadFolder/$safeName' target='_blank'>$safeName</a><br>";
            } else {
                $failed[] = $name;
            }
        } else {
            $failed[] = $name;
        }
    }

    if (!empty($failed)) {
        echo "❌ 上傳失敗： " . implode(', ', $failed);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>上傳多張圖片</title>
</head>
<body>
  <h2>📸 上傳圖片</h2>

  <form method="POST" enctype="multipart/form-data" action="upload_image.php">
    <label>選擇圖片資料夾：</label>
    <select name="folder" required>
      <option value="img">產品圖片（img/）</option>
      <option value="ad_img">廣告圖片（ad_img/）</option>
    </select><br><br>

    <input type="file" name="images[]" multiple accept="image/*" required>
    <button type="submit">上傳</button>
    <button type="submit"> <a href="../ecommerce_admin.php">返回</a> </button>
  </form>
</body>
</html>

