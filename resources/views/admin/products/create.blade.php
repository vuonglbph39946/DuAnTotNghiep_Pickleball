@extends('admin.layouts.app')

@section('content')
<div class="container-fluid premium-layout">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-main d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-plus"></i>
                </div>
                Thêm sản phẩm mới
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Nhập đầy đủ thông tin để đăng bán sản phẩm</p>
        </div>
        
        <a href="{{ route('admin.products.index') }}" class="btn btn-action fw-bold shadow-sm hover-lift px-4 py-2 border" style="border-radius: 12px;">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="fade-in-up" style="animation-delay: 0.2s;" id="productForm">
        @csrf
        <div class="row g-4">
            
            {{-- CỘT TRÁI --}}
            <div class="col-lg-8">
                
                {{-- Block 1: Thông tin cơ bản --}}
                <div class="card premium-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold text-main mb-4 border-bottom pb-3" style="border-color: var(--border-color) !important;"><i class="fa-solid fa-circle-info text-primary me-2"></i>Thông tin cơ bản</h5>
                        
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <label for="name" class="form-label fw-bold text-main">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg premium-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm..." required>
                                @error('name') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="sku" class="form-label fw-bold text-main">Mã SP (SKU Gốc)</label>
                                <input type="text" class="form-control form-control-lg premium-input text-uppercase @error('sku') is-invalid @enderror" id="base_sku" name="sku" value="{{ old('sku') }}" placeholder="VD: VOT-JOOLA">
                                @error('sku') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold text-main">Danh mục sản phẩm <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg premium-input @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="" disabled selected>Chọn danh mục sản phẩm</option>
                                
                                @foreach($categories as $parent)
                                    <option value="{{ $parent->id }}" class="fw-bold" {{ old('category_id') == $parent->id ? 'selected' : '' }}>
                                        ❖ {{ $parent->name }}
                                    </option>
                                    @if(isset($parent->children))
                                        @foreach($parent->children as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;&nbsp;&nbsp;↳ {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-0">
                            <label for="description" class="form-label fw-bold text-main">Mô tả sản phẩm</label>
                            <textarea class="form-control premium-input @error('description') is-invalid @enderror" id="description" name="description" rows="6" placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Block 2: Cấu hình Biến thể --}}
                <div class="card premium-card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4 p-md-5 d-flex justify-content-between align-items-center">
                        <h5 class="fw-extrabold text-main mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Sản phẩm có biến thể?</h5>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input shadow-sm" type="checkbox" id="hasVariantsToggle" name="has_variants" value="1" style="cursor: pointer;">
                        </div>
                    </div>
                    
                    <div class="card-body px-4 p-md-5 pt-0 d-none" id="variantsSection">
                        <div class="alert alert-info border-0 shadow-sm mb-4 alert-custom" style="border-radius: 12px;">
                            <p class="text-primary mb-0"><i class="fa-solid fa-circle-info me-2"></i>Chọn các thuộc tính áp dụng. Hệ thống sẽ kết hợp <b>Mã SKU Gốc</b> với <b>Thuộc tính</b> để tự sinh mã cho từng biến thể.</p>
                        </div>
                        
                        <div class="row mb-4">
                            @foreach($attributes as $attr)
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded-3 bg-body-custom h-100" style="border-color: var(--border-color) !important;">
                                        <h6 class="fw-bold text-main mb-3">{{ $attr->name }}</h6>
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($attr->values as $val)
                                                <div class="form-check custom-checkbox d-flex align-items-center">
                                                    <input class="form-check-input attr-checkbox me-2" type="checkbox" 
                                                           id="val_{{ $val->id }}" value="{{ $val->id }}" 
                                                           data-attr-id="{{ $attr->id }}" data-val-name="{{ $val->value }}">
                                                    <label class="form-check-label user-select-none d-flex align-items-center text-main" for="val_{{ $val->id }}" style="cursor:pointer;">
                                                        @if($val->color_code)
                                                            <span class="d-inline-block rounded-circle me-1 border shadow-sm" style="width: 16px; height: 16px; background-color: {{ $val->color_code }}; border-color: var(--border-color) !important;"></span>
                                                        @endif
                                                        {{ $val->value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-warning fw-bold text-dark shadow-sm px-4 py-2 rounded-3 hover-lift" onclick="generateVariants()">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Tự động tạo danh sách phân loại
                            </button>
                        </div>

                        <div class="table-responsive d-none rounded-3 overflow-hidden custom-scrollbar" id="variantTableContainer" style="border: 1px solid var(--border-color);">
                            <table class="table align-middle mb-0 premium-table-inner">
                                <thead style="font-size: 0.85rem; text-transform: uppercase;">
                                    <tr>
                                        <th style="width: 25%" class="ps-3 text-muted">Phân loại</th>
                                        <th style="width: 20%" class="text-muted">Mã SKU</th>
                                        <th style="width: 18%" class="text-muted">Giá gốc <span class="text-danger">*</span></th>
                                        <th style="width: 18%" class="text-muted">Giá Sale</th>
                                        <th style="width: 12%" class="text-muted">Tồn <span class="text-danger">*</span></th>
                                        <th style="width: 7%" class="text-center pe-3 text-muted">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="variantTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI --}}
            <div class="col-lg-4">
                
                {{-- Block Giá & Kho gốc --}}
                <div class="card premium-card border-0 shadow-sm mb-4" id="basePriceStockCard">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: var(--border-color) !important;">
                            <h6 class="fw-bold text-main mb-0"><i class="fa-solid fa-coins text-warning me-2"></i>Giá & Kho hàng</h6>
                        </div>

                        <div class="mb-4 position-relative z-index-0">
                            <label for="price" class="form-label fw-bold text-main">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                {{-- ĐÃ FIX BẰNG CÁCH CHỈ NHẬN SỐ NGUYÊN HOẶC STEP ANY ĐỂ KO BỊ LỖI CHẶN DECIMAL CỦA HTML5 --}}
                                <input type="number" step="any" class="form-control premium-input @error('price') is-invalid @enderror" id="base_price" name="price" value="{{ old('price') }}" placeholder="0" min="0">
                                <span class="input-group-text fw-bold text-muted span-addon">đ</span>
                            </div>
                            @error('price') <div class="text-danger small fw-medium mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4 position-relative z-index-0">
                            <label for="sale_price" class="form-label fw-bold text-main">Giá khuyến mãi</label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="any" class="form-control premium-input @error('sale_price') is-invalid @enderror" id="base_sale_price" name="sale_price" value="{{ old('sale_price') }}" placeholder="0" min="0">
                                <span class="input-group-text fw-bold text-muted span-addon">đ</span>
                            </div>
                        </div>

                        <div class="mb-4 position-relative z-index-0">
                            <label for="stock" class="form-label fw-bold text-main">Số lượng Tồn kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control premium-input @error('stock') is-invalid @enderror" id="base_stock" name="stock" value="{{ old('stock') }}" placeholder="0" min="0">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold text-main d-block">Trạng thái <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="status" id="status_1" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-main" for="status_1">
                                        <span class="status-dot bg-success d-inline-block me-1" style="width:10px; height:10px; border-radius:50%;"></span> Đang bán
                                    </label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="status" id="status_0" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-main" for="status_0">
                                        <span class="status-dot bg-secondary d-inline-block me-1" style="width:10px; height:10px; border-radius:50%;"></span> Ẩn
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Block Hình ảnh --}}
                <div class="card premium-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-main mb-4 border-bottom pb-3" style="border-color: var(--border-color) !important;"><i class="fa-solid fa-images text-info me-2"></i>Thư viện Ảnh</h6>
                        
                        <div class="upload-box shadow-sm mb-3" id="upload-box" onclick="document.getElementById('images-input').click()">
                            <div class="upload-content text-center">
                                <div class="icon-bg mb-3 mx-auto">
                                    <i class="fa-solid fa-cloud-arrow-up text-primary fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-main">Nhấn để chọn ảnh</h6>
                                <p class="text-muted small mb-0">Hỗ trợ tải nhiều ảnh cùng lúc</p>
                            </div>
                        </div>

                        <input type="file" id="images-input" name="images[]" class="d-none" accept="image/jpeg, image/png, image/webp" multiple onchange="previewMultipleImages(this)">
                        <div id="image-preview-container" class="row g-2 mt-2"></div>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-primary hover-lift" style="border-radius: 12px; background: var(--primary); border-color: var(--primary);">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Lưu sản phẩm
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-action btn-lg fw-bold hover-lift border" style="border-radius: 12px;">Hủy bỏ</a>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // Xử lý Ảnh
    const dt = new DataTransfer();
    function previewMultipleImages(input) {
        var container = document.getElementById('image-preview-container');
        if (input.files) {
            for(let i = 0; i < input.files.length; i++) {
                let file = input.files[i];
                dt.items.add(file); 
                let reader = new FileReader();
                reader.onload = function(e) {
                    let col = document.createElement('div');
                    col.className = 'col-4 col-md-3 col-lg-4 position-relative preview-item';
                    let removeBtn = document.createElement('button');
                    removeBtn.className = 'btn btn-danger btn-sm position-absolute rounded-circle remove-multi-btn shadow-sm';
                    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    removeBtn.type = 'button';
                    removeBtn.onclick = function() {
                        let fileName = file.name;
                        for(let j = 0; j < dt.items.length; j++) {
                            if(dt.items[j].getAsFile().name === fileName) { dt.items.remove(j); break; }
                        }
                        document.getElementById('images-input').files = dt.files;
                        col.remove();
                    };
                    let img = document.createElement('img');
                    img.src = e.target.result; img.className = 'img-fluid rounded-3 shadow-sm border preview-img-obj';
                    col.appendChild(img); col.appendChild(removeBtn); container.appendChild(col);
                }
                reader.readAsDataURL(file);
            }
            input.files = dt.files;
        }
    }

    // Tắt Required của Giá gốc khi bật Biến thể
    document.getElementById('hasVariantsToggle').addEventListener('change', function() {
        const section = document.getElementById('variantsSection');
        const basePrice = document.getElementById('base_price');
        const baseStock = document.getElementById('base_stock');
        
        if(this.checked) {
            section.classList.remove('d-none');
            basePrice.removeAttribute('required');
            baseStock.removeAttribute('required');
        } else {
            section.classList.add('d-none');
            document.getElementById('variantTableContainer').classList.add('d-none');
            document.getElementById('variantTableBody').innerHTML = '';
            basePrice.setAttribute('required', 'true');
            baseStock.setAttribute('required', 'true');
        }
    });

    // ĐÃ SỬA: HÀM TẠO BIẾN THỂ TỰ ĐỘNG NỐI MÃ SKU VÀ HIỂN THỊ CỘT GIÁ SALE
    function generateVariants() {
        const checkboxes = document.querySelectorAll('.attr-checkbox:checked');
        if(checkboxes.length === 0) {
            Swal.fire({
                title: 'Opps!', 
                text: 'Vui lòng chọn ít nhất 1 giá trị thuộc tính!', 
                icon: 'warning',
                background: document.body.classList.contains('admin-dark-mode') ? '#18181b' : '#fff',
                color: document.body.classList.contains('admin-dark-mode') ? '#f4f4f5' : '#09090b'
            }); 
            return;
        }

        const currentBasePrice = document.getElementById('base_price').value;
        const currentBaseSalePrice = document.getElementById('base_sale_price').value;
        const currentBaseStock = document.getElementById('base_stock').value;
        
        // Lấy Mã SKU Gốc
        let baseSku = document.getElementById('base_sku').value.trim();
        if (baseSku !== '') baseSku += '-'; 

        const grouped = {};
        checkboxes.forEach(cb => {
            const attrId = cb.getAttribute('data-attr-id');
            if(!grouped[attrId]) grouped[attrId] = [];
            grouped[attrId].push({ id: cb.value, valName: cb.getAttribute('data-val-name') });
        });

        const combine = (groups, prefix = []) => {
            if (!groups.length) return [prefix];
            let result = [];
            groups[0].forEach(item => { result = result.concat(combine(groups.slice(1), [...prefix, item])); });
            return result;
        };

        const combinations = combine(Object.values(grouped));
        const tbody = document.getElementById('variantTableBody');
        tbody.innerHTML = ''; 

        combinations.forEach((combo, index) => {
            const names = combo.map(c => c.valName).join(' - ');
            // Tạo SKU tự động
            const autoSku = baseSku + combo.map(c => c.valName.replace(/\s+/g, '').toUpperCase()).join('-');
            
            const idsHTML = combo.map(c => `<input type="hidden" name="variants[${index}][attribute_values][]" value="${c.id}">`).join('');
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold text-primary ps-3" style="font-size: 0.9rem;">${names} ${idsHTML}</td>
                <td><input type="text" name="variants[${index}][sku]" class="form-control rounded-3 py-1 px-2 text-center inner-table-input text-uppercase fw-bold" style="font-size:0.85rem;" value="${autoSku}" placeholder="Mã SKU..."></td>
                <td><input type="number" step="any" name="variants[${index}][price]" class="form-control rounded-3 py-1 px-2 text-center inner-table-input" style="font-size:0.9rem;" value="${currentBasePrice}" placeholder="0" required></td>
                <td><input type="number" step="any" name="variants[${index}][sale_price]" class="form-control rounded-3 py-1 px-2 text-center inner-table-input text-danger fw-bold" style="font-size:0.9rem;" value="${currentBaseSalePrice}" placeholder="Trống"></td>
                <td><input type="number" name="variants[${index}][stock]" class="form-control rounded-3 py-1 px-2 text-center inner-table-input" style="font-size:0.9rem;" value="${currentBaseStock || 0}" required></td>
                <td class="text-center pe-3"><button type="button" class="btn btn-sm btn-light text-danger rounded-circle border shadow-sm p-1 d-flex align-items-center justify-content-center mx-auto" style="width:28px; height:28px;" onclick="this.closest('tr').remove()"><i class="fa-solid fa-xmark"></i></button></td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('variantTableContainer').classList.remove('d-none');
    }

    // NGĂN CHẶN LỖI LƯU KHI QUÊN BẤM NÚT VÀNG TẠO BIẾN THỂ
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const hasVariants = document.getElementById('hasVariantsToggle').checked;
        const variantRows = document.querySelectorAll('#variantTableBody tr').length;
        
        if(hasVariants && variantRows === 0) {
            e.preventDefault(); // Chặn gửi form
            Swal.fire({
                title: 'Lỗi thiếu Biến thể!', 
                text: 'Bạn đã chọn "Sản phẩm có biến thể" nhưng chưa bấm nút Vàng để Tự động tạo danh sách phân loại.', 
                icon: 'error',
                background: document.body.classList.contains('admin-dark-mode') ? '#18181b' : '#fff',
                color: document.body.classList.contains('admin-dark-mode') ? '#f4f4f5' : '#09090b'
            });
        }
    });
