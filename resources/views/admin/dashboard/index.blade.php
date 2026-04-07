@extends('admin.layouts.app')

@section('title', 'Thống kê tổng quan | PBall Store')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="mdi mdi-chart-line text-primary me-2"></i>Tổng quan kinh doanh</h3>
        
        {{-- BỘ LỌC THỜI GIAN --}}
        <div class="dropdown">
            <button class="btn btn-outline-primary fw-bold dropdown-toggle bg-white" type="button" data-bs-toggle="dropdown">
                <i class="mdi mdi-calendar-range me-1"></i> 
                @if($days == 7) 7 Ngày qua 
                @elseif($days == 30) 30 Ngày qua 
                @else Toàn thời gian @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item {{ $days == 7 ? 'active' : '' }}" href="{{ route('admin.dashboard', ['days' => 7]) }}">7 Ngày qua</a></li>
                <li><a class="dropdown-item {{ $days == 30 ? 'active' : '' }}" href="{{ route('admin.dashboard', ['days' => 30]) }}">30 Ngày qua</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item {{ $days == 0 ? 'active' : '' }}" href="{{ route('admin.dashboard', ['days' => 0]) }}">Toàn thời gian</a></li>
            </ul>
        </div>
    </div>

    {{-- TẦNG 1: 5 THẺ CHỈ SỐ CỐT LÕI (Sử dụng row-cols-md-5 để xếp 5 cột) --}}
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <p class="mb-1 text-white-50 fw-bold text-uppercase">Doanh thu ròng</p>
                    <h5 class="fw-bold mb-0">{{ number_format($dashboardData['net_revenue']) }} đ</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body">
                    <p class="mb-1 text-white-50 fw-bold text-uppercase">Giá trị TB Đơn</p>
                    <h5 class="fw-bold mb-0">{{ number_format($dashboardData['aov']) }} đ</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 bg-danger text-white h-100">
                <div class="card-body">
                    <p class="mb-1 text-white-50 fw-bold text-uppercase">Tỷ lệ hủy đơn</p>
                    <h5 class="fw-bold mb-0">{{ $dashboardData['cancel_rate'] }}%</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body">
                    <p class="mb-1 text-white-50 fw-bold text-uppercase">Khách hàng mới</p>
                    <h5 class="fw-bold mb-0">{{ $dashboardData['new_customers'] }} user</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 bg-warning text-dark h-100">
                <div class="card-body">
                    <p class="mb-1 fw-bold text-uppercase" style="color: #856404;">Đã giảm giá</p>
                    <h5 class="fw-bold mb-0">{{ number_format($dashboardData['voucher_stats']['total_discount_given']) }} đ</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- TẦNG 2: 2 BIỂU ĐỒ (LINE 7 NGÀY & BAR 6 THÁNG) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-3 pb-2 border-0">
                    <h6 class="fw-bold text-uppercase mb-0">Biểu đồ doanh thu theo ngày</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-3 pb-2 border-0">
                    <h6 class="fw-bold text-uppercase mb-0">Doanh thu 6 tháng gần nhất</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TẦNG 3: BẢNG XẾP HẠNG --}}
    <div class="row g-4 mb-5">
        {{-- Top Sản phẩm --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-3 pb-2 border-0">
                    <h6 class="fw-bold text-uppercase mb-0">Top Sản phẩm bán chạy</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @foreach($dashboardData['top_products'] as $prodStat)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $prodStat->product->name ?? 'N/A' }}</td>
                                <td class="text-end pe-4"><span class="badge bg-primary">{{ $prodStat->total_sold }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Danh mục --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-3 pb-2 border-0">
                    <h6 class="fw-bold text-uppercase mb-0">Top Danh Mục bán chạy</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @foreach($dashboardData['top_categories'] as $cat)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $cat->name }}</td>
                                <td class="text-end pe-4"><span class="badge bg-success">{{ $cat->total_sold }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Voucher --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-3 pb-2 border-0">
                    <h6 class="fw-bold text-uppercase mb-0">Top Voucher</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @foreach($dashboardData['voucher_stats']['top_coupons'] as $vStat)
                            <tr>
                                <td class="ps-4 fw-bold text-danger">{{ $vStat->coupon->code ?? 'N/A' }}</td>
                                <td class="text-end pe-4"><span class="badge bg-danger">{{ $vStat->times_used }} đơn</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Biểu đồ Đường (Theo ngày)
        new Chart(document.getElementById('revenueChart').getContext('2d'), {
            type: 'line', 
            data: {
                labels: {!! json_encode($dashboardData['chart_data']['labels']) !!}, 
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($dashboardData['chart_data']['data']) !!}, 
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.15)',
                    borderWidth: 3, fill: true, tension: 0.4
                }]
            }
        });

        // Biểu đồ Cột (Theo tháng)
        new Chart(document.getElementById('monthlyChart').getContext('2d'), {
            type: 'bar', 
            data: {
                labels: {!! json_encode($dashboardData['monthly_chart']['labels']) !!}, 
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($dashboardData['monthly_chart']['data']) !!}, 
                    backgroundColor: '#198754',
                    borderRadius: 4
                }]
            }
        });
    });
</script>
@endsection