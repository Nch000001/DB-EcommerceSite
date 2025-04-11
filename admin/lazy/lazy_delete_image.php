<?php
$filename = $_POST['filename'] ?? '';
$fullPath = "../../img/" . basename($filename);

if ($filename && file_exists($fullPath)) {
  unlink($fullPath);
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => '找不到檔案']);
}
?>