</script>

<style>
    /* ===== FIX CHECKBOX BỊ LỆCH (QUAN TRỌNG) ===== */
.custom-checkbox {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-left: 0 !important; /* bỏ padding mặc định bootstrap */
    margin-bottom: 6px;
}

/* bỏ position mặc định */
.custom-checkbox .form-check-input {
    position: static !important;
    margin: 0 !important;
}

/* label không bị đẩy */
.custom-checkbox .form-check-label {
    margin: 0;
}
/* ===== FIX RADIO BỊ LỆCH ===== */
.custom-radio {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-left: 0 !important; /* bỏ padding bootstrap */
}

/* bỏ absolute */
.custom-radio .form-check-input {
    position: static !important;
    margin: 0 !important;
}

/* label sát radio */
.custom-radio .form-check-label {
    margin: 0;
}
    /* ===== FIX CHỮ BỊ MỜ ===== */
.text-main,
label,
.form-control,
.form-select,
input,
select,
textarea {
    opacity: 1 !important;
    color: #111 !important;
}

/* fix riêng select option */
select option {
    color: #111 !important;
    background: #fff !important;
}

/* ===== FIX NÚT LƯU ===== */
.btn-primary {
    opacity: 1 !important;
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    color: #fff !important;
}

/* ===== FIX BẢNG BIẾN THỂ LỆCH ===== */
.premium-table-inner {
    table-layout: fixed !important;
    width: 100% !important;
}

