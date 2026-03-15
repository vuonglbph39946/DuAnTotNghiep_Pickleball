@extends('admin.layouts.app')

@section('content')
<div class="container-fluid premium-layout">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-main d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                Quản lý sản phẩm
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Quản lý kho hàng, giá bán và thông tin sản phẩm</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="stats-badge shadow-sm">
                <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>
                <span class="text-muted fw-semibold">Tổng số:</span>
                <span class="fs-5 fw-extrabold text-main ms-2">{{ $products->total() }}</span> <span class="text-main ms-1">SP</span>
            </div>
            
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary fw-bold shadow-primary hover-lift px-4 py-2" style="border-radius: 12px; background: var(--primary); border-color: var(--primary);">
                <i class="fa-solid fa-plus me-2"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 fade-in-up"><i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 fade-in-up"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
    @endif

    {{-- BỘ LỌC VÀ TÌM KIẾM SẢN PHẨM --}}
    <div class="card premium-card border-0 shadow-sm mb-4 fade-in-up" style="animation-delay: 0.15s; background: var(--bg-surface);">
        <div class="card-body p-3">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-body-custom border-custom border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-body-custom border-custom border-start-0 ps-0 fw-medium text-main" placeholder="Tìm tên SP, Mã SKU..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select bg-body-custom border-custom fw-medium text-main">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @if($cat->children->count() > 0)
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>-- {{ $child->name }}</option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select bg-body-custom border-custom fw-medium text-main">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark fw-bold px-4 hover-lift w-100" style="border-radius: 10px;">Lọc</button>
                    @if(request('search') || request('status') != '' || request('category_id'))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light fw-bold text-danger border border-custom hover-lift px-3" style="border-radius: 10px;" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card premium-card fade-in-up border-0" style="animation-delay: 0.2s;">
        <div class="card-body p-0 mt-2">
            <div class="table-responsive px-4 pb-4 pt-2 custom-scrollbar">
                <table class="table align-middle premium-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Mã SP (SKU)</th>
                            <th style="width: 10%;">Hình ảnh</th>
                            <th style="width: 25%;">Tên sản phẩm</th>
                            <th style="width: 15%;">Giá bán</th>
                            <th class="text-center" style="width: 10%;">Tồn kho</th>
                            <th class="text-center" style="width: 15%;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 15%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                {{-- ĐÃ SỬA: IN MÃ SKU RA THAY VÌ MÃ ID --}}
                                <td class="ps-3 py-3">
                                    <span class="fw-bold text-main">{{ $product->sku ?? '#'.$product->id }}</span>
                                </td>

                                {{-- CỘT HÌNH ẢNH --}}
                                <td>
                                    @php 
                                        $imgCount = $product->images->count();
                                        $firstImage = $product->images->first();
                                        $imageUrls = $product->images->pluck('image_path')->map(function($path) { return asset($path); })->toJson();
                                    @endphp
                                    
                                    @if($firstImage)
                                        <div class="product-img-premium shadow-sm position-relative popup-trigger" onclick='openGalleryModal({!! $imageUrls !!})'>
                                            <img src="{{ asset($firstImage->image_path) }}" alt="{{ $product->name }}">
                                            <div class="eye-overlay"><i class="fa-solid fa-images"></i></div>
                                            
                                            @if($imgCount > 1)
                                                <span class="position-absolute bottom-0 end-0 bg-dark text-white rounded-pill px-2 py-1 m-1 border border-secondary" style="font-size: 0.65rem; font-weight: bold; z-index: 2;">
                                                    +{{ $imgCount - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="bg-body-custom rounded-3 d-flex align-items-center justify-content-center border-custom shadow-sm" style="width: 60px; height: 60px;">
                                            <i class="fa-solid fa-image text-muted opacity-50 fs-4"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- CỘT TÊN SẢN PHẨM --}}
                                <td>
                                    <h6 class="fw-bold mb-1 text-main" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $product->name }}
                                    </h6>
                                    <div class="text-muted small mb-2">
                                        <i class="fa-solid fa-tag text-primary opacity-75 me-1"></i> {{ $product->category->name ?? 'Không xác định' }}
                                    </div>
                                    
                                    @if(isset($product->variants) && $product->variants->count() > 0)
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @foreach($product->variants as $variant)
                                                <span class="badge custom-variant-badge border shadow-sm" style="font-size: 0.7rem; padding: 4px 6px;">
                                                    {{ $variant->attributeValues->pluck('value')->implode(' - ') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                {{-- CỘT GIÁ BÁN --}}
                                <td>
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <div class="fw-extrabold text-danger">{{ number_format($product->sale_price, 0, ',', '.') }}đ</div>
                                        <div class="text-muted small text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                    @else
                                        <div class="fw-bold text-main">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                    @endif
                                </td>

                                {{-- CỘT TỒN KHO --}}
                                <td class="text-center">
                                    @if($product->stock <= 0)
                                        <span class="badge bg-danger-soft text-danger fw-bold border border-danger px-2 py-1">Hết hàng</span>
                                    @else
                                        <span class="badge bg-body-custom text-muted border-custom px-2 py-1 fw-bold">{{ $product->stock }}</span>
                                    @endif
                                </td>

                                {{-- CỘT TRẠNG THÁI --}}
                                <td class="text-center">
                                    @if($product->status == 1)
                                        <span class="badge-premium completed shadow-sm"><span class="status-dot"></span> Đang bán</span>
                                    @else
                                        <span class="badge-premium pending shadow-sm"><span class="status-dot"></span> Đã ẩn</span>
                                    @endif
                                </td>

                                {{-- CỘT THAO TÁC --}}
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-action fw-bold text-info border shadow-sm hover-lift px-3">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-action fw-bold text-primary border shadow-sm hover-lift px-3">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-action fw-bold text-danger border shadow-sm hover-lift px-3 btn-delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fa-solid fa-box-open fs-2 opacity-50"></i></div>
                                    <h6 class="text-main fw-bold">Không tìm thấy sản phẩm nào!</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($products, 'links') && $products->hasPages())
                <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ================= CUSTOM MODAL GALLERY ================= --}}
