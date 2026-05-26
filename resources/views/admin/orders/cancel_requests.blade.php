@extends('admin.layouts.app')

@section('title', 'Quản lý Yêu cầu Hủy đơn | PBall Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="mdi mdi-alert-decagram text-danger me-2"></i>Yêu cầu Hủy đơn hàng</h3>
        <p class="text-muted small mb-0">Danh sách các đơn hàng khách hàng đang xin hủy chờ bạn duyệt.</p>
    </div>
    <span class="badge bg-danger fs-6 px-3 py-2 shadow-sm">{{ $totalRequests }} yêu cầu chờ xử lý</span>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        
        {{-- ================= FORM TÌM KIẾM ================= --}}
        <form action="{{ route('admin.orders.cancel_requests') }}" method="GET" class="mb-4">
            <div class="row gx-2">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tìm theo Mã đơn hoặc Tên khách..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary fw-bold px-4">Lọc</button>
                    </div>
                </div>
                @if(request('search'))
                <div class="col-md-2">
                    <a href="{{ route('admin.orders.cancel_requests') }}" class="btn btn-light border fw-bold w-100 text-danger">
                        <i class="mdi mdi-close me-1"></i> Xóa lọc
                    </a>
                </div>
                @endif
            </div>
        </form>

        {{-- ================= BẢNG DANH SÁCH ================= --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle border mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="py-3 px-3">MÃ ĐƠN</th>
                        <th class="py-3">KHÁCH HÀNG</th>
                        <th class="py-3">THỜI GIAN YÊU CẦU</th>
                        <th class="py-3">THANH TOÁN</th>
                        <th class="py-3" style="width: 25%;">LÝ DO HỦY</th>
                        <th class="py-3 text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancelOrders as $order)
                    <tr>
                        <td class="px-3 fw-bold text-primary">#{{ $order->order_code }}</td>
                        <td>
                            <p class="mb-0 fw-bold text-dark">{{ $order->customer_name }}</p>
                            <small class="text-muted"><i class="mdi mdi-phone me-1"></i>{{ $order->customer_phone }}</small>
                        </td>
                        <td>
                            <span class="text-dark fw-medium">{{ $order->updated_at->format('H:i') }}</span>
                            <br>
                            <small class="text-muted">{{ $order->updated_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            @if($order->payment_method == 'vnpay')
                                <span class="badge bg-primary px-2 py-1"><i class="mdi mdi-credit-card me-1"></i>VNPay</span>
                            @elseif($order->payment_method == 'momo')
                                <span class="badge px-2 py-1" style="background-color: #a50064;"><i class="mdi mdi-wallet me-1"></i>MoMo</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1"><i class="mdi mdi-cash me-1"></i>COD</span>
                            @endif
                            <br>
                            @if($order->payment_status == 'paid')
                                <small class="text-success fw-bold d-inline-block mt-1"><i class="mdi mdi-check-circle me-1"></i>Đã thanh toán</small>
                            @else
                                <small class="text-warning fw-bold d-inline-block mt-1 text-dark"><i class="mdi mdi-clock-outline me-1"></i>Chưa thanh toán</small>
                            @endif
                        </td>
                        <td>
                            <div class="p-2 bg-light rounded border-start border-3 border-danger">
                                <span class="text-danger fw-medium small">{{ $order->cancel_reason ?? 'Không để lại lý do cụ thể' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-danger fw-bold shadow-sm px-3">
                                <i class="mdi mdi-flash me-1"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="mb-3">
                                <i class="mdi mdi-check-circle-outline text-success" style="font-size: 60px;"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Tuyệt vời!</h5>
                            <p class="text-muted mb-0">Hiện không có yêu cầu hủy đơn nào cần xử lý.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= PHÂN TRANG ================= --}}
        <div class="mt-4 d-flex justify-content-end">
            {{ $cancelOrders->links('pagination::bootstrap-5') }}
        </div>
        
    </div>
</div>
@endsection