@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . ($order->order_code ?? $order->id) . ' | PBall Store')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $sum = 0;
    foreach($order->items as $item) {
        $sum += $item->price * $item->quantity;
    }
    
    $statusLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã huỷ',
        'returned' => 'Trả hàng'
    ];

    // LOGIC ĐÓNG BĂNG ĐƠN HÀNG VÀ THANH TOÁN (TÁCH BIỆT)
    $isOrderFrozen = in_array($order->order_status, ['cancelled', 'completed', 'returned']);
    
    $isPaymentFrozen = false;
    if ($order->payment_status == 'refunded') {
        $isPaymentFrozen = true; // Đã hoàn tiền là khóa vĩnh viễn
    }
    if ($order->payment_method == 'cod' && in_array($order->order_status, ['cancelled', 'returned'])) {
        $isPaymentFrozen = true; // COD Hủy/Trả là khóa không cho update thanh toán nữa
    }
@endphp

{{-- KHỐI NÀY SẼ ĐƯỢC AJAX CẬP NHẬT TOÀN BỘ --}}
<div id="orderContentAjax">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none fw-bold mb-2 d-inline-block text-muted">
                <i class="mdi mdi-arrow-left me-1"></i> Quay lại danh sách
            </a>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center">
                Đơn hàng <span class="text-primary ms-2">#{{ $order->order_code ?? $order->id }}</span>
            </h3>
            <div class="d-flex align-items-center mt-2 text-muted small">
                <i class="mdi mdi-calendar-clock me-1"></i> {{ $order->created_at ? $order->created_at->format('H:i - d/m/Y') : 'N/A' }}
                <span class="mx-2">|</span>
                
                {{-- TRẠNG THÁI THANH TOÁN --}}
                @if($order->payment_status == 'paid')
                    <span class="text-success fw-bold"><i class="mdi mdi-check-circle me-1"></i>Đã thanh toán</span>
                @elseif($order->payment_status == 'refunded')
                    <span class="text-secondary fw-bold"><i class="mdi mdi-backup-restore me-1"></i>Đã hoàn tiền</span>
                @else
                    <span class="text-warning fw-bold"><i class="mdi mdi-clock-alert me-1"></i>Chưa thanh toán</span>
                @endif

                <span class="mx-2">-</span>
                
                {{-- PHƯƠNG THỨC THANH TOÁN --}}
                <span class="fw-bold text-uppercase">
                    @if($order->payment_method == 'momo')
                        <span style="color: #a50064;"><i class="mdi mdi-wallet me-1"></i>MoMo</span>
                    @elseif($order->payment_method == 'vnpay')
                        <span class="text-primary"><i class="mdi mdi-credit-card me-1"></i>VNPay</span>
                    @elseif($order->payment_method == 'vietqr')
                        <span class="text-info"><i class="mdi mdi-qrcode me-1"></i>VietQR</span>
                    @else
                        <span class="text-secondary"><i class="mdi mdi-cash me-1"></i>COD</span>
                    @endif
                </span>
            </div>
        </div>
        <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-outline-dark fw-bold">
            <i class="mdi mdi-printer me-1"></i> In vận đơn
        </a>
    </div>

    {{-- ================= THANH TIẾN TRÌNH TRÊN CÙNG ================= --}}
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold text-uppercase mb-0"><i class="mdi mdi-map-marker-path text-primary me-2"></i>Hành trình đơn hàng</h6>
                <span class="badge bg-{{ $order->order_status == 'completed' ? 'success' : ($order->order_status == 'cancelled' ? 'danger' : 'primary') }} px-3 py-2">
                    {{ $statusLabels[$order->order_status] ?? ucfirst($order->order_status) }}
                </span>
            </div>

            <div class="stepper-horizontal">
                @php
                    $steps = ['pending', 'confirmed', 'shipping', 'completed'];
                    $current = $order->order_status;
                    $currentIndex = in_array($current, $steps) ? array_search($current, $steps) : -1;
                @endphp
                <div class="progress-bar-bg">
                    @php
                        $progressWidth = 0;
                        if($currentIndex == 1) $progressWidth = 33;
                        elseif($currentIndex == 2) $progressWidth = 66;
                        elseif($currentIndex == 3) $progressWidth = 100;
                        if(in_array($current, ['cancelled', 'returned'])) $progressWidth = 0;
                    @endphp
                    <div class="progress-bar-fill bg-primary" style="width: {{ $progressWidth }}%;"></div>
                </div>
                
                <div class="step-items d-flex justify-content-between position-relative z-1">
                    @foreach($steps as $index => $step)
                        @php
                            $isPassed = $index <= $currentIndex;
                            $isActive = $index == $currentIndex;
                            $icon = 'mdi-package-variant';
                            if($step == 'confirmed') $icon = 'mdi-clipboard-check-outline';
                            if($step == 'shipping') $icon = 'mdi-truck-fast-outline';
                            if($step == 'completed') $icon = 'mdi-check-decagram';
                            
                            $stateClass = 'step-pending';
                            if (in_array($current, ['cancelled', 'returned'])) {
                                $stateClass = 'step-pending';
                            } else {
                                if ($isPassed) $stateClass = 'step-passed';
                                if ($isActive) $stateClass = 'step-active';
                            }
                        @endphp
                        <div class="step-item text-center {{ $stateClass }}" style="width: 25%;">
                            <div class="step-icon shadow-sm mx-auto d-flex align-items-center justify-content-center bg-white border border-2">
                                <i class="mdi {{ $icon }} fs-4"></i>
                            </div>
                            <p class="mt-2 mb-0 fw-bold small step-text">{{ $statusLabels[$step] ?? ucfirst($step) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ================= BỐ CỤC CHÍNH 8 - 4 ================= --}}
    <div class="row g-4">
        
        {{-- CỘT TRÁI (CHỨA SẢN PHẨM & NHẬT KÝ) --}}
        <div class="col-lg-8">
            
            {{-- BẢNG SẢN PHẨM --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-uppercase mb-0"><i class="mdi mdi-shopping text-primary me-2"></i>Danh sách sản phẩm ({{ $order->items->sum('quantity') }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="text-muted small border-bottom">
                                <tr>
                                    <th class="ps-0 pb-3" style="width: 50%;">SẢN PHẨM</th>
                                    <th class="text-end pb-3">ĐƠN GIÁ</th>
                                    <th class="text-center pb-3">TỒN KHO</th>
                                    <th class="text-center pb-3">SL</th>
                                    <th class="text-end pe-0 pb-3">THÀNH TIỀN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php $line = $item->price * $item->quantity; @endphp
                                    <tr class="border-bottom">
                                        <td class="ps-0 py-3">
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $firstImage = $item->product->images->first();
                                                    $imageUrl = $firstImage ? asset($firstImage->image_path) : 'https://placehold.co/150x150/f8f9fa/adb5bd?text=No+Image';
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $item->product->name ?? '' }}" class="rounded border" style="width: 55px; height: 55px; object-fit: cover; cursor: pointer;" onclick="openImageModal('{{ $imageUrl }}')">
                                                <div class="ms-3">
                                                    <p class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</p>
                                                    @if($item->variant_info)
                                                        <span class="badge bg-light text-dark border"><i class="mdi mdi-tag-outline me-1"></i>{{ $item->variant_info }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end text-muted fw-medium">{{ number_format($item->price) }} đ</td>
                                        <td class="text-center">
                                            @php $stock = $item->variant ? $item->variant->stock : ($item->product->stock ?? 0); @endphp
                                            <span class="badge {{ $stock <= 0 ? 'bg-danger' : 'bg-light text-dark border' }}">{{ $stock }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold bg-light border rounded px-2 py-1 d-inline-block">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="text-end pe-0 fw-bold text-dark">{{ number_format($line) }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- NHẬT KÝ HOẠT ĐỘNG --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-uppercase mb-0"><i class="mdi mdi-history text-primary me-2"></i>Nhật ký hoạt động</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-container">
                        @php $logsArray = $order->statusLogs->sortByDesc('id')->values(); @endphp
                        @forelse($logsArray as $log)
                            <div class="d-flex mb-3 position-relative">
                                <div class="timeline-icon bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3 border" style="width: 40px; height: 40px; z-index: 2;">
                                    <i class="mdi mdi-check-circle-outline fs-5"></i>
                                </div>
                                <div class="timeline-content bg-light p-3 rounded flex-grow-1 border">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold text-dark">Chuyển trạng thái: <span class="text-primary">{{ $statusLabels[$log->status] ?? ucfirst($log->status) }}</span></span>
                                        <span class="text-muted small"><i class="mdi mdi-clock-outline me-1"></i>{{ $log->created_at->format('H:i - d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted py-3">Chưa có dữ liệu nhật ký.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- CỘT PHẢI (CHỨA THÔNG TIN & BẢNG ĐIỀU KHIỂN) --}}
        <div class="col-lg-4">
            
            {{-- BẢNG ĐIỀU KHIỂN NẰM NGANG --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3 border-top border-3 border-primary">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase mb-3"><i class="mdi mdi-tune-vertical text-primary me-2"></i>Bảng điều khiển</h6>
                    
                    <div class="row gx-2">
                        {{-- Select Trạng thái đơn --}}
                        <div class="col-6 mb-2">
                            <label class="small fw-bold text-muted mb-1">Trạng thái đơn</label>
                            <select id="statusSelect" class="form-select custom-select-fix shadow-none fw-semibold" style="font-size: 0.85rem;" {{ $isOrderFrozen ? 'disabled' : '' }}>
                                @foreach(['pending','confirmed','shipping','completed','cancelled'] as $st)
                                    @php
                                        $disabled = 'disabled'; 
                                        if (!$isOrderFrozen) {
                                            if ($st === $current) { 
                                                $disabled = ''; 
                                            } elseif ($st === 'cancelled') {
                                                if ($current !== 'shipping') $disabled = ''; 
                                            } else {
                                                $stIndex = array_search($st, $steps);
                                                if ($stIndex !== false && $stIndex === $currentIndex + 1) $disabled = ''; 
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $st }}" {{ $current==$st?'selected':'' }} {{ $disabled }}>{{ $statusLabels[$st] ?? ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select Thanh toán (ĐÃ FIX LOGIC 1 CHIỀU) --}}
                        <div class="col-6 mb-2">
                            <label class="small fw-bold text-muted mb-1">Thanh toán</label>
                            <select id="paymentStatusSelect" class="form-select custom-select-fix shadow-none fw-semibold" style="font-size: 0.85rem;" {{ $isPaymentFrozen ? 'disabled' : '' }}>
                                
                                <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }} {{ $order->payment_status != 'unpaid' ? 'disabled' : '' }}>⏳ Chưa Thanh Toán</option>
                                
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }} {{ $order->payment_status == 'refunded' ? 'disabled' : '' }}>✅ Đã Thanh Toán</option>
                                
                                {{-- Đơn COD thì ẨN luôn tùy chọn Hoàn tiền --}}
                                @if($order->payment_method != 'cod')
                                    <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>💸 Hoàn Tiền</option>
                                @endif
                                
                            </select>
                        </div>

                        {{-- Nút Lưu Trạng thái --}}
                        <div class="col-6">
                            <button onclick="handleUpdateOrder()" id="btnUpdateOrder" class="btn btn-primary w-100 fw-bold p-2 text-white" style="font-size: 0.8rem;" {{ $isOrderFrozen ? 'disabled' : '' }}>
                                <i class="mdi mdi-content-save-outline me-1"></i>Lưu đơn
                            </button>
                        </div>

                        {{-- Nút Lưu Thanh toán --}}
                        <div class="col-6">
                            <button onclick="updatePayment()" id="btnUpdatePayment" class="btn btn-success w-100 fw-bold p-2 text-white" style="font-size: 0.8rem;" {{ $isPaymentFrozen ? 'disabled' : '' }}>
                                <i class="mdi mdi-cash-check me-1"></i>Lưu trạng thái
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- THÔNG TIN GIAO HÀNG --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase mb-4"><i class="mdi mdi-account-box-outline text-primary me-2"></i>Thông tin giao hàng</h6>
                    
                    <div class="d-flex mb-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-account fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0 fw-bold text-dark">{{ $order->customer_name ?? 'User '.$order->user_id }}</p>
                            <p class="mb-0 text-muted small">Khách hàng</p>
                        </div>
                    </div>

                    <hr class="text-muted">

                    <p class="mb-2"><i class="mdi mdi-phone text-success me-2"></i> <strong>{{ $order->customer_phone ?? 'N/A' }}</strong></p>
                    @if($order->customer_email)
                        <p class="mb-2"><i class="mdi mdi-email-outline text-info me-2"></i> {{ $order->customer_email }}</p>
                    @endif
                    <p class="mb-4 mt-3" style="line-height: 1.5;">
                        <i class="mdi mdi-map-marker-outline text-danger me-2"></i>
                        {{ $order->specific_address ? $order->specific_address . ($order->ward_name ? ', ' . $order->ward_name : '') . ($order->district_name ? ', ' . $order->district_name : '') . ', ' . $order->province_name : 'Chưa có địa chỉ' }}
                    </p>

                    <div class="bg-light p-3 rounded border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Phương thức TT:</span>
                            <span class="fw-bold text-dark text-uppercase">
                                @if($order->payment_method == 'momo')
                                    MoMo
                                @elseif($order->payment_method == 'vnpay')
                                    VNPay
                                @elseif($order->payment_method == 'vietqr')
                                    VietQR
                                @else
                                    Thanh toán khi nhận hàng (COD)
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Tiền hàng:</span>
                            <span class="fw-bold text-dark">{{ number_format($sum) }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Phí vận chuyển:</span>
                            <span class="fw-bold text-dark">{{ number_format($order->shipping_fee ?? 0) }} đ</span>
                        </div>
                        <hr class="my-2 border-secondary">
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold text-dark">TỔNG CỘNG:</span>
                            <span class="fs-5 fw-bold text-danger">{{ number_format($order->total_amount) }} đ</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* FIX MÀU CHỮ SELECT BOX BỊ TÀNG HÌNH */
    .custom-select-fix {
        background-color: #ffffff !important;
        color: #212529 !important;
        border: 1px solid #dee2e6 !important;
    }
    .custom-select-fix:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    .custom-select-fix option {
        color: #212529 !important;
        background-color: #ffffff !important;
        font-weight: 500;
    }
    .custom-select-fix option:disabled {
        color: #adb5bd !important;
    }

    /* CSS CHO STEAMER TIẾN TRÌNH */
    .stepper-horizontal { position: relative; padding: 20px 0; margin-bottom: 10px; }
    .progress-bar-bg { position: absolute; top: 40px; left: 12.5%; width: 75%; height: 4px; background: #e9ecef; border-radius: 4px; z-index: 0; }
    .progress-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
    
    .step-item .step-icon { width: 44px; height: 44px; border-radius: 50%; color: #adb5bd; border-color: #e9ecef !important; transition: all 0.3s ease; }
    .step-item .step-text { color: #adb5bd; }
    
    .step-passed .step-icon { background: #0d6efd !important; color: #fff; border-color: #0d6efd !important; }
    .step-passed .step-text { color: #0d6efd; }
    
    .step-active .step-icon { border-color: #0d6efd !important; color: #0d6efd; box-shadow: 0 0 0 5px rgba(13, 110, 253, 0.2) !important; }
    .step-active .step-text { color: #212529; font-weight: 900 !important; }

    /* CSS TIMELINE NHẬT KÝ */
    .timeline-container::before { content: ''; position: absolute; top: 0; left: 19px; height: 100%; width: 2px; background: #dee2e6; z-index: 1; }

    /* Toast & Modal */
    .toast{ position:fixed; bottom:30px; right:30px; background:#212529; color:#fff; padding:14px 24px; border-radius:8px; opacity:0; transition:all 0.4s; z-index:99999; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transform: translateY(20px);}
    .toast.show{opacity:1; transform: translateY(0);}
    .cancel-modal{ position:fixed; inset:0; background:rgba(0,0,0,0.5); display:none; align-items:center; justify-content:center; z-index:9999; backdrop-filter: blur(3px);}
    .cancel-box{ background:#fff; padding:30px; border-radius:12px; width:350px; text-align:center; animation:popModal .3s ease-out; }
    @keyframes popModal{ from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
    
    /* Image Zoom */
    .image-modal-content { background: transparent; width: auto; max-width: 90vw; padding: 0; position: relative; box-shadow: none;}
    .image-modal-content img { max-height: 80vh; border-radius: 8px; object-fit: contain; background: #fff; padding: 5px;}
    .close-img-btn { position: absolute; top: -15px; right: -15px; width: 35px; height: 35px; border-radius: 50%; background: #fff; color: #dc3545; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer; z-index: 10; }
</style>

<div id="toast" class="toast"><i class="mdi mdi-check-circle me-2"></i>Cập nhật thành công</div>

<script>
function sendAjaxRequest(url, bodyData, successMsg, bgColor, btnId, originalHtml) {
    let btnUp = document.getElementById(btnId);
    
    if(btnUp) {
        btnUp.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Đang lưu...';
        btnUp.disabled = true;
    }

    fetch(url, {
        method: "POST",
        headers: { 
            "Content-Type": "application/json", 
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        },
        body: JSON.stringify(bodyData)
    })
    .then(async response => {
        if (!response.ok) {
            let errorMsg = 'Có lỗi xảy ra!';
            try {
                let errorData = await response.json();
                errorMsg = errorData.error || errorData.message || errorMsg;
            } catch(e) {}
            throw new Error(errorMsg); 
        }
        return response.text(); 
    })
    .then(html => {
        let parser = new DOMParser();
        let doc = parser.parseFromString(html, 'text/html');
        let newContent = doc.getElementById('orderContentAjax');

        if (newContent) {
            document.getElementById('orderContentAjax').innerHTML = newContent.innerHTML;
            showToast(successMsg, bgColor);
        } else { location.reload(); }
    })
    .catch(error => { 
        showToast(error.message, "#dc3545");
        if(btnUp) {
            btnUp.innerHTML = originalHtml;
            btnUp.disabled = false;
        }
    });
}

function updateOrderStatusOnly() {
    let select = document.getElementById('statusSelect');
    if (!select) return;
    let btnOriginal = '<i class="mdi mdi-content-save-outline me-1"></i>Lưu đơn';
    
    sendAjaxRequest("{{ url('/admin/orders/'.$order->id.'/status') }}", { 
        status: select.value
    }, "Cập nhật trạng thái đơn thành công", "#0d6efd", "btnUpdateOrder", btnOriginal);
}

function handleUpdateOrder() {
    let select = document.getElementById('statusSelect');
    if(select && select.value === 'cancelled'){
        document.getElementById('cancelModal').style.display='flex';
        return;
    }
    updateOrderStatusOnly();
}

function confirmCancel() { 
    document.getElementById('cancelModal').style.display='none'; 
    updateOrderStatusOnly(); 
}
function closeCancel(){ 
    document.getElementById('cancelModal').style.display='none'; 
}

function updatePayment() {
    let select = document.getElementById('paymentStatusSelect');
    if (!select) return;
    let btnOriginal = '<i class="mdi mdi-cash-check me-1"></i>Lưu tiền';

    sendAjaxRequest("{{ url('/admin/orders/'.$order->id.'/status') }}", { 
        payment_status: select.value
    }, "Cập nhật thanh toán thành công", "#198754", "btnUpdatePayment", btnOriginal);
}

function openImageModal(imgSrc) {
    document.getElementById('zoomedImage').src = imgSrc;
    document.getElementById('imagePopupModal').style.display = 'flex';
}
function closeImageModal() {
    document.getElementById('imagePopupModal').style.display = 'none';
}

function showToast(msg, bgColor){
    let t = document.getElementById('toast');
    let icon = bgColor === '#dc3545' ? 'mdi-alert-circle' : 'mdi-check-circle';
    t.innerHTML = `<i class="mdi ${icon} me-2"></i>${msg}`;
    t.style.background = bgColor;
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 3000); 
}
</script>

<div id="imagePopupModal" class="cancel-modal" onclick="closeImageModal()">
    <div class="cancel-box image-modal-content" onclick="event.stopPropagation()">
        <button class="close-img-btn" onclick="closeImageModal()"><i class="mdi mdi-close"></i></button>
        <img id="zoomedImage" src="" alt="Zoom">
    </div>
</div>

<div id="cancelModal" class="cancel-modal">
    <div class="cancel-box">
        <i class="mdi mdi-alert text-danger" style="font-size: 50px;"></i>
        <h5 class="fw-bold mb-2">Huỷ đơn hàng?</h5>
        <p class="text-muted small mb-4">Bạn có chắc chắn muốn huỷ đơn hàng này?<br>Thao tác không thể hoàn tác.</p>
        <div class="d-flex gap-2">
            <button class="btn btn-light w-50 fw-bold border" onclick="closeCancel()">Đóng</button>
            <button class="btn btn-danger w-50 fw-bold" onclick="confirmCancel()">Xác nhận</button>
        </div>
    </div>
</div>

@endsection