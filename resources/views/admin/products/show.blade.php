@extends('admin.layouts.app')

@section('content')
<div class="container-fluid premium-layout">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-main d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3">
                    <i class="fa-solid fa-box"></i>
                </div>
                Chi tiết Sản phẩm
            </h2>
            {{-- ĐÃ SỬA: HIỂN THỊ MÃ SKU GỐC Ở ĐÂY --}}
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Mã SP (SKU): <strong class="text-primary text-uppercase">{{ $product->sku ?? '#' . $product->id }}</strong></p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-action fw-bold shadow-sm hover-lift px-4 py-2 border" style="border-radius: 12px;">
                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
            </a>
            <a href="{{ route('admin.products.edit', $product->id) }}"
   class="btn fw-bold shadow-primary hover-lift px-4 py-2"
   style="border-radius: 12px; background: #0d3b66; border-color: #0d3b66; color: #fff;">
    <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
</a>
        </div>
    </div>

    <div class="row g-4">
        {{-- CỘT TRÁI: THÔNG TIN VÀ BIẾN THỂ --}}
        <div class="col-lg-8 fade-in-up" style="animation-delay: 0.2s;">
            
            {{-- THÔNG TIN CƠ BẢN --}}
            <div class="card premium-card border-0 shadow-sm mb-4 p-2">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4" style="border-color: var(--border-color) !important;">
                        <div>
                            <h4 class="fw-extrabold text-main mb-1">{{ $product->name }}</h4>
                            
                            {{-- ĐÁNH GIÁ SAO (RATING) --}}
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2" style="font-size: 0.95rem;">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <span class="text-muted fw-bold" style="font-size: 0.85rem;">(4.8/5 - 128 Đánh giá)</span>
                            </div>

                            <span class="badge bg-body-custom text-primary border border-primary-subtle px-3 py-2 fw-semibold" style="font-size: 0.85rem; border-color: var(--primary-light) !important;">
                                <i class="fa-solid fa-folder-tree me-1"></i> {{ $product->category->name ?? 'Không xác định' }}
                            </span>
                            <span class="badge {{ $product->status == 1 ? 'badge-success-custom' : 'badge-secondary-custom' }} border px-3 py-2 fw-semibold ms-2" style="font-size: 0.85rem;">
                                @if($product->status == 1) <i class="fa-solid fa-circle-check me-1"></i> Đang bán @else <i class="fa-solid fa-eye-slash me-1"></i> Đã ẩn @endif
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted small fw-bold mb-1 text-uppercase tracking-wide">Giá bán gốc</p>
                            <h3 class="fw-extrabold text-main mb-0">
                                {{ number_format($product->price, 0, ',', '.') }}<span class="text-muted fs-5">đ</span>
                            </h3>
                            @if($product->sale_price)
                                <p class="text-danger fw-bold small mt-1 mb-0"><i class="fa-solid fa-arrow-trend-down me-1"></i> Khuyến mãi: {{ number_format($product->sale_price, 0, ',', '.') }}đ</p>
                            @endif
                        </div>
                        <div class="col-md-6 border-start ps-4" style="border-color: var(--border-color) !important;">
                            <p class="text-muted small fw-bold mb-1 text-uppercase tracking-wide">Tổng tồn kho</p>
                            <h3 class="fw-extrabold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }} mb-0">
                                {{ $product->stock }} <span class="text-muted fs-5">sản phẩm</span>
                            </h3>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
                        <p class="text-muted small fw-bold mb-2 text-uppercase tracking-wide">Mô tả sản phẩm</p>
                        <div class="bg-body-custom p-4 rounded-4 text-main" style="font-size: 0.95rem; line-height: 1.6; border: 1px solid var(--border-color);">
                            {!! nl2br(e($product->description)) ?: '<span class="text-muted fst-italic">Không có mô tả...</span>' !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUẢN LÝ BIẾN THỂ --}}
            @if($product->variants->count() > 0)
                <div class="card premium-card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-extrabold text-main mb-0"><i class="fa-solid fa-layer-group text-warning me-2"></i>Danh sách Phân loại hàng</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive rounded-4 overflow-hidden shadow-sm border-custom custom-scrollbar" style="border: 1px solid var(--border-color);">
                            <table class="table align-middle mb-0 premium-table-inner">
                                <thead style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-4 py-3 text-muted">Loại</th>
                                        <th class="text-muted text-center">Mã SKU</th>
                                        <th class="text-muted text-end">Giá bán</th>
                                        <th class="text-center pe-4 text-muted">Kho / Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td class="fw-bold text-main ps-4">
                                                {{ $variant->attributeValues->pluck('value')->implode(' - ') }}
                                            </td>
                                            {{-- ĐÃ SỬA: LÀM NỔI BẬT MÃ SKU BẰNG BADGE --}}
                                            <td class="text-center">
                                                <span class="badge bg-body-custom border border-custom text-main shadow-sm text-uppercase" style="font-size: 0.75rem;">
                                                    {{ $variant->sku ?: 'CHƯA CÓ MÃ' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-primary text-end">{{ number_format($variant->price, 0, ',', '.') }}đ</td>
                                            <td class="text-center pe-4">
                                                @if($variant->stock == 0)
                                                    <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> Hết hàng</span>
                                                @elseif($variant->stock <= 5)
                                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-bell me-1"></i> Sắp hết ({{ $variant->stock }})</span>
                                                @else
                                                    <span class="badge bg-success-soft text-success border border-success px-3 py-2 rounded-pill">{{ $variant->stock }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- CỘT PHẢI: THỐNG KÊ & HÌNH ẢNH --}}
        <div class="col-lg-4 fade-in-up" style="animation-delay: 0.3s;">
            
            

            {{-- THƯ VIỆN HÌNH ẢNH --}}
            <div class="card premium-card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-main mb-4 border-bottom pb-3" style="border-color: var(--border-color) !important;"><i class="fa-solid fa-images text-info me-2"></i>Hình ảnh sản phẩm</h6>
                    
                    @if($product->images->count() > 0)
                        @php 
                            $imageUrls = $product->images->pluck('image_path')->map(function($path) { return asset($path); })->toJson();
                        @endphp
                        
                        {{-- ẢNH CHÍNH --}}
                        <div class="mb-3 rounded-4 overflow-hidden shadow-sm border text-center bg-body-custom p-2 position-relative product-img-premium popup-trigger border-custom" onclick='openGalleryModal({!! $imageUrls !!}, 0)'>
                            <img src="{{ asset($product->images->first()->image_path) }}" class="img-fluid rounded-3" style="max-height: 250px; object-fit: contain;">
                            <div class="eye-overlay rounded-4"><i class="fa-solid fa-eye fs-1"></i></div>
                        </div>
                        
                        {{-- ẢNH THUMBNAILS --}}
                        @if($product->images->count() > 1)
                            <div class="row g-2">
                                @foreach($product->images->skip(1) as $index => $img)
                                    <div class="col-4">
                                        <div class="rounded-3 overflow-hidden border shadow-sm h-100 bg-body-custom d-flex align-items-center justify-content-center position-relative product-img-premium popup-trigger border-custom" onclick='openGalleryModal({!! $imageUrls !!}, {{ $index + 1 }})'>
                                            <img src="{{ asset($img->image_path) }}" class="img-fluid" style="height: 70px; width: 100%; object-fit: cover;">
                                            <div class="eye-overlay rounded-3"><i class="fa-solid fa-eye fs-4"></i></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="text-center p-5 bg-body-custom rounded-4 border border-dashed border-custom">
                            <i class="fa-solid fa-image-polaroid fs-1 text-muted opacity-25 mb-2"></i>
                            <p class="text-muted fw-bold mb-0">Chưa có hình ảnh</p>
                        </div>
                    @endif
                </div>
            </div>
            
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

<script>
    let currentGallery = [];
    let currentImgIndex = 0;

    function openGalleryModal(imagesArray, startIndex = 0) {
        if (!imagesArray || imagesArray.length === 0) return;
        currentGallery = imagesArray;
        currentImgIndex = startIndex;
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
</script>

<style>
    /* Utilities */
    .text-main { color: var(--text-main) !important; }
    .bg-body-custom { background-color: var(--bg-body) !important; }
    .border-custom { border-color: var(--border-color) !important; border: 1px solid var(--border-color) !important;}
    
    .fw-extrabold { font-weight: 800; }
    .tracking-wide { letter-spacing: 0.5px; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-gradient-primary { background: linear-gradient(135deg, var(--primary), var(--primary-hover)); }
    
    /* Stats Card Custom */
    .stats-card-custom { background-color: var(--bg-surface) !important; border: 1px solid var(--primary-light) !important; box-shadow: 0 10px 30px var(--primary-light) !important; }
    body.admin-dark-mode .stats-card-custom { border-color: var(--border-color) !important; box-shadow: none !important;}

    /* Badges Custom */
    .badge-success-custom { background-color: rgba(34, 197, 94, 0.1); color: #16a34a; border-color: rgba(34, 197, 94, 0.2) !important; }
    .badge-secondary-custom { background-color: rgba(100, 116, 139, 0.1); color: #64748b; border-color: var(--border-color) !important; }
    
    .premium-card { border-radius: 20px; background: var(--bg-surface); transition: var(--transition); }
    .premium-card:hover { box-shadow: var(--shadow-float) !important; }
    
    .fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    
    /* Buttons */
    .btn-action { background: var(--bg-surface); color: var(--text-main); border-color: var(--border-color); transition: var(--transition); }
    .btn-action:hover { background: var(--bg-body); border-color: var(--border-color); color: var(--text-main);}
    .hover-lift { transition: var(--transition); }
    .hover-lift:hover { transform: translateY(-3px); }

    /* Bảng Biến thể bên trong */
    .premium-table-inner { background: var(--bg-surface); }
    .premium-table-inner th { background: var(--bg-body) !important; border-bottom: 1px solid var(--border-color); padding: 12px; }
    .premium-table-inner td { border-bottom: 1px solid var(--border-color); padding: 10px; vertical-align: middle;}

    /* Hiệu ứng Ảnh & Modal */
    .product-img-premium { cursor: pointer; display: block; overflow: hidden; }
    .product-img-premium img { transition: transform 0.3s ease; width: 100%; height: 100%;}
    .product-img-premium:hover img { transform: scale(1.05); }
    .eye-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; color: #fff; opacity: 0; transition: all 0.3s ease; backdrop-filter: blur(2px); }
    .product-img-premium:hover .eye-overlay { opacity: 1; }

    /* CSS CHO MODAL GALLERY */
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