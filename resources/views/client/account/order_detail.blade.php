@extends('client.layouts.app')
@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')

@php
    // BẢNG DỊCH TRẠNG THÁI & MÀU SẮC & ICON CHO TIMELINE (GIỮ NGUYÊN LOGIC)
    $statusMap = [
        'pending' => ['label' => 'Chờ xác nhận', 'color' => 'warning text-dark', 'icon' => 'fa-clock-rotate-left'],
        'confirmed' => ['label' => 'Đã xác nhận', 'color' => 'info', 'icon' => 'fa-clipboard-check'],
        'shipping' => ['label' => 'Đang giao hàng', 'color' => 'primary', 'icon' => 'fa-truck-fast'],
        'completed' => ['label' => 'Hoàn thành', 'color' => 'success', 'icon' => 'fa-box-open'],
        'cancelled' => ['label' => 'Đã huỷ', 'color' => 'danger', 'icon' => 'fa-xmark-circle'],
        'returned' => ['label' => 'Trả hàng', 'color' => 'secondary', 'icon' => 'fa-arrow-rotate-left'],
    ];
@endphp

{{-- MAIN WRAPPER MÀU NỀN XÁM SÁNG --}}
<div class="main-wrapper pb-5">
    <div class="container p-t-40 p-b-20">
        
        {{-- BREADCRUMB --}}
        <div class="bread-crumb d-flex align-items-center p-b-20">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none hov-cl1 transition-all me-2">Trang chủ <i class="fa fa-angle-right ms-1"></i></a>
            <a href="{{ route('account.index') }}" class="text-muted text-decoration-none hov-cl1 transition-all me-2">Tài khoản <i class="fa fa-angle-right ms-1"></i></a>
            <span class="text-primary fw-bold">Chi tiết đơn hàng #{{ $order->order_code }}</span>
        </div>

        <div class="row g-4">
            
            {{-- ================= CỘT TRÁI (8) ================= --}}
            <div class="col-lg-8">
                
                {{-- KHỐI 1: TIMELINE GIAO HÀNG --}}
                <div class="card card-modern mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="section-title mb-4">Tiến trình giao hàng</h5>
                        
                        <div class="timeline-modern">
                            @foreach($order->statusLogs as $log)
                                @php 
                                    $mapped = $statusMap[$log->status] ?? ['label' => ucfirst($log->status), 'color' => 'dark', 'icon' => 'fa-circle-dot']; 
                                @endphp
                                <div class="timeline-modern-item d-flex align-items-start">
                                    <div class="timeline-time text-muted mt-1 me-4">
                                        <div class="fw-medium text-end" style="font-size: 14px;">{{ $log->created_at->format('d/m/Y') }}</div>
                                        <div class="text-end" style="font-size: 13px;">{{ $log->created_at->format('H:i') }}</div>
                                    </div>
                                    <div class="timeline-marker bg-{{ str_replace(' text-dark', '', $mapped['color']) }} border-0 shadow-sm"></div>
                                    <div class="timeline-content ms-4 pb-4">
                                        <h6 class="fw-bold mb-1 text-{{ str_replace(' text-dark', '', $mapped['color']) }}" style="font-size: 16px;">{{ $mapped['label'] }}</h6>
                                        @if($log->status == 'pending')
                                            <p class="text-muted mb-0" style="font-size: 14px;">Đơn hàng đã được tạo thành công trên hệ thống.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- HIỂN THỊ MỐC HOÀN TIỀN NẾU ADMIN ĐÃ BẤM HOÀN TIỀN --}}
                            @if($order->payment_status == 'refunded')
                                <div class="timeline-modern-item d-flex align-items-start">
                                    <div class="timeline-time text-muted mt-1 me-4">
                                        <div class="fw-medium text-end" style="font-size: 14px;">{{ $order->updated_at->format('d/m/Y') }}</div>
                                        <div class="text-end" style="font-size: 13px;">{{ $order->updated_at->format('H:i') }}</div>
                                    </div>
                                    <div class="timeline-marker bg-warning border-0 shadow-sm"></div>
                                    <div class="timeline-content ms-4">
                                        <h6 class="fw-bold text-warning text-dark mb-1" style="font-size: 16px;">Đã hoàn tiền</h6>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Tiền sẽ được hoàn lại vào tài khoản của bạn theo chính sách.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- KHỐI 2: DANH SÁCH SẢN PHẨM --}}
                <div class="card card-modern mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="section-title mb-4">Sản phẩm đã đặt</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-product-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-0">SẢN PHẨM</th>
                                        <th class="text-center">ĐƠN GIÁ</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end pe-0">THÀNH TIỀN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr class="product-row transition-all">
                                        <td class="ps-0 py-3">
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->images->count() > 0)
                                                    <div class="product-img-box">
                                                        <img src="{{ asset($item->product->images[0]->image_path) }}" alt="IMG">
                                                    </div>
                                                @else
                                                    <div class="product-img-box bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fa-regular fa-image text-muted" style="font-size: 24px;"></i>
                                                    </div>
                                                @endif
                                                <div class="ms-3">
                                                    <a href="{{ url('product/' . ($item->product->slug ?? '')) }}" class="product-name text-dark text-decoration-none d-block mb-1">
                                                        {{ $item->product->name ?? 'Sản phẩm đã bị xóa' }}
                                                    </a>
                                                    @if($item->variant_info)
                                                        <span class="variant-badge text-muted">{{ $item->variant_info }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted fw-medium" style="font-size: 15px;">₫{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="qty-badge">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end pe-0 text-danger fw-bold" style="font-size: 15px;">₫{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================= CỘT PHẢI (4) STICKY ================= --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 90px; z-index: 10;">
                    
                    {{-- KHỐI 3: ĐỊA CHỈ NHẬN HÀNG --}}
                    <div class="card card-modern mb-4">
                        <div class="card-body p-4">
                            <h5 class="section-title mb-4">Thông tin nhận hàng</h5>
                            
                            <div class="address-box bg-light-soft rounded-3 p-3 border">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fa-regular fa-user text-muted me-3" style="font-size: 18px; width: 20px; text-align: center;"></i>
                                    <span class="fw-bold text-dark" style="font-size: 16px;">{{ $order->customer_name }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fa-solid fa-phone text-muted me-3" style="font-size: 18px; width: 20px; text-align: center;"></i>
                                    <span class="text-dark fw-medium" style="font-size: 15px;">{{ $order->customer_phone }}</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="fa-solid fa-map-location-dot mt-1 text-muted me-3" style="font-size: 18px; width: 20px; text-align: center;"></i>
                                    <span class="text-muted" style="line-height: 1.6; font-size: 14px;">
                                        {{ $order->specific_address }}, {{ $order->ward_name }}, {{ $order->district_name }}, {{ $order->province_name }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($order->note)
                                <div class="mt-3 p-3 bg-warning-soft rounded-3 border-warning-soft">
                                    <div class="text-warning-dark fw-bold mb-1" style="font-size: 14px;"><i class="fa-regular fa-comment-dots me-1"></i> Ghi chú từ bạn:</div>
                                    <div class="text-dark fst-italic" style="font-size: 13px;">{{ $order->note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- KHỐI 4: THANH TOÁN & TỔNG KẾT --}}
                    <div class="card card-modern border-0">
                        <div class="card-body p-4">
                            
                            {{-- Chi tiết tiền --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium" style="white-space: nowrap; font-size: 15px;">Tạm tính:</span>
                                <span class="text-dark fw-bold" style="white-space: nowrap; font-size: 16px;">₫{{ number_format($order->total_amount - $order->shipping_fee + $order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium" style="white-space: nowrap; font-size: 15px;">Phí vận chuyển:</span>
                                <span class="text-dark fw-bold" style="white-space: nowrap; font-size: 16px;">₫{{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-medium" style="white-space: nowrap; font-size: 15px;">Giảm giá:</span>
                                <span class="text-success fw-bold" style="white-space: nowrap; font-size: 16px;">- ₫{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            {{-- Dải phân cách đứt nét --}}
                            <div class="dashed-divider my-3"></div>

                            {{-- TỔNG CỘNG --}}
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold text-dark text-uppercase" style="font-size: 15px;">Tổng thanh toán:</span>
                                <span class="text-danger fw-extrabold" style="font-size: 24px; line-height: 1;">₫{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>

                            {{-- Box thông tin phương thức & trạng thái --}}
                            <div class="payment-info-box bg-light-soft p-3 rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted fw-medium" style="font-size: 14px;">Phương thức:</span>
                                    <span class="text-end">
                                        @if($order->payment_method == 'cod')
                                            <span class="badge-payment bg-secondary-soft text-secondary-dark"><i class="fa-solid fa-money-bill-wave me-1"></i> Thanh toán COD</span>
                                        @elseif($order->payment_method == 'vnpay')
                                            <span class="badge-payment bg-primary px-2 py-1"><i class="fa-solid fa-credit-card me-1"></i> Thanh toán VNPAY</span>
                                        @elseif($order->payment_method == 'momo')
                                            <span class="badge-payment bg-danger px-2 py-1"><i class="fa-solid fa-wallet me-1"></i> Ví MOMO</span>
                                        @else
                                            <span class="badge-payment bg-dark px-2 py-1">{{ strtoupper($order->payment_method) }}</span>
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted fw-medium" style="font-size: 14px;">Tình trạng TT:</span>
                                    <span class="text-end">
                                        @if($order->payment_status == 'paid')
                                            <span class="badge-status bg-success-soft text-success-dark"><i class="fa-solid fa-check me-1"></i> ĐÃ THANH TOÁN</span>
                                        @elseif($order->payment_status == 'refunded')
                                            <span class="badge-status bg-warning text-dark"><i class="fa-solid fa-rotate-left me-1"></i> ĐÃ HOÀN TIỀN</span>
                                        @else
                                            <span class="badge-status bg-danger-soft text-danger-dark"><i class="fa-solid fa-exclamation me-1"></i> CHƯA THANH TOÁN</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Nút Hủy đơn (chỉ hiện khi pending/confirmed) --}}
                            @if(in_array($order->order_status, ['pending', 'confirmed']))
                                <form action="{{ route('account.orders.cancel', $order->order_code) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="button" class="btn btn-cancel-modern w-100 js-btn-cancel-order">
                                        HỦY ĐƠN HÀNG
                                    </button>
                                </form>
                            @endif

                            {{-- Nút Đã nhận hàng (chỉ hiện khi đang giao) --}}
                            @if($order->order_status == 'shipping')
                                <form action="{{ route('account.orders.receive', $order->order_code) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="button" class="btn btn-success w-100 fw-bold py-2 rounded-3 js-btn-receive-order" style="border-width: 2px; font-size: 16px;">
                                        <i class="fa-solid fa-box-open me-1"></i> ĐÃ NHẬN ĐƯỢC HÀNG
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ================= CUSTOM CSS MỚI ================= --}}
<style>
    /* Tổng thể */
    .main-wrapper { background-color: #f5f6fa; min-height: 100vh; }
    .transition-all { transition: all 0.3s ease; }
    
    /* Card Hiện Đại */
    .card-modern { border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: none; overflow: hidden; background: #fff; }
    .section-title { font-size: 18px; font-weight: 700; color: #2b3445; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; }
    
    /* Timeline Dọc Mới */
    .timeline-modern { position: relative; padding-top: 10px; }
    .timeline-modern::before { content: ''; position: absolute; left: 160px; top: 15px; bottom: 0; width: 0; background: transparent; z-index: 1; }
    .timeline-modern-item { position: relative; z-index: 2; margin-bottom: 0; }
    .timeline-time { width: 130px; flex-shrink: 0; }
    .timeline-marker { width: 14px; height: 14px; border-radius: 50%; border: none; margin-top: 5px; flex-shrink: 0; }
    .timeline-modern-item:last-child .timeline-content { padding-bottom: 0; }
    
    /* Bảng Sản Phẩm */
    .table-product-modern th { color: #8392a5; font-size: 13px; font-weight: 600; padding-bottom: 15px; border-bottom: 1px solid #eaedf1 !important; }
    .table-product-modern td { border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
    .table-product-modern tr:last-child td { border-bottom: none; }
    .product-row:hover { background-color: #fdfdfe; }
    .product-img-box { width: 65px; height: 65px; border-radius: 8px; overflow: hidden; border: 1px solid #eaedf1; flex-shrink: 0; }
    .product-img-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
    .product-img-box:hover img { transform: scale(1.08); }
    .product-name { font-size: 15px; font-weight: 600; transition: color 0.2s; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .product-name:hover { color: #0d6efd !important; }
    .variant-badge { font-size: 13px; color: #8392a5; background: #f8f9fa; padding: 3px 8px; border-radius: 4px; display: inline-block; }
    .qty-badge { font-size: 14px; font-weight: 600; color: #495057; border: 1px solid #eaedf1; padding: 4px 10px; border-radius: 6px; background: #f8f9fa;}

    /* Box Địa Chỉ & Thanh Toán */
    .bg-light-soft { background-color: #f8f9fa; }
    .border-light-soft { border-color: #eaedf1; }
    .bg-warning-soft { background-color: #fff8e1; }
    .border-warning-soft { border: 1px solid #ffecb3; }
    .text-warning-dark { color: #b97a00; }
    .dashed-divider { border-top: 2px dashed #eaedf1; }

    /* Badges Thanh Toán */
    .badge-payment { padding: 6px 12px; font-size: 13px; border-radius: 6px; font-weight: 600; color: #fff; display: inline-block;}
    .bg-secondary-soft { background-color: #e9ecef; }
    .text-secondary-dark { color: #495057; }
    
    .badge-status { padding: 6px 12px; font-size: 12px; border-radius: 6px; font-weight: 700; display: inline-block;}
    .bg-success-soft { background-color: #d1e7dd; border: 1px solid #badbcc; }
    .text-success-dark { color: #0f5132; }
    .bg-danger-soft { background-color: #f8d7da; border: 1px solid #f5c2c7; }
    .text-danger-dark { color: #842029; }

    /* Nút Button Mới */
    .btn-cancel-modern { background-color: #fff; color: #dc3545; border: 1px solid #dc3545; padding: 12px 20px; font-weight: 700; border-radius: 8px; transition: all 0.2s ease; font-size: 15px;}
    .btn-cancel-modern:hover { background-color: #dc3545; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2); }
    
    /* Responsive */
    @media (max-width: 768px) {
        .timeline-modern::before { display: none; }
        .timeline-time { width: auto; text-align: left !important; margin-bottom: 5px; }
        .timeline-time div.text-end { text-align: left !important; display: inline-block; margin-right: 5px;}
        .timeline-modern-item { flex-direction: column; padding-left: 35px; }
        .timeline-marker { position: absolute; left: 9px; top: 3px; }
        .timeline-content { margin-left: 0 !important; }
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.js-btn-cancel-order').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Xác nhận hủy đơn?",
                text: "Bạn có chắc chắn muốn hủy đơn hàng này không? Thao tác này không thể hoàn tác.",
                icon: "warning",
                buttons: ["Đóng lại", "Đồng ý hủy"],
                dangerMode: true,
            }).then(function(willCancel) {
                if (willCancel) {
                    form.submit();
                }
            });
        });

        // KỊCH BẢN NÚT NHẬN HÀNG
        $('.js-btn-receive-order').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Xác nhận đã nhận hàng?",
                text: "Vui lòng chỉ xác nhận khi bạn đã nhận được gói hàng nguyên vẹn và thanh toán đầy đủ (nếu có).",
                icon: "info",
                buttons: ["Đóng lại", "Xác nhận"],
            }).then(function(willConfirm) {
                if (willConfirm) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush