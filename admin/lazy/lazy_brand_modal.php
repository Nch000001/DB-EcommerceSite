<!-- lazy_brand_modal.php -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addBrandForm" onsubmit="submitAddBrandForm(event)">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addBrandModalLabel">新增品牌</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control" id="brandName" name="name" placeholder="輸入品牌名稱" required>
          <div id="addBrandError" class="text-danger small mt-2"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">新增</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function openAddBrandModal(targetSelect) {
  document.getElementById('addBrandForm').reset();
  document.getElementById('addBrandError').textContent = '';
  document.getElementById('addBrandForm').dataset.target = targetSelect;
  new bootstrap.Modal(document.getElementById('addBrandModal')).show();
}

function submitAddBrandForm(e) {
  e.preventDefault();
  const form = e.target;
  const target = form.dataset.target;
  const formData = new FormData(form);

  fetch('lazy_add_brand.php', {
    method: 'POST',
    body: formData
  }).then(res => res.json()).then(data => {
    if (data.success) {
      const select = document.querySelector(`select[name="brand_id[]"][data-id="${target}"]`);
      const option = new Option(data.name, data.brand_id, true, true);
      select.appendChild(option);
      bootstrap.Modal.getInstance(document.getElementById('addBrandModal')).hide();
    } else {
      document.getElementById('addBrandError').textContent = data.error || '新增失敗';
    }
  }).catch(() => {
    document.getElementById('addBrandError').textContent = '伺服器錯誤';
  });
}
</script>
