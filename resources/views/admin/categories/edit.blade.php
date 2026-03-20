@extends('admin.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container-fluid px-4 py-4 premium-layout" style="background-color: #f4f7f9; font-family: 'Inter', sans-serif; min-height: 100vh;">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                Chỉnh sửa danh mục
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Cập nhật thông tin nhóm sản phẩm</p>
        </div>
        
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light fw-bold shadow-sm hover-lift px-4 py-2 border" style="border-radius: 12px;">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="row fade-in-up" style="animation-delay: 0.2s;">
        <div class="col-lg-8 mx-auto">
            <div class="card premium-card border-0 shadow-sm">
                <div class="card-body p-5">
                    
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg premium-input @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   placeholder="Ví dụ: Vợt Pickleball, Balo thể thao..." 
                                   required>
                            @error('name')
                                <div class="invalid-feedback fw-medium mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark d-flex align-items-center">
                                Phân loại danh mục
                                <span class="badge bg-light text-secondary border ms-2 fw-medium" style="font-size: 0.75rem; padding: 4px 8px;">Tùy chọn</span>
                            </label>
                            
                            <div class="custom-select-wrapper" id="customSelectWrapper">
                                <input type="hidden" name="parent_id" id="parent_id_input" value="{{ old('parent_id', $category->parent_id) }}">
                                
                                <div class="custom-select-trigger premium-input @error('parent_id') border-danger @enderror" onclick="toggleCustomSelect()">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-wrap bg-light text-primary"><i class="fa-solid fa-folder-tree"></i></div>
                                        <span id="selected-text" class="fw-medium text-dark">Không thuộc nhóm nào (Danh mục gốc)</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-muted select-arrow"></i>
                                </div>
                                
                                <div class="custom-select-options shadow-lg">
                                    <div class="custom-opt" data-value="" onclick="selectOption(this, 'Không thuộc nhóm nào (Danh mục gốc)', 'fa-folder-tree')">
                                        <div class="icon-wrap bg-light text-secondary me-3"><i class="fa-solid fa-layer-group"></i></div>
                                        <span class="fw-bold">Không thuộc nhóm nào (Danh mục gốc)</span>
                                    </div>
                                    
                                    @foreach($parentCategories as $parent)
                                        <div class="custom-opt" data-value="{{ $parent->id }}" onclick="selectOption(this, 'Thuộc nhóm: {{ $parent->name }}', 'fa-folder')">
                                            <div class="icon-wrap bg-primary-soft text-primary me-3"><i class="fa-regular fa-folder-open"></i></div>
                                            <span class="fw-medium text-dark">Thuộc nhóm: <span class="fw-bold">{{ $parent->name }}</span></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            @error('parent_id') <div class="text-danger small fw-medium mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Hình ảnh đại diện</label>
                            
                            <div class="upload-box shadow-sm" id="upload-box" onclick="document.getElementById('image-input').click()">
                                
                                <div class="upload-content text-center {{ $category->image ? 'd-none' : '' }}" id="upload-content">
                                    <div class="icon-bg mb-3 mx-auto">
                                        <i class="fa-solid fa-cloud-arrow-up text-primary fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Nhấn để tải ảnh lên</h6>
                                    <p class="text-muted small mb-0">Hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB)</p>
                                </div>
                                
                                <img id="image-preview" class="{{ $category->image ? '' : 'd-none' }}" src="{{ $category->image ? asset($category->image) : '' }}" alt="Preview">
                                
                                <button type="button" class="btn btn-danger btn-sm rounded-circle {{ $category->image ? '' : 'd-none' }} remove-preview-btn" id="remove-btn" onclick="removeImage(event)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <input type="file" id="image-input" name="image" class="d-none" accept="image/jpeg, image/png, image/webp" onchange="previewImage(this)">
                            <input type="hidden" name="remove_image" id="remove_image_input" value="0">

                            @error('image')
                                <div class="text-danger small fw-medium mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark d-block">Trạng thái hoạt động <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="status" id="status_1" value="1" {{ old('status', $category->status) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="status_1">
                                        <span class="status-dot bg-success d-inline-block me-1" style="width:10px; height:10px; border-radius:50%;"></span> Hiển thị
                                    </label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="status" id="status_0" value="0" {{ old('status', $category->status) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="status_0">
                                        <span class="status-dot bg-secondary d-inline-block me-1" style="width:10px; height:10px; border-radius:50%;"></span> Ẩn danh mục
                                    </label>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger small fw-medium mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="border-light mb-4">
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light fw-bold px-4 py-2 hover-lift text-muted border" style="border-radius: 12px;">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary fw-bold shadow-primary px-4 py-2 hover-lift" style="border-radius: 12px;">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu cập nhật
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ----- LOGIC CUSTOM DROPDOWN SELECT -----
    function toggleCustomSelect() {
        document.getElementById('customSelectWrapper').classList.toggle('open');
    }

    function selectOption(element, text, iconClass) {
        let value = element.getAttribute('data-value');
        document.getElementById('parent_id_input').value = value;
        
        let triggerIcon = document.querySelector('.custom-select-trigger .icon-wrap i');
        triggerIcon.className = `fa-solid ${iconClass}`;
        document.getElementById('selected-text').innerText = text;
        
        document.getElementById('customSelectWrapper').classList.remove('open');
        document.querySelectorAll('.custom-opt').forEach(opt => opt.classList.remove('active'));
        element.classList.add('active');
    }

    document.addEventListener('click', function(e) {
        let wrapper = document.getElementById('customSelectWrapper');
        if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('open');
        }
    });

    // Auto chọn lại khi trang tải xong (dựa trên DB hoặc Old Value)
    window.addEventListener('DOMContentLoaded', (event) => {
        let oldVal = document.getElementById('parent_id_input').value;
        if(oldVal) {
            let opt = document.querySelector(`.custom-opt[data-value="${oldVal}"]`);
            if(opt) { opt.click(); }
        }
    });

    // ----- LOGIC UPLOAD ẢNH -----
    function previewImage(input) {
        var preview = document.getElementById('image-preview');
        var content = document.getElementById('upload-content');
        var removeBtn = document.getElementById('remove-btn');
        var removeInput = document.getElementById('remove_image_input');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                content.classList.add('d-none');
                removeBtn.classList.remove('d-none');
                removeInput.value = "0";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage(event) {
        event.stopPropagation(); 
        var input = document.getElementById('image-input');
        var preview = document.getElementById('image-preview');
        var content = document.getElementById('upload-content');
        var removeBtn = document.getElementById('remove-btn');
        var removeInput = document.getElementById('remove_image_input');
        
        input.value = ""; 
        removeInput.value = "1";
        preview.src = "";
        preview.classList.add('d-none');
        removeBtn.classList.add('d-none');
        content.classList.remove('d-none');
    }
</script>

<style>
    .fw-extrabold { font-weight: 800; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); }
    .bg-primary-soft { background-color: #eff6ff; }

    .premium-card { border-radius: 20px; }
    
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    .premium-input {
        border-radius: 12px; border: 1px solid #e2e8f0; padding: 14px 20px;
        font-size: 0.95rem; background-color: #f8fafc; transition: all 0.3s ease;
    }
    .premium-input:focus { background-color: #fff; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }

    /* ====== CUSTOM SELECT CSS ====== */
    .custom-select-wrapper { position: relative; user-select: none; width: 100%; }
    .custom-select-trigger {
        display: flex; align-items: center; justify-content: space-between;
        cursor: pointer; background-color: #f8fafc; padding: 10px 16px;
    }
    .custom-select-wrapper.open .custom-select-trigger { background-color: #fff; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }
    .custom-select-wrapper.open .select-arrow { transform: rotate(180deg); }
    .select-arrow { transition: transform 0.3s ease; }
    
    .icon-wrap { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }

    .custom-select-options {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0;
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        opacity: 0; visibility: hidden; transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 100; max-height: 250px; overflow-y: auto;
    }
    .custom-select-wrapper.open .custom-select-options { opacity: 1; visibility: visible; transform: translateY(0); }
    
    .custom-opt { padding: 12px 16px; display: flex; align-items: center; cursor: pointer; transition: all 0.2s ease; border-bottom: 1px solid #f1f5f9; }
    .custom-opt:last-child { border-bottom: none; }
    .custom-opt:hover { background-color: #eff6ff; padding-left: 24px; }
    .custom-opt.active { background-color: #f8fafc; }
    /* ====== END CUSTOM SELECT CSS ====== */

    .custom-radio .form-check-input { width: 1.2rem; height: 1.2rem; cursor: pointer; }
    .custom-radio .form-check-input:checked { background-color: #3b82f6; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
    .custom-radio .form-check-label { cursor: pointer; }

    .upload-box {
        position: relative; height: 220px; border: 2px dashed #cbd5e1; border-radius: 16px;
        background-color: #f8fafc; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.3s ease; overflow: hidden;
    }
    .upload-box:hover { border-color: #3b82f6; background-color: #eff6ff; }
    .upload-box .icon-bg { width: 60px; height: 60px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    #image-preview { width: 100%; height: 100%; object-fit: contain; background-color: #f1f5f9; }
    
    .remove-preview-btn { position: absolute; top: 15px; right: 15px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.2); z-index: 10; }
    .hover-lift { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
    
    .custom-select-options::-webkit-scrollbar { width: 6px; }
    .custom-select-options::-webkit-scrollbar-track { background: transparent; }
    .custom-select-options::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    /* FIX RADIO BỊ LỆCH */
.custom-radio {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-left: 0 !important;
}

.custom-radio .form-check-input {
    position: static !important;
    margin: 0 !important;
}

.custom-radio .form-check-label {
    margin: 0;
}
</style>
@endsection