<!-- lazy_category_modal.php -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addCategoryForm" onsubmit="submitAddCategoryForm(event)">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">新增分類</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="categoryId" class="form-label">分類 ID</label>
            <input type="text" class="form-control" id="categoryId" name="category_id" placeholder="例如：MOUSE" maxlength="5" required>
            <div class="form-text text-muted">請輸入 5 碼大寫英文代號</div>
          </div>
          <div class="mb-3">
            <label for="categoryName" class="form-label">分類名稱</label>
            <input type="text" class="form-control" id="categoryName" name="name" placeholder="例如：滑鼠" required>
          </div>
          <div id="addCategoryError" class="text-danger small mt-2"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">新增</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function openAddCategoryModal(targetSelect) {
  document.getElementById('addCategoryForm').reset();
  document.getElementById('addCategoryError').textContent = '';
  document.getElementById('addCategoryForm').dataset.target = targetSelect;
  new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
}

function submitAddCategoryForm(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const categoryId = formData.get('category_id');

  if (!/^[A-Z]{5}$/.test(categoryId)) {
    document.getElementById('addCategoryError').textContent = '分類 ID 格式錯誤，需為 5 個大寫英文字母';
    return;
  }

  fetch('lazy_add_category.php', {
    method: 'POST',
    body: formData
  }).then(res => res.json()).then(data => {
    if (data.success) {
      const select = document.querySelector(`select[name=\"category_id[]\"][data-id=\"${form.dataset.target}\"]`);
      const option = new Option(data.name, data.category_id, true, true);
      select.appendChild(option);
      bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
      // 新增成功後自動加入 tag type modal 的分類勾選欄位
      const checkboxGroup = document.getElementById('tagTypeCategoryCheckboxes');
      const wrapper = document.createElement('div');
      wrapper.className = 'form-check';
      wrapper.innerHTML = `
      <input class="form-check-input" type="checkbox" name="category_ids[]" value="${data.category_id}" id="cat_${data.category_id}" checked>
      <label class="form-check-label" for="cat_${data.category_id}">${data.name}</label>`;
      checkboxGroup.appendChild(wrapper);
    } else {
      document.getElementById('addCategoryError').textContent = data.error || '新增失敗';
    }
  }).catch(() => {
    document.getElementById('addCategoryError').textContent = '伺服器錯誤';
  });
}
</script>
