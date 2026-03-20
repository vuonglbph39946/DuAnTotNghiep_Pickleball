@extends('admin.layouts.app')

@section('content')
<div class="container-fluid premium-layout" style="font-family: 'Inter', sans-serif;">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                Cập nhật Banner
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Sửa thông tin banner: <strong class="text-primary">#{{ $banner->id }}</strong></p>
        </div>
        
        <a href="{{ route('admin.banners.index') }}" class="btn btn-white fw-bold shadow-sm hover-lift px-4 py-2 border" style="border-radius: 12px; color: #475569;">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    {{-- Thêm novalidate --}}
    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="fade-in-up" style="animation-delay: 0.2s;" novalidate>
        @csrf
        @method('PUT')
        <div class="row g-4">
            
            {{-- CỘT TRÁI: THÔNG TIN --}}
            <div class="col-lg-8">
                <div class="card premium-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="fa-solid fa-circle-info text-primary me-2"></i>Thông tin Banner</h5>
                        
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-dark">Tiêu đề (Không bắt buộc)</label>
                            <input type="text" class="form-control form-control-lg premium-input @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner->title) }}">
                            @error('title') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="link" class="form-label fw-bold text-dark">Đường dẫn / Link (Không bắt buộc)</label>
                            <input type="text" class="form-control form-control-lg premium-input @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $banner->link) }}">
                            @error('link') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label for="position" class="form-label fw-bold text-dark">Vị trí sắp xếp</label>
                                <input type="number" class="form-control form-control-lg premium-input @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $banner->position) }}" min="0">
                                {{-- BỔ SUNG HIỂN THỊ LỖI TẠI ĐÂY --}}
                                @error('position') <div class="invalid-feedback fw-medium mt-2">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark d-block">Trạng thái hiển thị</label>
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="status" id="status_1" value="1" {{ old('status', $banner->status) == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="status_1">
                                            <span class="text-success"><i class="fa-solid fa-eye me-1"></i> Hiển thị</span>
                                        </label>
                                    </div>
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="status" id="status_0" value="0" {{ old('status', $banner->status) == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="status_0">
                                            <span class="text-secondary"><i class="fa-solid fa-eye-slash me-1"></i> Ẩn</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: HÌNH ẢNH --}}
            <div class="col-lg-4">
                <div class="card premium-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="fa-solid fa-image text-info me-2"></i>Hình ảnh Banner</h6>
                        
                        <div class="upload-box shadow-sm mb-3 position-relative @error('image') border-danger @enderror" id="upload-box" onclick="document.getElementById('image-input').click()" style="height: 200px; display: flex; flex-direction: column; justify-content: center; overflow: hidden;">
                            
                            <div class="upload-content text-center d-none" id="upload-placeholder">
                                <div class="icon-bg mb-3 mx-auto" style="width: 50px; height: 50px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-cloud-arrow-up text-primary fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark">Nhấn để đổi ảnh mới</h6>
                            </div>

                            <img id="image-preview" src="{{ asset($banner->image_path) }}" alt="Preview" class="position-absolute w-100 h-100" style="object-fit: cover; top: 0; left: 0; border-radius: 14px;">
                        </div>

                        <input type="file" id="image-input" name="image" class="d-none" accept="image/jpeg, image/png, image/webp" onchange="previewImage(this)">
                        <small class="text-muted d-block text-center">Bỏ trống nếu muốn giữ nguyên ảnh cũ.</small>
                        @error('image') <div class="text-danger small fw-medium text-center mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <button type="submit" class="btn btn-warning btn-lg fw-bold shadow-sm hover-lift" style="border-radius: 12px; color: #000;">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('image-preview');
                preview.src = e.target.result;
                document.getElementById('upload-box').classList.remove('border-danger');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25); }
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    .premium-card { border-radius: 16px; }
    .premium-input { border-radius: 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; transition: all 0.3s; }
    .premium-input:focus { background-color: #fff; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }
    .custom-radio .form-check-input { width: 1.2rem; height: 1.2rem; cursor: pointer; }
    .custom-radio .form-check-input:checked { background-color: #2563eb; border-color: #2563eb; }
    .upload-box { border: 2px dashed #cbd5e1; border-radius: 16px; background-color: #f8fafc; cursor: pointer; transition: all 0.3s; }
    .upload-box:hover { border-color: #3b82f6; }
    .hover-lift { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
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