<?php
// lazy_delete_detail_image.php
$targetDir = '../../img/';
$response = ['success' => false];

if (!isset($_POST['filename'])) {
  $response['error'] = '未指定檔案名稱';
  echo json_encode($response);
  exit;
}

$filename = basename($_POST['filename']);
$targetFile = $targetDir . $filename;

if (file_exists($targetFile) && is_file($targetFile)) {
  if (unlink($targetFile)) {
    $response['success'] = true;
  } else {
    $response['error'] = '無法刪除檔案';
  }
} else {
  $response['error'] = '檔案不存在';
}

header('Content-Type: application/json');
echo json_encode($response);
