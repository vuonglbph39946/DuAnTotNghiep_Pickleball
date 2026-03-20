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
                    <i class="fa-solid fa-tags"></i>
                </div>
                Quản lý danh mục
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Phân loại và tổ chức sản phẩm trên hệ thống</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="stats-badge shadow-sm">
                <i class="fa-solid fa-folder-open text-primary me-2"></i>
                <span class="text-muted fw-semibold">Tổng số:</span>
                <span class="fs-5 fw-extrabold text-dark ms-2">{{ $totalCategories }}</span> nhóm
            </div>
            
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary fw-bold shadow-primary hover-lift px-4 py-2" style="border-radius: 12px;">
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
                            <th style="width: 10%;">Danh Mục</th>
                            <th style="width: 15%;">Hình ảnh</th>
                            <th style="width: 35%;">Tên danh mục</th>
                            <th style="width: 20%;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 20%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            {{-- LOGIC CỘNG DỒN SỐ SẢN PHẨM CỦA DANH MỤC CHA VÀ CON --}}
                            @php
                                $totalProductsInParentAndChildren = $category->products->count();
                                if($category->children) {
                                    foreach($category->children as $childCat) {
                                        $totalProductsInParentAndChildren += $childCat->products->count();
                                    }
                                }
                            @endphp

                            {{-- DÒNG HIỂN THỊ DANH MỤC CHA --}}
                            <tr>
                                <td class="ps-3 py-3"><span class="fw-bold text-dark">#{{ $category->id }}</span></td>
                                <td>
                                    @if($category->image)
                                        <div class="product-img-premium shadow-sm popup-trigger" onclick="openImageModal('{{ asset($category->image) }}')">
                                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                                            <div class="eye-overlay"><i class="fa-solid fa-eye"></i></div>
                                        </div>
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px; border: 2px solid #fff;">
                                            <i class="fa-solid fa-image text-muted opacity-50 fs-4"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <h6 class="fw-bold mb-1 text-dark text-primary">{{ $category->name }}</h6>
                                    {{-- ĐÃ SỬA: Hiển thị tổng số lượng sản phẩm --}}
                                    <span class="text-muted small">Đang có <strong>{{ $totalProductsInParentAndChildren }}</strong> sản phẩm</span>
                                </td>
                                <td>
                                    @if($category->status == 1)
                                        <span class="badge-premium completed shadow-sm"><span class="status-dot"></span> Đang hiển thị</span>
                                    @else
                                        <span class="badge-premium pending shadow-sm"><span class="status-dot"></span> Đã ẩn</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-light btn-sm fw-bold text-primary border shadow-sm hover-lift px-3"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-light btn-sm fw-bold text-danger border shadow-sm hover-lift px-3 btn-delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- VÒNG LẶP HIỂN THỊ CÁC DANH MỤC CON NẰM NGAY DƯỚI CHA --}}
                            @foreach($category->children as $child)
                                <tr class="child-row">
                                    <td class="ps-3 py-3"><span class="fw-medium text-muted">#{{ $child->id }}</span></td>
                                    <td>
                                        @if($child->image)
                                            <div class="product-img-premium shadow-sm popup-trigger" style="width: 45px; height: 45px;" onclick="openImageModal('{{ asset($child->image) }}')">
                                                <img src="{{ asset($child->image) }}" alt="{{ $child->name }}">
                                                <div class="eye-overlay" style="font-size: 0.9rem;"><i class="fa-solid fa-eye"></i></div>
                                            </div>
                                        @else
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; border: 2px solid #fff;">
                                                <i class="fa-solid fa-image text-muted opacity-50"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-turn-up fa-rotate-90 text-muted me-2" style="opacity: 0.5;"></i>
                                            <div>
                                                <h6 class="fw-semibold mb-1 text-dark">{{ $child->name }}</h6>
                                                <span class="text-muted" style="font-size: 0.75rem;">Đang có <strong>{{ $child->products->count() }}</strong> sản phẩm</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($child->status == 1)
                                            <span class="badge-premium completed shadow-sm" style="transform: scale(0.9); transform-origin: left;"><span class="status-dot"></span> Đang hiển thị</span>
                                        @else
                                            <span class="badge-premium pending shadow-sm" style="transform: scale(0.9); transform-origin: left;"><span class="status-dot"></span> Đã ẩn</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.categories.edit', $child->id) }}" class="btn btn-light btn-sm fw-bold text-primary border shadow-sm hover-lift px-2" style="padding: 2px 8px;"><i class="fa-solid fa-pen" style="font-size: 0.8rem;"></i></a>
                                            <form action="{{ route('admin.categories.destroy', $child->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-light btn-sm fw-bold text-danger border shadow-sm hover-lift px-2 btn-delete" style="padding: 2px 8px;"><i class="fa-solid fa-trash" style="font-size: 0.8rem;"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-folder-open fs-1 text-muted opacity-25 mb-3"></i>
                                        <h5 class="fw-bold text-dark">Chưa có danh mục nào</h5>
                                        <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-primary fw-bold mt-2" style="border-radius: 10px;">
                                            <i class="fa-solid fa-plus me-1"></i> Tạo danh mục đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="imagePopupModal" class="cancel-modal" onclick="closeImageModal()">
    <div class="cancel-box image-modal-content" onclick="event.stopPropagation()">
        <button class="close-img-btn" onclick="closeImageModal()"><i class="fa-solid fa-xmark"></i></button>
        <img id="zoomedImage" src="" alt="Zoom">
    </div>
