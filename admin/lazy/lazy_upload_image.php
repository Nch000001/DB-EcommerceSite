<?php
$targetDir = "../../img/";
$response = ['success' => false];

if (!empty($_FILES['image']['name'])) {
  $fileName = basename($_FILES["image"]["name"]);
  $targetFile = $targetDir . $fileName;

  if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    $response = ['success' => true, 'filename' => $fileName];
  } else {
    $response['error'] = "上傳失敗";
  }
} else {
  $response['error'] = "找不到檔案";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;