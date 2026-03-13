@extends('admin.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container-fluid px-4 py-4 premium-layout" style="background-color: #f4f7f9; font-family: 'Inter', sans-serif; min-height: 100vh;">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <div class="icon-box-md bg-gradient-primary text-white shadow-primary me-3"><i class="fa-solid fa-layer-group"></i></div>
                Quản lý đơn hàng
            </h2>
            <p class="text-muted fw-medium mb-0 ms-5 ps-2">Xem và quản lý tất cả đơn hàng trên hệ thống</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="stats-badge shadow-sm">
                <i class="fa-solid fa-chart-pie text-primary me-2"></i>
                <span class="text-muted fw-semibold">Tổng số:</span>
                <span class="fs-5 fw-extrabold text-dark ms-2">{{ $totalFiltered ?? count($orders) }}</span> đơn
            </div>
        </div>
    </div>

    {{-- BỘ LỌC VÀ TÌM KIẾM (TÍNH NĂNG MỚI) --}}
    <div class="card premium-card border-0 shadow-sm mb-4 fade-in-up" style="animation-delay: 0.15s;">
        <div class="card-body p-3">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 fw-medium" placeholder="Tìm mã đơn, tên hoặc SĐT khách..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-light fw-medium text-secondary">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã huỷ</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark fw-bold px-4 hover-lift">Lọc</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-light fw-bold text-danger border hover-lift">Xóa lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- BẢNG DỮ LIỆU ĐƠN HÀNG --}}
    <div class="card premium-card fade-in-up" style="animation-delay: 0.2s;">
        <div class="card-body p-0 mt-2">
            <div class="table-responsive px-4 pb-4 pt-2">
                <table class="table align-middle premium-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Mã Đơn & Thời gian</th>
                            <th style="width: 25%;">Khách hàng</th>
                            <th style="width: 15%;">Tổng tiền</th>
                            <th class="text-center" style="width: 15%;">Thanh toán</th>
                            <th style="width: 15%;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 15%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            {{-- ĐÃ GỠ BỎ ĐOẠN CODE CHỌC DB GÂY LỖI N+1 Ở ĐÂY --}}
                            <tr>
                                <td class="ps-3 py-3">
                                    {{-- IN MÃ ĐƠN HÀNG VÀ NGÀY GIỜ --}}
                                    <span class="fw-bold text-dark fs-6">#{{ $order->order_code ?? $order->id }}</span><br>
                                    <span class="text-muted small fw-medium mt-1 d-inline-block">
                                        <i class="fa-regular fa-clock me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3 bg-primary-soft text-primary shadow-sm fw-bold text-uppercase">
                                            {{ $order->address ? mb_substr($order->address->receiver_name, 0, 1) : 'U' }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $order->address ? $order->address->receiver_name : 'User '.$order->user_id }}</h6>
                                            <span class="text-muted small"><i class="fa-solid fa-phone-flip me-1"></i>{{ $order->address ? $order->address->phone : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="fw-extrabold text-danger" style="letter-spacing: -0.5px;">
                                        {{ number_format($order->total_amount) }} <span class="fs-6 text-muted fw-bold">đ</span>
                                    </span>
                                </td>

                                <td class="text-center">
                                    {{-- TRẠNG THÁI THANH TOÁN --}}
                                    @if($order->payment_status == 'paid')
                                        <span class="badge-soft badge-soft-success"><i class="fa-solid fa-circle-check me-1"></i>Đã thanh toán</span>
                                    @elseif($order->payment_status == 'unpaid')
                                        <span class="badge-soft badge-soft-warning"><i class="fa-solid fa-circle-exclamation me-1"></i>Chưa thanh toán</span>
                                    @else
                                        <span class="badge-soft badge-soft-secondary">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                    
                                    {{-- PHƯƠNG THỨC THANH TOÁN --}}
                                    <div class="mt-2">
                                        <span class="badge bg-light text-dark border px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                            @if(strtolower($order->payment_method) == 'momo')
                                                <i class="fa-solid fa-wallet text-pink-500 me-1" style="color: #a50064;"></i> MOMO
                                            @elseif(strtolower($order->payment_method) == 'vnpay')
                                                <i class="fa-solid fa-credit-card text-primary me-1"></i> VNPAY
                                            @else
                                                <i class="fa-solid fa-truck text-secondary me-1"></i> THU HỘ (COD)
                                            @endif
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-premium {{ $order->order_status }} shadow-sm">
                                        <span class="status-dot"></span> {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>

                                <td class="text-end pe-3">
                                    <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-light btn-sm fw-bold text-primary border shadow-sm hover-lift px-3">
                                        <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-box-open fs-1 text-muted opacity-25 mb-3"></i>
                                        <h5 class="fw-bold text-dark">Chưa có đơn hàng nào</h5>
                                        <p class="text-muted">Hệ thống chưa ghi nhận hoặc không tìm thấy đơn hàng phù hợp.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($orders, 'links') && $orders->hasPages())
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ================= SUPER CSS UI/UX LỘT XÁC ================= --}}
<style>
    /* Typography & Utils */
    .fw-extrabold { font-weight: 800; }
    .icon-box-md { width: 45px; height: 45px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .bg-primary-soft { background: #eff6ff; }
    .bg-gradient-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .shadow-primary { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); }

    /* Layout & Cards */
    .premium-card {
        border-radius: 20px; border: 1px solid rgba(0,0,0,0.03); background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stats-badge {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 10px 20px; display: inline-flex; align-items: center;
    }

    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Avatar & Badges */
    .avatar-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    
    .badge-soft { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; }
    .badge-soft-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a;}
    .badge-soft-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;}
    .badge-soft-secondary { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;}

    /* Status Badges Premium (Tái sử dụng từ trang show) */
    .badge-premium { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; border: 1px solid transparent; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }
    .badge-premium.pending { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .badge-premium.pending .status-dot { background: #64748b; }
    .badge-premium.confirmed { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .badge-premium.confirmed .status-dot { background: #2563eb; box-shadow: 0 0 6px #2563eb; }
    .badge-premium.shipping { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .badge-premium.shipping .status-dot { background: #f59e0b; box-shadow: 0 0 6px #f59e0b; }
    .badge-premium.completed { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .badge-premium.completed .status-dot { background: #16a34a; box-shadow: 0 0 6px #16a34a;}
    .badge-premium.cancelled { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .badge-premium.cancelled .status-dot { background: #ef4444; }

    /* Bảng Premium Tách Rời (Floating Rows) */
    .premium-table { border-collapse: separate; border-spacing: 0 10px; table-layout: fixed !important; width: 100% !important; min-width: 900px !important; }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 10px; border: none; background: transparent; color: #64748b;}
    .premium-table tbody tr { background: #fff; transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 12px; background: #fff;}
    .premium-table td { border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: middle;}
    .premium-table td:first-child { border-left: 1px solid #e2e8f0; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .premium-table td:last-child { border-right: 1px solid #e2e8f0; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    /* Nút bấm hover */
    .hover-lift { transition: all 0.2s ease; border-radius: 10px; }
    .hover-lift:hover { transform: translateY(-2px); }
    
    /* Làm mượt thanh cuộn ngang khi màn hình nhỏ */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px;}
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection