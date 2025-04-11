<?php
require_once '../lib/db.php';
$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'];
    $account = $_POST['account'];
    $raw_password = $_POST['password'];
    $level = (int)$_POST['level'];

    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO super_admin (admin_id, admin_account, admin_password, level, is_active)
        VALUES (?, ?, ?, ?, 1)
    ");
    $stmt->bind_param("sssi", $admin_id, $account, $hashed_password, $level);

    if ($stmt->execute()) {
        echo "<h3 style='color:green'>✅ 管理員帳號已建立：{$admin_id}</h3>";
        echo "<p>請立即刪除此檔案以避免被濫用！</p>";
    } else {
        echo "<h3 style='color:red'>❌ 建立失敗：</h3>";
        echo $conn->error;
    }

    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>建立管理員帳號</title>
</head>
<body style="font-family: sans-serif; padding: 40px">
  <h2>🔐 建立超級管理員</h2>
  <form method="POST">
    <p>
      <label>管理員 ID（admin_id）</label><br>
      <input name="admin_id" required>
    </p>
    <p>
      <label>帳號（admin_account）</label><br>
      <input name="account" required>
    </p>
    <p>
      <label>密碼（admin_password）</label><br>
      <input name="password" type="password" required>
    </p>
    <p>
      <label>權限等級（level）</label><br>
      <input name="level" type="number" value="1" required>
    </p>
    <button type="submit">✅ 建立管理員</button>
  </form>
</body>
</html>
