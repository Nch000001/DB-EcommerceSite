<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

// 取得所有 tag_type
$tag_types = $conn->query("SELECT tag_type_id, name FROM tag_type ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$editing = false; // 新增模式

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>新增標籤</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h2>新增標籤</h2>
  <?php include 'tag_form.php'; ?>
</body>
</html>
