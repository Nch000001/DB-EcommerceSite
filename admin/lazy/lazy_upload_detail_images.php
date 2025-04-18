<?php
$targetDir = '../../img/';
$response = ['success' => false, 'paths' => []];

if (!isset($_FILES['images'])) {
  $response['error'] = '找不到檔案';
  echo json_encode($response);
  exit;
}

foreach ($_FILES['images']['name'] as $i => $name) {
  $tmp = $_FILES['images']['tmp_name'][$i];
  $filename = basename($name);

  // 防止檔名重複：加上 timestamp + uniqid
  $ext = pathinfo($filename, PATHINFO_EXTENSION);
  $safeName = pathinfo($filename, PATHINFO_FILENAME);
  $finalName = $safeName . '_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;

  $targetFile = $targetDir . $finalName;

  if (move_uploaded_file($tmp, $targetFile)) {
    $response['paths'][] = 'img/' . $finalName;
  }
}

$response['success'] = count($response['paths']) > 0;
if (!$response['success']) {
  $response['error'] = '圖片處理失敗';
}

header('Content-Type: application/json');
echo json_encode($response);