</div>

<script>
    function openImageModal(imgSrc) {
        document.getElementById('zoomedImage').src = imgSrc;
        document.getElementById('imagePopupModal').style.display = 'flex';
    }
    function closeImageModal() {
        document.getElementById('imagePopupModal').style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Bạn có chắc chắn muốn xóa danh mục này không?",
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
    .bg-primary-soft { background: #eff6ff; }
    .bg-gradient-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); }
    .premium-card { border-radius: 20px; border: 1px solid rgba(0,0,0,0.03); background: #fff; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .stats-badge { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 20px; display: inline-flex; align-items: center; }
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    .badge-premium { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; border: 1px solid transparent; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }
    .badge-premium.pending { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .badge-premium.pending .status-dot { background: #64748b; }
    .badge-premium.completed { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .badge-premium.completed .status-dot { background: #16a34a; box-shadow: 0 0 6px #16a34a;}
    .premium-table { border-collapse: separate; border-spacing: 0 10px; table-layout: fixed !important; width: 100% !important; min-width: 800px !important; }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 10px; border: none; background: transparent; color: #64748b;}
    .premium-table tbody tr { background: #fff; transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 12px; background: #fff;}
    
    /* Làm mờ và thụt lề cho Danh mục con */
    .child-row { background-color: #fafafa !important; }
    .child-row:hover { background-color: #f1f5f9 !important; }
    
    .premium-table td { border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: middle;}
    .premium-table td:first-child { border-left: 1px solid #e2e8f0; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .premium-table td:last-child { border-right: 1px solid #e2e8f0; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    .hover-lift { transition: all 0.2s ease; border-radius: 10px; }
    .hover-lift:hover { transform: translateY(-2px); }
   .product-img-premium { 
        width: 60px; 
        height: 60px; 
        border-radius: 10px; 
        overflow: hidden; 
        border: 1px solid #e2e8f0; 
        flex-shrink: 0; 
        background: #fff; 
        position: relative; 
        cursor: pointer;
        display: block; /* ĐÃ FIX: Đưa về block thuần, bỏ flexbox để ảnh không bị bóp */
    }

    .product-img-premium img { 
        width: 100% !important; 
        height: 100% !important; 
        object-fit: cover !important; /* ĐÃ FIX: Ép cứng lấp đầy 100% khoảng trắng */
        display: block;
        transition: transform 0.3s ease;
    }
    .eye-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4); display: flex; align-items: center; justify-content: center; color: #fff; opacity: 0; transition: opacity 0.3s ease; font-size: 1.2rem; }
    .product-img-premium:hover img { transform: scale(1.1); }
    .product-img-premium:hover .eye-overlay { opacity: 1; }
    .cancel-modal { position:fixed; inset:0; background:rgba(15, 23, 42, 0.5); display:none; align-items:center; justify-content:center; z-index:9999; backdrop-filter: blur(4px);}
    .cancel-box { background:#fff; padding:35px; border-radius:24px; width:380px; text-align:center; animation:popModal .4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 25px 50px rgba(0,0,0,0.2);}
    @keyframes popModal { from{transform:scale(.9) translateY(20px);opacity:0} to{transform:scale(1) translateY(0);opacity:1} }
    .image-modal-content { background: transparent; box-shadow: none; width: auto; max-width: 450px; padding: 0; position: relative; margin: 0 auto;}
    .image-modal-content img { max-height: 65vh; width: 100%; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); object-fit: contain; background: #fff; padding: 8px;}
    .close-img-btn { position: absolute; top: -15px; right: -15px; width: 40px; height: 40px; border-radius: 50%; background: #fff; color: #ef4444; border: none; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer; transition: transform 0.2s; z-index: 10; }
    .close-img-btn:hover { transform: scale(1.1); background: #fee2e2; }
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px;}
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection