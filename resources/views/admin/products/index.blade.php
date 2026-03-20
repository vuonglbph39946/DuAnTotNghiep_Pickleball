@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm | PBall Store')

@section('content')
<div class="container-fluid premium-layout">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-main d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md text-white shadow-primary me-3" style="background: #4b49ac;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                Quản lý sản phẩm
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Quản lý kho hàng, giá bán và thông tin sản phẩm</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="stats-badge shadow-sm bg-white">
                <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>
                <span class="text-muted fw-semibold">Tổng số:</span>
                <span class="fs-5 fw-extrabold text-main ms-2">{{ $products->total() }}</span> <span class="text-main ms-1">SP</span>
            </div>
            
            {{-- ĐÃ FIX LỖI: Chữ mờ của nút Thêm sản phẩm --}}
            <a href="{{ route('admin.products.create') }}" class="btn text-white fw-bold shadow-primary hover-lift px-4 py-2" style="border-radius: 12px; background-color: #4b49ac !important; border: none;">
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

    {{-- =============================================== --}}
    {{-- BỘ LỌC VÀ TÌM KIẾM REALTIME                     --}}
    {{-- =============================================== --}}
    {{-- ĐÃ FIX LỖI: Thêm position: relative và z-index: 100 để dropdown không bị bảng đè lên --}}
    <div class="card premium-card border-0 shadow-sm mb-4 fade-in-up bg-white" style="animation-delay: 0.15s; position: relative; z-index: 100;">
        <div class="card-body p-3">
            <form action="{{ route('admin.products.index') }}" method="GET" id="filterForm">
                <div class="row gx-2 gy-3 align-items-center">
                    
                    {{-- Ô Tìm kiếm Realtime --}}
                    <div class="col-md-5 position-relative">
                        <div class="input-group custom-search-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" id="searchInput" class="form-control bg-white border-start-0 ps-0 fw-medium text-dark" placeholder="Tìm tên SP, Mã SKU..." value="{{ request('search') }}" autocomplete="off" style="border-color: #dee2e6;">
                        </div>
                        
                        {{-- Hộp chứa gợi ý Realtime --}}
                        <div id="searchSuggestions" class="position-absolute w-100 bg-white border rounded shadow d-none" style="z-index: 9999; top: calc(100% + 5px); max-height: 350px; overflow-y: auto;">
                            {{-- Gợi ý sẽ được AJAX đổ vào đây --}}
                        </div>
                    </div>

                    {{-- Lọc Danh mục --}}
                    <div class="col-md-3">
                        <select name="category_id" class="form-select bg-white fw-medium text-dark" onchange="this.form.submit()" style="border-color: #dee2e6; height: 44px;">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @if($cat->children->count() > 0)
                                    @foreach($cat->children as $child)
                                        <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Lọc Trạng thái --}}
                    <div class="col-md-3">
                        <select name="status" class="form-select bg-white fw-medium text-dark" onchange="this.form.submit()" style="border-color: #dee2e6; height: 44px;">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang bán</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã ẩn</option>
                        </select>
                    </div>

                    {{-- Nút Reset --}}
                    <div class="col-md-1">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light w-100 border p-2 text-center text-dark hover-lift" title="Làm mới" style="height: 44px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-rotate-left fs-5 mx-0"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- =============================================== --}}
    {{-- BẢNG DANH SÁCH SẢN PHẨM                         --}}
    {{-- =============================================== --}}
    <div class="card premium-card fade-in-up border-0 bg-white" style="animation-delay: 0.2s; position: relative; z-index: 10;">
        <div class="card-body p-0 mt-2">
            <div class="table-responsive px-4 pb-4 pt-2 custom-scrollbar">
                <table class="table align-middle premium-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Mã SP (SKU)</th>
                            <th style="width: 10%;">Hình ảnh</th>
                            <th style="width: 23%;">Tên sản phẩm</th>
                            <th style="width: 15%;">Giá bán</th>
                            <th class="text-center" style="width: 10%;">Tồn kho</th>
                            <th class="text-center" style="width: 15%;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 15%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                {{-- CỘT 1: SKU --}}
                                <td class="ps-3 py-3">
                                    <span class="fw-bold text-dark">{{ $product->sku ?? '#'.$product->id }}</span>
                                </td>

                                {{-- CỘT 2: HÌNH ẢNH (ĐÃ FIX CSS CĂN CHỈNH) --}}
                                <td>
                                    @php 
                                        $imgCount = $product->images->count();
                                        $firstImage = $product->images->first();
                                        $imageUrls = $product->images->pluck('image_path')->map(function($path) { return asset($path); })->toJson();
                                    @endphp
                                    
                                    @if($firstImage)
                                        <div class="product-img-premium shadow-sm position-relative popup-trigger" onclick='openGalleryModal({!! $imageUrls !!})' style="width: 65px; height: 65px; border-radius: 12px; border: 1px solid #dee2e6; cursor: pointer; display: inline-block; background: #fff;">
                                            <img src="{{ asset($firstImage->image_path) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 11px;">
                                            
                                            {{-- Lớp màng đen khi trỏ chuột --}}
                                            <div class="eye-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: #fff; opacity: 0; transition: 0.3s; border-radius: 11px;">
                                                <i class="fa-solid fa-images fs-5"></i>
                                            </div>
                                            
                                            {{-- Số lượng ảnh thừa --}}
                                            @if($imgCount > 1)
                                                <span class="position-absolute bg-dark text-white rounded-pill px-2 shadow-sm" style="font-size: 0.7rem; font-weight: bold; z-index: 2; bottom: -5px; right: -5px; border: 2px solid #fff; padding-top: 2px; padding-bottom: 2px;">
                                                    +{{ $imgCount - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border shadow-sm" style="width: 65px; height: 65px;">
                                            <i class="fa-solid fa-image text-muted opacity-50 fs-4"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- CỘT 3: TÊN SẢN PHẨM --}}
                                <td>
                                    <h6 class="fw-bold mb-1 text-dark product-name-col" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4;">
                                        {{ $product->name }}
                                    </h6>
                                    <div class="text-muted small mb-2">
                                        <i class="fa-solid fa-tag text-primary opacity-75 me-1"></i> {{ $product->category->name ?? 'Không xác định' }}
                                    </div>
                                    
                                    @if(isset($product->variants) && $product->variants->count() > 0)
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @foreach($product->variants as $variant)
                                                <span class="badge bg-light text-dark border shadow-sm" style="font-size: 0.7rem; padding: 4px 6px;">
                                                    {{ $variant->attributeValues->pluck('value')->implode(' - ') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                {{-- CỘT 4: GIÁ BÁN --}}
                                <td>
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <div class="fw-extrabold text-danger product-price-col">{{ number_format($product->sale_price, 0, ',', '.') }}đ</div>
                                        <div class="text-muted small text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                    @else
                                        <div class="fw-bold text-dark product-price-col">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                    @endif
                                </td>

                                {{-- CỘT 5: TỒN KHO --}}
                                <td class="text-center">
                                    @if($product->stock <= 0)
                                        <span class="badge bg-danger text-white fw-bold px-2 py-1">Hết hàng</span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold">{{ $product->stock }}</span>
                                    @endif
                                </td>

                                {{-- CỘT 6: TRẠNG THÁI --}}
                                <td class="text-center">
                                    @if($product->status == 1)
                                        <span class="badge-premium completed shadow-sm" style="background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2);"><span class="status-dot" style="background: #16a34a;"></span> Đang bán</span>
                                    @else
                                        <span class="badge-premium pending shadow-sm" style="background: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.2);"><span class="status-dot" style="background: #64748b;"></span> Đã ẩn</span>
                                    @endif
                                </td>

                                {{-- CỘT 7: THAO TÁC --}}
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
                            <tr class="empty-state-row">
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fa-solid fa-box-open fs-2 opacity-50"></i></div>
                                    <h6 class="text-dark fw-bold">Không tìm thấy sản phẩm nào!</h6>
                                    <p class="text-muted small">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc.</p>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // ----------------------------------------------------
    // LOGIC TÌM KIẾM REALTIME SẢN PHẨM
    // ----------------------------------------------------
    $(document).ready(function() {
        let searchTimer;
        
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimer);
            let query = $(this).val().trim();
            let suggestBox = $('#searchSuggestions');
            
            if (query.length >= 2) {
                suggestBox.removeClass('d-none').html('<div class="p-3 text-center text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tìm kiếm...</div>');
                
                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('admin.products.index') }}",
                        data: { search: query },
                        success: function(res) {
                            let suggestionsHTML = '';
                            let validRows = 0;
                            
                            $(res).find('.premium-table tbody tr').each(function() {
                                if ($(this).hasClass('empty-state-row')) return; 
                                
                                if (validRows < 5) {
                                    let sku = $(this).find('td:eq(0) span').text().trim();
                                    let imgSrc = $(this).find('td:eq(1) img').attr('src');
                                    let imgHtml = imgSrc 
                                        ? `<img src="${imgSrc}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">` 
                                        : `<div style="width: 45px; height: 45px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #eee;"><i class="fa-solid fa-image text-muted opacity-50"></i></div>`;
                                    
                                    let name = $(this).find('td:eq(2) .product-name-col').text().trim();
                                    let price = $(this).find('td:eq(3) .product-price-col').text().trim();
                                    
                                    suggestionsHTML += `
                                        <div class="p-3 border-bottom suggestion-item d-flex align-items-center" style="cursor: pointer; transition: 0.2s;" onclick="submitSearch('${sku.replace('#', '')}')">
                                            <div class="me-3">${imgHtml}</div>
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.9rem;">${name}</div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-barcode me-1"></i>${sku}</span>
                                                    <span class="text-danger fw-bold" style="font-size: 0.85rem;">${price}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    validRows++;
                                }
                            });
                            
                            if (suggestionsHTML !== '') {
                                suggestBox.html(suggestionsHTML);
                            } else {
                                suggestBox.html('<div class="p-4 text-center text-danger"><i class="fa-solid fa-circle-xmark fs-4 d-block mb-1"></i> Không tìm thấy sản phẩm nào!</div>');
                            }
                        },
                        error: function() {
                            suggestBox.html('<div class="p-3 text-center text-muted">Lỗi kết nối khi tải gợi ý</div>');
                        }
                    });
                }, 400); 
            } else {
                suggestBox.addClass('d-none');
            }
        });

        window.submitSearch = function(val) {
            $('#searchInput').val(val);
            $('#searchSuggestions').addClass('d-none');
            $('#filterForm').submit();
        };

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.position-relative').length) {
                $('#searchSuggestions').addClass('d-none');
            }
        });
    });

    // ----------------------------------------------------
    // LOGIC POPUP GALLERY ẢNH
    // ----------------------------------------------------
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

    // ----------------------------------------------------
    // LOGIC XÓA SẢN PHẨM
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); const form = this.closest('form');
                Swal.fire({
                    title: 'Xác nhận xóa?', text: "Dữ liệu không thể khôi phục!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Xóa ngay', cancelButtonText: 'Hủy bỏ',
                    customClass: { popup: 'rounded-4 shadow-lg', confirmButton: 'btn btn-danger px-4 rounded-3 shadow-sm', cancelButton: 'btn btn-secondary px-4 rounded-3' }
                }).then((result) => { if (result.isConfirmed) form.submit(); })
            });
        });
    });
</script>

{{-- ================= CSS TƯƠNG THÍCH VỚI LAYOUT ================= --}}
<style>
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .shadow-primary { box-shadow: 0 8px 20px rgba(75, 73, 172, 0.3); }

    .stats-badge { border: 1px solid #dee2e6; border-radius: 12px; padding: 10px 20px; display: inline-flex; align-items: center; }

    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    .btn-action { background: #fff; color: #212529; transition: 0.3s; }
    .btn-action:hover { background: #f8f9fa; }

    .badge-premium { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }

    .premium-table { border-collapse: separate; border-spacing: 0 10px; table-layout: fixed !important; width: 100% !important; min-width: 900px !important; }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 10px; border: none; background: transparent; color: #6c757d;}
    .premium-table tbody tr { background: #fff; transition: 0.3s; border-radius: 12px; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    
    .premium-table td { border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
    .premium-table td:first-child { border-left: 1px solid #dee2e6; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .premium-table td:last-child { border-right: 1px solid #dee2e6; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .hover-lift { transition: 0.3s; }
    .hover-lift:hover { transform: translateY(-2px); }

    /* CSS GỢI Ý TÌM KIẾM */
    .custom-search-group .form-control:focus { border-color: #4b49ac !important; box-shadow: 0 0 0 0.2rem rgba(75, 73, 172, 0.25) !important; }
    .custom-search-group .form-control:focus + .input-group-text { border-color: #4b49ac !important; }
    .suggestion-item:hover { background-color: #f8f9fa; }

    /* Modal Gallery */
    .product-img-premium:hover .eye-overlay { opacity: 1 !important; }
    .cancel-modal { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.85); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(5px);}
    .cancel-box { background: transparent; padding: 0; text-align: center; animation: popModal 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    @keyframes popModal { from { transform: scale(.9) translateY(20px); opacity: 0 } to { transform: scale(1) translateY(0); opacity: 1 } }
    
    .image-modal-content { max-width: 650px; position: relative; margin: 0 auto; }
    .image-modal-content img { max-height: 75vh; width: 100%; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); object-fit: contain; background: #fff; padding: 8px; border: 1px solid #dee2e6; }
    
    .close-img-btn { position: absolute; top: -15px; right: -15px; width: 40px; height: 40px; border-radius: 50%; background: #ef4444; color: #fff; border: 2px solid #fff; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0,0,0,0.2); cursor: pointer; transition: transform 0.2s; z-index: 10; }
    .close-img-btn:hover { transform: scale(1.1); background: #dc2626; }
    
    .nav-img-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; background: #fff; color: #212529; border: 1px solid #dee2e6; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; z-index: 10; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .nav-img-btn:hover { background: #f8f9fa; transform: translateY(-50%) scale(1.1); }
    .nav-img-btn.left { left: -20px; }
    .nav-img-btn.right { right: -20px; }
    
    .img-counter-badge { position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%); background: #fff; color: #212529; padding: 4px 12px; border-radius: 50px; font-size: 0.85rem; font-weight: bold; border: 1px solid #dee2e6; z-index: 10; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
</style>
@endsection