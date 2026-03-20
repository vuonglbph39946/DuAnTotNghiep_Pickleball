@extends('client.layouts.app')
@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="container p-t-80 p-b-50">
    <div class="bread-crumb flex-w p-b-30">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">Trang chủ <i class="fa fa-angle-right m-l-9 m-r-10"></i></a>
        <a href="{{ route('account.index') }}" class="stext-109 cl8 hov-cl1 trans-04">Tài khoản <i class="fa fa-angle-right m-l-9 m-r-10"></i></a>
        <span class="stext-109 cl4 fw-bold">Chi tiết đơn hàng #{{ $order->order_code }}</span>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Tiến trình giao hàng</h5>
                    @foreach($order->statusLogs as $log)
                        <div class="d-flex mb-2">
                            <div class="me-3 text-muted fw-medium">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                            <div class="fw-bold text-dark" style="font-size: 15px;">
                                @if($log->status == 'pending') <span class="text-warning">Chờ xác nhận</span>
                                @elseif($log->status == 'confirmed') <span class="text-info">Đã xác nhận / Đang lấy hàng</span>
                                @elseif($log->status == 'shipping') <span class="text-primary">Đang giao hàng</span>
                                @elseif($log->status == 'completed') <span class="text-success">Giao hàng thành công</span>
                                @elseif($log->status == 'cancel_requested') <span class="text-warning fw-bold">Yêu cầu hủy (Đang chờ xử lý hoàn tiền)</span>
                                @elseif($log->status == 'cancelled') <span class="text-danger">Đã huỷ đơn hàng</span>
                                @elseif($log->status == 'returned') <span class="text-danger">Trả hàng</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Sản phẩm đã đặt</h5>
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <a href="{{ $item->product ? url('product/'.$item->product->slug) : 'javascript:void(0)' }}" class="wrap-pic-w border rounded me-3" style="width: 80px; height: 80px; overflow: hidden; display: block; flex-shrink: 0;">
                                <img src="{{ $item->product && $item->product->images->first() ? asset($item->product->images->first()->image_path) : 'https://placehold.co/80' }}" alt="IMG" style="width: 100%; height: 100%; object-fit: cover; transition: 0.3s;">
                            </a>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 16px;">
                                    <a href="{{ $item->product ? url('product/'.$item->product->slug) : 'javascript:void(0)' }}" class="text-dark text-decoration-none hov-cl1 trans-04 fw-bolder">
                                        {{ $item->product->name ?? 'Sản phẩm đã ngừng kinh doanh / bị xóa' }}
                                    </a>
                                </h6>
                                @if($item->variant_info) 
                                    <div class="small text-muted fw-medium mb-1">Phân loại: {{ $item->variant_info }}</div> 
                                @endif
                                <div class="mt-1 fw-bolder text-danger" style="font-size: 15px;">
                                    {{ number_format($item->price) }}đ 
                                    <span class="text-muted fw-bold ms-1" style="font-size: 14px;">x {{ $item->quantity }}</span>
                                </div>
                            </div>
                            <div class="fw-bolder text-danger ms-3 text-end" style="font-size: 18px; min-width: 100px;">
                                {{ number_format($item->price * $item->quantity) }}đ
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Địa chỉ nhận hàng</h5>
                    <p class="mb-1 fw-bold text-dark" style="font-size: 15px;">{{ $order->customer_name }}</p>
                    <p class="mb-1 text-muted fw-medium"><i class="fa-solid fa-phone me-1"></i> {{ $order->customer_phone }}</p>
                    <p class="mb-0 text-muted fw-medium"><i class="fa-solid fa-location-dot me-1"></i> 
                        {{ $order->specific_address }}, {{ $order->ward_name }}, {{ $order->district_name }}, {{ $order->province_name }}
                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-medium text-muted" style="font-size: 15px;">Tạm tính:</span> 
                        <span class="fw-bold text-dark" style="font-size: 15px;">{{ number_format($order->total_amount - $order->shipping_fee) }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-medium text-muted" style="font-size: 15px;">Phí vận chuyển:</span> 
                        <span class="fw-bold text-dark" style="font-size: 15px;">{{ number_format($order->shipping_fee) }}đ</span>
                    </div>
                    
                    <div class="d-flex justify-content-between pt-3 border-top" style="border-color: #dee2e6 !important;">
                        <span class="fw-bolder text-dark" style="font-size: 18px;">Tổng thanh toán:</span> 
                        <span class="fw-bolder text-danger" style="font-size: 24px;">{{ number_format($order->total_amount) }}đ</span>
                    </div>
                    
                    <div class="mt-3 small text-center text-muted fw-medium p-2 rounded" style="background-color: #fff; border: 1px dashed #ccc;">
                        Thanh toán: <span class="fw-bold text-dark">{{ strtoupper($order->payment_method) }}</span> <br>
                        Trạng thái: {!! $order->payment_status == 'paid' ? '<span class="text-success fw-bolder">ĐÃ THANH TOÁN</span>' : '<span class="text-warning fw-bolder">CHƯA THANH TOÁN</span>' !!}
                    </div>

                    {{-- HIỂN THỊ THÔNG BÁO --}}
                    @if(session('success_order')) <div class="alert alert-success mt-3 mb-0">{{ session('success_order') }}</div> @endif
                    @if(session('error_order')) <div class="alert alert-danger mt-3 mb-0">{{ session('error_order') }}</div> @endif

                    {{-- ĐÃ FIX: Hủy trực tiếp cho cả COD và Online nếu đơn ở trạng thái Pending hoặc Confirmed --}}
                    @if(in_array($order->order_status, ['pending', 'confirmed']))
                        <form action="{{ route('account.orders.cancel', $order->order_code) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="button" class="btn btn-outline-danger w-100 fw-bold py-2 js-btn-cancel-order">
                                <i class="fa-solid fa-xmark me-1"></i> HỦY ĐƠN HÀNG
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .wrap-pic-w:hover img { transform: scale(1.05); }
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
    });
</script>
@endpush