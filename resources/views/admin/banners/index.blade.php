@extends('admin.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid px-4 py-4 premium-layout" style="background-color: #f4f7f9; font-family: 'Inter', sans-serif; min-height: 100vh;">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3"><i class="fa-solid fa-images"></i></div>
                Quản lý Banner
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Thiết lập hình ảnh trình chiếu trên trang chủ</p>
        </div>
        
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary fw-bold shadow-primary hover-lift px-4 py-2" style="border-radius: 12px;">
            <i class="fa-solid fa-plus me-2"></i> Thêm Banner mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 fade-in-up" style="animation-delay: 0.2s;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card premium-card border-0 shadow-sm fade-in-up" style="animation-delay: 0.3s; border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle premium-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 30%;">Hình ảnh</th>
                            <th style="width: 25%;">Tiêu đề / Link</th>
                            <th class="text-center" style="width: 15%;">Vị trí</th>
                            <th class="text-center" style="width: 15%;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 15%;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="banner-img-box border shadow-sm rounded-3 overflow-hidden" style="width: 200px; height: 80px;">
                                        <img src="{{ asset($banner->image_path) }}" alt="Banner" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </td>
                                <td>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $banner->title ?? 'Không có tiêu đề' }}</h6>
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" target="_blank" class="text-primary small text-decoration-none"><i class="fa-solid fa-link me-1"></i> Xem link</a>
                                    @else
                                        <span class="text-muted small">Không gắn link</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 shadow-sm">{{ $banner->position }}</span>
                                </td>
                                <td class="text-center">
                                    @if($banner->status == 1)
                                        <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-eye me-1"></i> Hiển thị</span>
                                    @else
                                        <span class="badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-eye-slash me-1"></i> Đang ẩn</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-light btn-sm rounded-circle shadow-sm hover-lift border" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; color: #3b82f6;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        {{-- Nút Xóa gọi hàm JS --}}
                                        <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm hover-lift border" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; color: #ef4444;" onclick="confirmDelete({{ $banner->id }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        
                                        {{-- Form ẩn để submit --}}
                                        <form id="delete-form-{{ $banner->id }}" action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-regular fa-image fs-1 mb-3 opacity-50"></i><br>Chưa có banner nào trong hệ thống.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">
                {{ $banners->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm gọi SweetAlert2 xác nhận xóa
    function confirmDelete(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Hành động này sẽ xóa banner vĩnh viễn khỏi hệ thống!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Vâng, Xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit cái form ẩn tương ứng
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

<style>
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25); }
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 12px; border: none; color: #64748b;}
    .premium-table td { border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .hover-lift { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
    .bg-success-soft { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .bg-secondary-soft { background: #f8fafc; border: 1px solid #e2e8f0; }
</style>
@endsection