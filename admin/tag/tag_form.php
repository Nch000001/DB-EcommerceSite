<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

// 取得 tag_type 資料用來顯示選單
$tag_types = $conn->query("SELECT tag_type_id, name FROM tag_type ORDER BY name")->fetch_all(MYSQLI_ASSOC);

?>

<form method="POST" action="tag_add_save.php">
  <div class="mb-3">
    <label class="form-label">標籤種類</label>
    <select name="tag_type_id" id="tag_type_id" class="form-select" required>
      <option value="">請選擇標籤種類</option>
      <?php foreach ($tag_types as $type): ?>
        <option value="<?= $type['tag_type_id'] ?>" data-prefix="<?= substr($type['name'], 0, 2) ?>">
          <?= htmlspecialchars($type['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">標籤代碼 (自動產生)</label>
    <input type="text" name="tag_id_display" id="tag_id_display" class="form-control" readonly required>
    <input type="hidden" name="tag_id" id="tag_id_hidden">
  </div>

  <div class="mb-3">
    <label class="form-label">標籤名稱</label>
    <input type="text" name="name" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-success">新增</button>
  <a href="../ecommerce_admin.php" class="btn btn-secondary">返回</a>
</form>

<script>
document.getElementById('tag_type_id').addEventListener('change', function() {
  const tagTypeId = this.value;
  if (!tagTypeId) return;

  fetch('get_next_tag_id.php?tag_type_id=' + tagTypeId)
    .then(res => res.text())
    .then(tagId => {
      document.getElementById('tag_id_display').value = tagId;
      document.getElementById('tag_id_hidden').value = tagId;
    });
});
</script>
