@extends('admin.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid px-4 py-4 premium-layout" style="background-color: #f4f7f9; font-family: 'Inter', sans-serif; min-height: 100vh;">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                Quản lý Thuộc tính
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Thiết lập phân loại (Màu sắc, Kích cỡ...) cho sản phẩm</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="stats-badge shadow-sm">
                <i class="fa-solid fa-list-check text-primary me-2"></i>
                <span class="text-muted fw-semibold">Tổng số:</span>
                <span class="fs-5 fw-extrabold text-dark ms-2">{{ count($attributes) ?? 0 }}</span> nhóm
            </div>
            
            <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary fw-bold shadow-primary hover-lift px-4 py-2" style="border-radius: 12px;">
                <i class="fa-solid fa-plus me-2"></i> Thêm mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 fade-in-up" style="animation-delay: 0.15s;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 fade-in-up" style="animation-delay: 0.15s;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card premium-card fade-in-up" style="animation-delay: 0.2s;">
        <div class="card-body p-0 mt-2">
            <div class="table-responsive px-4 pb-4 pt-2">
                <table class="table align-middle premium-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Thuộc tính</th>
                            <th style="width: 25%;">Tên Nhóm Phân Loại</th>
                            <th style="width: 45%;">Các Giá Trị (Lựa chọn)</th>
                            <th class="text-end pe-4" style="width: 20%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attributes as $attribute)
                            <tr>
                                <td class="ps-3 py-3"><span class="fw-bold text-dark">#{{ $attribute->id }}</span></td>
                                <td>
                                    <h6 class="fw-bold mb-1 text-primary">{{ $attribute->name }}</h6>
                                    <span class="text-muted small">Đang có <strong>{{ $attribute->values->count() }}</strong> giá trị</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 py-1">
                                        @foreach($attribute->values as $val)
                                            <span class="badge-premium completed shadow-sm" style="transform: scale(0.9); transform-origin: left;">
                                                <span class="status-dot"></span> {{ $val->value }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.attributes.edit', $attribute->id) }}" class="btn btn-light btn-sm fw-bold text-primary border shadow-sm hover-lift px-3">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.attributes.destroy', $attribute->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-light btn-sm fw-bold text-danger border shadow-sm hover-lift px-3 btn-delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-layer-group fs-1 text-muted opacity-25 mb-3"></i>
                                        <h5 class="fw-bold text-dark">Chưa có thuộc tính nào</h5>
                                        <p class="text-muted">Hãy tạo các thuộc tính như Kích cỡ, Màu sắc... để làm biến thể sản phẩm.</p>
                                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-outline-primary fw-bold mt-2" style="border-radius: 10px;">
                                            <i class="fa-solid fa-plus me-1"></i> Thêm thuộc tính đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($attributes, 'links') && $attributes->hasPages())
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    {{ $attributes->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Nếu xóa, các biến thể đang sử dụng thuộc tính này sẽ bị ảnh hưởng. Không thể phục hồi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa ngay',
                    cancelButtonText: 'Hủy bỏ',
                    customClass: { popup: 'rounded-4 shadow-lg', confirmButton: 'btn btn-danger px-4 rounded-3 shadow-sm', cancelButton: 'btn btn-secondary px-4 rounded-3' }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                })
            });
        });
    });
</script>

<style>
    .fw-extrabold { font-weight: 800; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); }
    .premium-card { border-radius: 20px; border: 1px solid rgba(0,0,0,0.03); background: #fff; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); transition: transform 0.3s ease, box-shadow 0.3s ease; }
    
    /* Giao diện số lượng giống hệt Danh mục */
    .stats-badge { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 20px; display: inline-flex; align-items: center; }
    
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    
    /* Giao diện Badge giá trị giống Danh mục */
    .badge-premium { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; border: 1px solid transparent; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }
    .badge-premium.completed { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .badge-premium.completed .status-dot { background: #16a34a; box-shadow: 0 0 6px #16a34a;}
    
    .premium-table { border-collapse: separate; border-spacing: 0 10px; table-layout: fixed !important; width: 100% !important; min-width: 800px !important; }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 10px; border: none; background: transparent; color: #64748b;}
    .premium-table tbody tr { background: #fff; transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 12px; background: #fff;}
    .premium-table td { border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: middle;}
    .premium-table td:first-child { border-left: 1px solid #e2e8f0; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .premium-table td:last-child { border-right: 1px solid #e2e8f0; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    .hover-lift { transition: all 0.2s ease; border-radius: 10px; }
    .hover-lift:hover { transform: translateY(-2px); }
    
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px;}
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection