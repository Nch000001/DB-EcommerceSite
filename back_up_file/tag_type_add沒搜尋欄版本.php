<?php
session_start();
require_once '../../lib/db.php';
$conn = getDBConnection();

// Handle add tag_type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['category_ids'])) {
    $name = trim($_POST['name']);
    $category_ids = $_POST['category_ids'];

    if ($name === '' || empty($category_ids)) {
        $error = '名稱與分類皆必填';
    } else {
        // Check name uniqueness
        $check = $conn->prepare("SELECT * FROM tag_type WHERE name = ?");
        $check->bind_param('s', $name);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $error = '標籤類型名稱已存在';
        } else {
            // Determine tag_type_id
            $prefix = (count($category_ids) > 1) ? 'US' : strtoupper(substr($category_ids[0], 0, 2));
            $query = $conn->prepare("SELECT tag_type_id FROM tag_type WHERE tag_type_id LIKE CONCAT(?, '%') ORDER BY tag_type_id DESC LIMIT 1");
            $query->bind_param('s', $prefix);
            $query->execute();
            $result = $query->get_result();
            $last_id = $result->fetch_assoc()['tag_type_id'] ?? '';

            $num = $last_id ? (int)substr($last_id, 2) + 1 : 1;
            $tag_type_id = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO tag_type (tag_type_id, name) VALUES (?, ?)");
            $stmt->bind_param('ss', $tag_type_id, $name);
            $stmt->execute();

            // Insert tag_category
            $cat_stmt = $conn->prepare("INSERT INTO tag_category (tag_type_id, category_id) VALUES (?, ?)");
            foreach ($category_ids as $cat_id) {
                $cat_stmt->bind_param('ss', $tag_type_id, $cat_id);
                $cat_stmt->execute();
            }

            header("Location: tag_manage.php");
            exit;
        }
    }
}

$categories = $conn->query("SELECT category_id, name FROM category ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>新增標籤類型</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
  <h2 class="mb-4">新增標籤類型</h2>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">標籤類型名稱</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">適用分類（可多選）</label>
      <?php foreach ($categories as $cat): ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $cat['category_id'] ?>" id="cat<?= $cat['category_id'] ?>">
          <label class="form-check-label" for="cat<?= $cat['category_id'] ?>"> <?= htmlspecialchars($cat['name']) ?> </label>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-success">新增</button>
    <a href="tag_manage.php" class="btn btn-secondary">返回</a>
  </form>
</body>
</html>