<div id="imagePopupModal" class="cancel-modal" onclick="closeGalleryModal()">
    <div class="cancel-box image-modal-content position-relative" onclick="event.stopPropagation()">
        <button class="close-img-btn" onclick="closeGalleryModal()"><i class="fa-solid fa-xmark"></i></button>
        
        <button id="prevImgBtn" class="nav-img-btn left shadow-sm" onclick="changeImage(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <button id="nextImgBtn" class="nav-img-btn right shadow-sm" onclick="changeImage(1)"><i class="fa-solid fa-chevron-right"></i></button>

        <img id="zoomedImage" src="" alt="Zoom">
        
        <div id="imgCounter" class="img-counter-badge shadow-sm"></div>
    </div>
</div>

{{-- ================= JAVASCRIPT ================= --}}
<script>
    let currentGallery = [];
    let currentImgIndex = 0;

    function openGalleryModal(imagesArray) {
        if (!imagesArray || imagesArray.length === 0) return;
        currentGallery = imagesArray;
        currentImgIndex = 0;
        updateGalleryUI();
        document.getElementById('imagePopupModal').style.display = 'flex';
    }

    function changeImage(step) {
        currentImgIndex += step;
        if(currentImgIndex >= currentGallery.length) currentImgIndex = 0;
        if(currentImgIndex < 0) currentImgIndex = currentGallery.length - 1;
        updateGalleryUI();
    }

    function updateGalleryUI() {
        document.getElementById('zoomedImage').src = currentGallery[currentImgIndex];
        
        if(currentGallery.length > 1) {
            document.getElementById('prevImgBtn').style.display = 'flex';
            document.getElementById('nextImgBtn').style.display = 'flex';
            document.getElementById('imgCounter').style.display = 'block';
            document.getElementById('imgCounter').innerText = (currentImgIndex + 1) + ' / ' + currentGallery.length;
        } else {
            document.getElementById('prevImgBtn').style.display = 'none';
            document.getElementById('nextImgBtn').style.display = 'none';
            document.getElementById('imgCounter').style.display = 'none';
        }
    }

    function closeGalleryModal() {
        document.getElementById('imagePopupModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); const form = this.closest('form');
                Swal.fire({
                    title: 'Xác nhận xóa?', text: "Dữ liệu không thể khôi phục!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Xóa ngay', cancelButtonText: 'Hủy bỏ',
                    customClass: { popup: 'rounded-4 shadow-lg', confirmButton: 'btn btn-danger px-4 rounded-3 shadow-sm', cancelButton: 'btn btn-secondary px-4 rounded-3' },
                    background: document.body.classList.contains('admin-dark-mode') ? '#18181b' : '#fff',
                    color: document.body.classList.contains('admin-dark-mode') ? '#f4f4f5' : '#09090b',
                }).then((result) => { if (result.isConfirmed) form.submit(); })
            });
        });
    });
