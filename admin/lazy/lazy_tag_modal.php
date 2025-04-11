<!-- Modal -->
<div class="modal fade" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addTagForm" onsubmit="submitAddTagForm(event)">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTagModalLabel">新增標籤</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="addTagTypeId" name="tag_type_id">
          <div class="mb-3">
            <label for="addTagName" class="form-label">標籤名稱</label>
            <input type="text" class="form-control" id="addTagName" name="tag_name" required>
          </div>
          <div id="addTagError" class="text-danger small"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">新增</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function submitAddTagForm(e) {
    e.preventDefault();
    const form = document.getElementById('addTagForm');
    const formData = new FormData(form);
    const tagTypeId = formData.get('tag_type_id');

    fetch('lazy_add_tag.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('addTagModal'));
        modal.hide();
        form.reset();
        document.getElementById('addTagError').textContent = '';
        appendNewTagToType(data.tag_type_id, data.tag_id, data.tag_name);
      } else {
        document.getElementById('addTagError').textContent = data.error || '新增失敗';
      }
    })
    .catch(() => {
      document.getElementById('addTagError').textContent = '發生錯誤，請稍後再試。';
    });
  }

  function appendNewTagToType(tag_type_id, tag_id, tag_name) {
    const block = document.querySelector(`div[data-tag-type-id='${tag_type_id}']`);
    if (!block) return;

    const label = document.createElement('label');
    label.classList.add('me-3');
    label.innerHTML = `<input type="checkbox" name="tags[${tag_type_id}][]" value="${tag_id}"> ${tag_name}`;
    block.appendChild(label);
  }
</script>
