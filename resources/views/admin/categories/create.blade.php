@extends('admin.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container-fluid px-4 py-4 premium-layout" style="background-color: #f4f7f9; font-family: 'Inter', sans-serif; min-height: 100vh;">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-plus"></i>
                </div>
                Thêm danh mục mới
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Nhập thông tin để tạo nhóm sản phẩm mới</p>
        </div>
        
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light fw-bold shadow-sm hover-lift px-4 py-2 border" style="border-radius: 12px;">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="row fade-in-up" style="animation-delay: 0.2s;">
        <div class="col-lg-8 mx-auto">
            <div class="card premium-card border-0 shadow-sm">
                <div class="card-body p-5">
                    
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg premium-input @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Ví dụ: Vợt Pickleball, Balo thể thao..." 
                                   required>
                            @error('name')
                                <div class="invalid-feedback fw-medium mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Hình ảnh đại diện</label>
                            
                            <div class="upload-box shadow-sm" id="upload-box" onclick="document.getElementById('image-input').click()">
                                <div class="upload-content text-center" id="upload-content">
                                    <div class="icon-bg mb-3 mx-auto">
                                        <i class="fa-solid fa-cloud-arrow-up text-primary fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Nhấn để tải ảnh lên</h6>
                                    <p class="text-muted small mb-0">Hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB)</p>
                                </div>
                                
                                <img id="image-preview" class="d-none" src="" alt="Preview">
                                
                                <button type="button" class="btn btn-danger btn-sm rounded-circle d-none remove-preview-btn" id="remove-btn" onclick="removeImage(event)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <input type="file" id="image-input" name="image" class="d-none" accept="image/jpeg, image/png, image/webp" onchange="previewImage(this)">
                            
                            @error('image')
                                <div class="text-danger small fw-medium mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark d-block">Trạng thái hoạt động <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="status" id="status_1" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="status_1">
                                        <span class="status-dot bg-success d-inline-block me-1" style="width:10px; height:10px; border-radius:50%;"></span> Hiển thị
                                    </label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="status" id="status_0" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
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
                                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu danh mục
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT: XỬ LÝ PREVIEW ẢNH SIÊU MƯỢT --}}
<script>
    function previewImage(input) {
        var preview = document.getElementById('image-preview');
        var content = document.getElementById('upload-content');
        var removeBtn = document.getElementById('remove-btn');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                content.classList.add('d-none');
                removeBtn.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage(event) {
        event.stopPropagation(); // Ngăn chặn nổi bọt (không cho click xuyên xuống thẻ cha)
        
        var input = document.getElementById('image-input');
        var preview = document.getElementById('image-preview');
        var content = document.getElementById('upload-content');
        var removeBtn = document.getElementById('remove-btn');
        
        // Reset giá trị
        input.value = ""; 
        preview.src = "";
        
        // Chuyển đổi trạng thái hiển thị
        preview.classList.add('d-none');
        removeBtn.classList.add('d-none');
        content.classList.remove('d-none');
    }
</script>

{{-- SUPER CSS UI/UX LỘT XÁC --}}
<style>
    /* Typography & Utils */
    .fw-extrabold { font-weight: 800; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); }

    /* Layout & Cards */
    .premium-card { border-radius: 20px; }
    
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Form Inputs */
    .premium-input {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 14px 20px;
        font-size: 0.95rem;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    .premium-input:focus {
        background-color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }
    .premium-input::placeholder { color: #94a3b8; }

    /* Custom Radio Buttons */
    .custom-radio .form-check-input {
        width: 1.2rem; height: 1.2rem; cursor: pointer;
    }
    .custom-radio .form-check-input:checked {
        background-color: #3b82f6; border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    .custom-radio .form-check-label { cursor: pointer; }

    /* Upload Box UI */
    .upload-box {
        position: relative;
        height: 220px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .upload-box:hover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .upload-box .icon-bg {
        width: 60px; height: 60px;
        border-radius: 50%; background: #fff;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    #image-preview {
        width: 100%; height: 100%;
        object-fit: contain; /* Hoặc cover tùy sở thích hiển thị của anh */
        background-color: #f1f5f9;
    }
    
    /* Nút Xóa ảnh */
    .remove-preview-btn {
        position: absolute;
        top: 15px; right: 15px;
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 10;
    }

    /* Nút bấm hover */
    .hover-lift { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
</style>
@endsection