</script>

{{-- ================= CSS TƯƠNG THÍCH VỚI LAYOUT SAAS & DARK MODE ================= --}}
<style>
    /* Utilities */
    .text-main { color: var(--text-main) !important; }
    .bg-body-custom { background-color: var(--bg-body) !important; }
    .border-custom { border-color: var(--border-color) !important; }
    
    .fw-extrabold { font-weight: 800; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, var(--primary), var(--primary-hover)); }
    .shadow-primary { box-shadow: 0 8px 20px var(--primary-glow); }

    /* Top Stats Badge */
    .stats-badge { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 10px 20px; display: inline-flex; align-items: center; }

    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Nút Thao tác trong bảng */
    .btn-action { background: var(--bg-surface); color: var(--text-main); border-color: var(--border-color); transition: var(--transition); }
    .btn-action:hover { background: var(--bg-body); border-color: var(--border-color); }

    /* Custom Variants Badge */
    .custom-variant-badge { background: var(--bg-body); color: var(--text-main); border-color: var(--border-color) !important; }

    /* Status Badges */
    .badge-premium { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }
    
    .badge-premium.pending { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    .badge-premium.pending .status-dot { background: #64748b; }
    
    .badge-premium.completed { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
    .badge-premium.completed .status-dot { background: #16a34a; box-shadow: 0 0 6px rgba(22, 163, 74, 0.5); }

    /* Premium Table - Tương thích Dark/Light Mode */
    .premium-table { border-collapse: separate; border-spacing: 0 10px; table-layout: fixed !important; width: 100% !important; min-width: 900px !important; }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 10px; border: none; background: transparent; color: var(--text-muted);}
    .premium-table tbody tr { background: var(--bg-surface); transition: var(--transition); border-radius: var(--radius-md); }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); background: var(--bg-surface); }
    
    .premium-table td { border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    .premium-table td:first-child { border-left: 1px solid var(--border-color); border-top-left-radius: var(--radius-md); border-bottom-left-radius: var(--radius-md); }
    .premium-table td:last-child { border-right: 1px solid var(--border-color); border-top-right-radius: var(--radius-md); border-bottom-right-radius: var(--radius-md); }

    .hover-lift { transition: var(--transition); border-radius: 10px; }
    .hover-lift:hover { transform: translateY(-2px); }

    /* Product Thumbnail */
    .product-img-premium { width: 60px; height: 60px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0; background: var(--bg-surface); position: relative; cursor: pointer; display: inline-block;}
    .product-img-premium img { width: 100%; height: 100%; object-fit: cover; transition: var(--transition); }
    .eye-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; color: #fff; opacity: 0; transition: opacity 0.3s ease; font-size: 1.2rem; }
    .product-img-premium:hover img { transform: scale(1.1); }
    .product-img-premium:hover .eye-overlay { opacity: 1; }

    /* Modal Gallery */
    .cancel-modal { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(5px);}
    .cancel-box { background: transparent; padding: 0; text-align: center; animation: popModal 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    @keyframes popModal { from { transform: scale(.9) translateY(20px); opacity: 0 } to { transform: scale(1) translateY(0); opacity: 1 } }
    
    .image-modal-content { max-width: 650px; position: relative; margin: 0 auto; }
    .image-modal-content img { max-height: 75vh; width: 100%; border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.5); object-fit: contain; background: var(--bg-surface); padding: 8px; border: 1px solid var(--border-color); }
    
    .close-img-btn { position: absolute; top: -15px; right: -15px; width: 40px; height: 40px; border-radius: 50%; background: #ef4444; color: #fff; border: 2px solid var(--bg-surface); font-size: 1.2rem; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); cursor: pointer; transition: transform 0.2s; z-index: 10; }
    .close-img-btn:hover { transform: scale(1.1); background: #dc2626; }
    
    .nav-img-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-color); font-size: 1.2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; z-index: 10; box-shadow: var(--shadow-sm); }
    .nav-img-btn:hover { background: var(--bg-body); transform: translateY(-50%) scale(1.1); }
    .nav-img-btn.left { left: -20px; }
    .nav-img-btn.right { right: -20px; }
    
    .img-counter-badge { position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%); background: var(--bg-surface); color: var(--text-main); padding: 4px 12px; border-radius: 50px; font-size: 0.85rem; font-weight: bold; border: 1px solid var(--border-color); z-index: 10; box-shadow: var(--shadow-sm);}
</style>
@endsection