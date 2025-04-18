<?php
require_once '../../lib/db.php';
$conn = getDBConnection();

$categories = $conn->query("SELECT category_id, name FROM category ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$brands = $conn->query("SELECT brand_id, name FROM brand ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>懶人商品新增</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .card { box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .card-header { background: #f8f9fa; cursor: pointer; position: relative; }
    .remove-btn { position: absolute; right: 10px; top: 10px; }
    .toggle-note { position: absolute; left: 50%; transform: translateX(-50%); top: 10px; color: gray; font-size: 0.9em; }
    .image-preview { max-height: 180px; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; }
    .preview-info { font-size: 0.9em; color: gray; }
  </style>
</head>
<body class="container py-4">
  <h3 class="mb-4">懶人商品新增</h3> 

  <form id="productForm">
    <div id="productCards"></div>
    <button type="submit" class="btn btn-primary mt-3">一次送出全部</button>
    <button type="button" class="btn btn-success mt-3" onclick="addCard()">+ 新增商品</button>
    <button type="submit" class="btn btn-link mt-3"><a href ="../ecommerce_admin.php?mode=add">返回</a></button>
  </form>

  <!-- 彈窗模組包含 -->
  <?php include 'lazy_tag_modal.php'; ?>
  <?php include 'lazy_tag_type_modal.php'; ?>
  <?php include 'lazy_category_modal.php'; ?>
  <?php include 'lazy_brand_modal.php'; ?>

  <template id="productCardTemplate">
    <div class="card">
      <div class="card-header" onclick="this.nextElementSibling.classList.toggle('d-none')">
        <strong>新商品</strong>
        <span class="toggle-note">點此摺疊</span>
        <button type="button" class="btn btn-sm btn-danger remove-btn" onclick="removeCard(this)">刪除</button>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="togglePreview(this)">預覽</button>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label>商品名稱</label>
            <input type="text" name="product_name[]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label>價格</label>
            <input type="number" name="price[]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label>庫存數量</label>
            <input type="number" name="stock_quantity[]" class="form-control" required>
          </div>
                                                                    <!--反正不會有人真的看這個Code 我只能說寫這種耦合性低到令人髮指的大便真的會反胃-->
          <div class="col-md-6">
            <label>分類</label>
            <div class="input-group">
              <select name="category_id[]" class="form-select category-select" onchange="loadTags(this)" data-id="cat_<?= uniqid() ?>">
                <option value="">請選擇</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['category_id'] ?>"><?= $cat['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-outline-secondary" type="button" onclick="openAddCategoryModal(this.closest('.input-group').querySelector('select').dataset.id)">+ 新增</button>
            </div>
          </div>

          <div class="col-md-6">
            <label>品牌</label>
            <div class="input-group">
              <select name="brand_id[]" class="form-select brand-select" data-id="brand_<?= uniqid() ?>">
                <option value="">請選擇</option>
                <?php foreach ($brands as $brand): ?>
                  <option value="<?= $brand['brand_id'] ?>"><?= $brand['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-outline-secondary" type="button" onclick="openAddBrandModal(this.closest('.input-group').querySelector('select').dataset.id)">+ 新增</button>
            </div>
          </div>

          <div class="col-md-12">
            <label>標籤與細項</label>
            <div class="tag-section">請先選擇分類...</div>
            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="openAddTagTypeModal()">＋ 新增標籤類型</button>
          </div>

          <div class="col-md-12">
            <label>圖片上傳</label>
            <input type="file" class="form-control" accept="image/*" onchange="handleImageUpload(this)">
            <img class="image-preview d-none" alt="預覽圖片">
            <div class="preview-info"></div>
            <button class="btn btn-sm btn-outline-danger mt-2 d-none" onclick="deletePreviewImage(this)">刪除圖片</button>
          </div>

          <div class="col-md-6">
            <label>短描述</label>
            <textarea name="short_description[]" class="form-control"></textarea>
          </div>
          <div class="col-md-6">
            <label>詳細描述</label>
            <textarea name="detail_description[]" class="form-control" rows="4"></textarea>
            <input type="file" accept="image/*" multiple class="form-control mt-2" onchange="handleDescriptionImages(this)">
          </div> 
        </div>
          
      </div>
      <div class="preview collapse mt-3 border rounded p-3 bg-light">
      <div class="row">
        <div class="col-md-4">
          <img class="preview-img img-fluid mb-2" src="" alt="預覽圖片">
        </div>
        <div class="col-md-8">
          <h5 class="preview-title">商品名稱</h5>
          <p class="preview-price text-danger fw-bold">$價格</p>
          <p class="preview-category-brand text-muted">分類 - 品牌</p>
          <p class="preview-tags small">標籤：<span class="text-secondary">無</span></p>
          <p class="preview-shortdesc mb-2">短描述預覽...</p>
          <pre class="preview-detaildesc bg-white p-2 border rounded small text-wrap"></pre>
        </div>
      </div>
      <div class="text-end">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="this.closest('.preview').classList.remove('show')">關閉預覽</button>
      </div>
    </div>
    </div>
    
  </template>

  <script>
    
    document.getElementById('productForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('lazy_add_save.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);
        if (data.success) {
        alert(data.message);
        location.reload(); // 或者清空卡片
        } else {
        alert((data.errors || []).join('\\n') || '新增失敗');
        }
    })
    .catch(() => {
        alert('送出失敗，請稍後再試');
    });
    });

    function addCard() {
      const container = document.getElementById('productCards');
      const template = document.getElementById('productCardTemplate').content.cloneNode(true);
      container.appendChild(template);
    }

    function removeCard(btn) {
      if (!confirm('確定要刪除這筆商品（包含已選圖片）？')) return;

      const card = btn.closest('.card');
      const hiddenImage = card.querySelector('input[type="hidden"][name="image_path[]"]');

      // 主圖刪除
      if (hiddenImage && hiddenImage.value) {
        const fileName = hiddenImage.value.split('/').pop();
        fetch('lazy_delete_image.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `filename=${encodeURIComponent(fileName)}`
        });
      }

      //詳細描述圖片刪除
      const detailText = card.querySelector('textarea[name="detail_description[]"]');
      if (detailText && detailText.value) {
        const lines = detailText.value.split(/\r?\n/);
        lines.forEach(line => {
          const trimmed = line.trim();
          if (trimmed.startsWith('img/')) {
            const fileName = trimmed.split('/').pop();
            fetch('lazy_delete_detail_images.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `filename=${encodeURIComponent(fileName)}`
            });
          }
        });
      }
      
      // const detailText = card.querySelector('textarea[name="detail_description[]"]'); 
      // if (detailText && detailText.value) {
      //   const lines = detailText.value.split('\\n');
      //   lines.forEach(line => {
      //     if (line.trim().startsWith('img/')) {
      //       const fileName = line.trim().split('/').pop();
      //       fetch('lazy_delete_detail_images.php', {
      //         method: 'POST',
      //         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      //         body: `filename=${encodeURIComponent(fileName)}`
      //       });
      //     }
      //   });
      // }

      card.remove();
    }

    function handleImageUpload(input) {

        const file = input.files[0];
        const preview = input.parentElement.querySelector('.image-preview');
        const info = input.parentElement.querySelector('.preview-info');
        const delBtn = input.parentElement.querySelector('.btn-outline-danger');

        if (!file) return;

        // 預覽
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            info.textContent = `檔案名稱：${file.name}，大小：${(file.size / 1024).toFixed(1)} KB`;
            delBtn.classList.remove('d-none');
        };
        reader.readAsDataURL(file);

        // 寫入圖片到 server
        const formData = new FormData();
        formData.append('image', file);

        fetch('lazy_upload_image.php', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
            // 建立隱藏欄位 <input name="image_path[]">
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'image_path[]';
            hiddenInput.value = `img/${data.filename}`;
            input.parentElement.appendChild(hiddenInput);
            } else {
            alert(data.error || '圖片上傳失敗');
            }
        }).catch(() => {
            alert('圖片上傳失敗（連線錯誤）');
        });
    }

    function deletePreviewImage(btn) {
        if (!confirm('確定要刪除這張圖片嗎？')) return;

        const group = btn.closest('.col-md-12');
        const preview = group.querySelector('.image-preview');
        const input = group.querySelector('input[type="file"]');
        const info = group.querySelector('.preview-info');
        const hiddenInput = group.querySelector('input[type="hidden"][name="image_path[]"]');

        // 如果已經上傳，就刪除 server 檔案
        if (hiddenInput && hiddenInput.value) {
            const fileName = hiddenInput.value.split('/').pop();
            fetch('lazy_delete_image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `filename=${encodeURIComponent(fileName)}`
            });
            
        }

        // 清空畫面預覽
        preview.src = '';
        preview.classList.add('d-none');
        info.textContent = '';
        btn.classList.add('d-none');
        input.value = '';
        if (hiddenInput) hiddenInput.remove();
    }

    function loadTags(select) {
      const categoryId = select.value;
      const section = select.closest('.row').querySelector('.tag-section');
      section.innerHTML = '載入中...';

      fetch(`get_tags_by_category_json.php?category_id=${categoryId}`)
        .then(res => res.json())
        .then(data => {
          let html = '';
          data.forEach(group => {
            html += `<div class='mb-2' data-tag-type-id='${group.tag_type_id}'>`;
            html += `<strong>類型: ${group.tag_type_name}</strong>`;
            html += ` <button type='button' class='btn btn-sm btn-outline-primary ms-2' onclick='openAddTagModal("${group.tag_type_id}", this)'>＋新增標籤</button><br>`;
            group.tags.forEach(tag => {
              html += `<label class='me-3'><input type='checkbox' name='tags[${group.tag_type_id}][]' value='${tag.tag_id}'> ${tag.tag_name}</label>`;
            });
            html += '</div>';
          });
          section.innerHTML = html || '無可選擇標籤';
        })
        .catch(() => section.innerHTML = '載入失敗');
    }

    function appendNewTagType(tag_type_id, tag_type_name) {
      const activeCard = document.querySelector('#productCards .card:last-child');
      const section = activeCard.querySelector('.tag-section');
      const newBlock = document.createElement('div');
      newBlock.className = 'mb-2';
      newBlock.setAttribute('data-tag-type-id', tag_type_id);
      newBlock.innerHTML = `<strong>類型: ${tag_type_name}</strong>
        <button type='button' class='btn btn-sm btn-outline-primary ms-2' onclick='openAddTagModal("${tag_type_id}", this)'>＋新增標籤</button><br>`;
      section.appendChild(newBlock);
    }

    function openAddTagModal(tag_type_id, btn) {
      const modal = new bootstrap.Modal(document.getElementById('addTagModal'));
      document.getElementById('addTagTypeId').value = tag_type_id;
      document.getElementById('addTagName').value = '';
      modal.show();
    }

    function handleDescriptionImages(input) {
      const files = input.files;
      if (!files.length) return;

      const formData = new FormData();
      for (const file of files) {
        formData.append('images[]', file);
      }

      fetch('lazy_upload_detail_images.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert(data.error || '圖片上傳失敗');
          return;
        }
        const textarea = input.closest('.card-body').querySelector('textarea[name="detail_description[]"]');
        textarea.value += data.paths.map(p => p + '\n').join('');
      })
      .catch(() => alert('圖片上傳失敗（連線錯誤）'));
    }
    function togglePreview(btn) {
      const card = btn.closest('.card');
      const preview = card.querySelector('.preview');
      const name = card.querySelector('input[name="product_name[]"]').value;
      const price = card.querySelector('input[name="price[]"]').value;
      const short = card.querySelector('textarea[name="short_description[]"]').value;
      const detail = card.querySelector('textarea[name="detail_description[]"]').value;

      const categorySelect = card.querySelector('select[name="category_id[]"]');
      const brandSelect = card.querySelector('select[name="brand_id[]"]');
      const category = categorySelect.options[categorySelect.selectedIndex]?.text || '未選分類';
      const brand = brandSelect.options[brandSelect.selectedIndex]?.text || '未選品牌';

      const image = card.querySelector('.image-preview').getAttribute('src');
      const tagSection = card.querySelector('.tag-section');
      const checkedTags = tagSection.querySelectorAll('input[type="checkbox"]:checked');
      const tagTexts = Array.from(checkedTags).map(t => t.parentElement.textContent.trim());

      preview.querySelector('.preview-img').src = image || '';
      preview.querySelector('.preview-title').textContent = name;
      preview.querySelector('.preview-price').textContent = '$' + (price || '0');
      preview.querySelector('.preview-category-brand').textContent = `${category} - ${brand}`;
      preview.querySelector('.preview-tags span').textContent = tagTexts.join('、') || '無';
      preview.querySelector('.preview-shortdesc').textContent = short;
      preview.querySelector('.preview-detaildesc').textContent = detail;

      preview.classList.add('show');
    }
    window.onload = addCard;
  </script>
</body>
</html>