.premium-table-inner th,
.premium-table-inner td {
    vertical-align: middle !important;
    text-align: center;
}

/* cột đầu căn trái */
.premium-table-inner th:first-child,
.premium-table-inner td:first-child {
    text-align: left;
}

/* input trong bảng không làm lệch */
.inner-table-input {
    width: 100% !important;
    min-width: 0 !important;
    padding: 6px 8px !important;
}

/* ===== FIX CHECKBOX LỆCH ===== */
.custom-checkbox .form-check-input {
    margin-top: 0 !important;
}
    /* Utilities */
    .text-main { color: var(--text-main) !important; }
    .bg-body-custom { background-color: var(--bg-body) !important; }
    
    .fw-extrabold { font-weight: 800; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, var(--primary), var(--primary-hover)); }
    .shadow-primary { box-shadow: 0 8px 20px var(--primary-glow); }
    
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Nút Action chung */
    .btn-action { background: var(--bg-surface); color: var(--text-main); border-color: var(--border-color); transition: var(--transition); }
    .btn-action:hover { background: var(--bg-body); border-color: var(--border-color); color: var(--text-main);}

    /* Form Inputs */
    .premium-input { 
        border-radius: 12px; border: 1px solid var(--border-color); padding: 12px 18px; 
        font-size: 0.95rem; background-color: var(--bg-body); color: var(--text-main); transition: var(--transition); 
    }
    .premium-input:focus { background-color: var(--bg-surface); border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
    
    .span-addon { border-radius: 12px; background-color: var(--bg-body); border: 1px solid var(--border-color); border-left: 0; border-top-left-radius: 0; border-bottom-left-radius: 0;}
    .input-group > .premium-input { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0;}
    
    /* Option / Select text color fix */
    select.premium-input option { background-color: var(--bg-surface); color: var(--text-main); }

    /* Custom Radio & Checkbox */
    .custom-radio .form-check-input { width: 1.2rem; height: 1.2rem; cursor: pointer; border-color: var(--border-color); background-color: var(--bg-body);}
    .custom-radio .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
    .custom-radio .form-check-label { cursor: pointer; }
    
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; border-color: var(--border-color); background-color: var(--bg-body);}
    .form-switch .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
    
    .custom-checkbox .form-check-input { width: 1.2em; height: 1.2em; cursor: pointer; border-color: var(--border-color); background-color: var(--bg-body);}
    .custom-checkbox .form-check-input:checked { background-color: #f59e0b; border-color: #f59e0b; }

    /* Upload Box */
    .upload-box { border: 2px dashed var(--border-color); border-radius: 16px; background-color: var(--bg-body); padding: 30px 15px; cursor: pointer; transition: var(--transition); }
    .upload-box:hover { border-color: var(--primary); background-color: var(--primary-light); }
    .upload-box .icon-bg { width: 50px; height: 50px; border-radius: 50%; background: var(--bg-surface); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); }
    
    .preview-img-obj { width: 100%; height: 80px; object-fit: cover; border-color: var(--border-color) !important;}
    .remove-multi-btn { top: -5px; right: -5px; width: 22px; height: 22px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 10px; z-index: 10;}
    
    .hover-lift { transition: var(--transition); }
    .hover-lift:hover { transform: translateY(-2px); }
    .z-index-1 { z-index: 1; pointer-events: none;}
    .z-index-0 { z-index: 0; }

    /* Alert Info Custom */
    .alert-custom { background-color: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; }

    /* Bảng Biến thể bên trong */
    .premium-table-inner { background: var(--bg-surface); }
    .premium-table-inner th { background: var(--bg-body) !important; border-bottom: 1px solid var(--border-color); padding: 12px; }
    .premium-table-inner td { border-bottom: 1px solid var(--border-color); padding: 10px; vertical-align: middle;}
    .inner-table-input { background-color: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-color); transition: var(--transition); }
    .inner-table-input:focus { border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary-light); outline: none;}
</style>
@endsection