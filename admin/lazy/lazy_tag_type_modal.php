<!-- Modal -->
<div class="modal fade" id="addTagTypeModal" tabindex="-1" aria-labelledby="addTagTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addTagTypeForm" onsubmit="submitAddTagTypeForm(event)">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTagTypeModalLabel">新增標籤類型</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="tagTypeName" class="form-label">標籤類型名稱</label>
            <input type="text" class="form-control" id="tagTypeName" name="tag_type_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">選擇適用分類</label>
            <div id="tagTypeCategoryCheckboxes" class="form-check">
              <?php foreach ($categories as $cat): ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $cat['category_id'] ?>" id="cat_<?= $cat['category_id'] ?>">
                  <label class="form-check-label" for="cat_<?= $cat['category_id'] ?>"><?= $cat['name'] ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div id="addTagTypeError" class="text-danger small"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">新增</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function openAddTagTypeModal() {
  const modal = new bootstrap.Modal(document.getElementById('addTagTypeModal'));
  document.getElementById('addTagTypeForm').reset();
  document.getElementById('addTagTypeError').textContent = '';
  modal.show();
}

function submitAddTagTypeForm(e) {
  e.preventDefault();
  const form = document.getElementById('addTagTypeForm');
  const formData = new FormData(form);

  fetch('lazy_add_tag_type.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const modal = bootstrap.Modal.getInstance(document.getElementById('addTagTypeModal'));
      modal.hide();
      appendNewTagType(data.tag_type_id, formData.get('tag_type_name'));
    } else {
      document.getElementById('addTagTypeError').textContent = data.message || '新增失敗';
    }
  })
  .catch(() => {
    document.getElementById('addTagTypeError').textContent = '伺服器錯誤，請稍後再試';
  });
}
</script>
