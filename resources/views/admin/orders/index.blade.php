@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng | PBall Store')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title text-primary mb-1"><i class="mdi mdi-receipt me-2"></i> Danh Sách Đơn Hàng</h4>
                        <p class="card-description text-muted mb-0">Quản lý, tìm kiếm và theo dõi trạng thái đơn hàng</p>
                    </div>
                    <div>
                        <span class="badge badge-opacity-primary border">Tổng số: <strong>{{ $totalFiltered ?? count($orders) }}</strong> đơn</span>
                    </div>
                </div>

                {{-- =============================================== --}}
                {{-- BỘ LỌC VÀ TÌM KIẾM REALTIME                     --}}
                {{-- =============================================== --}}
                <form action="{{ route('admin.orders.index') }}" method="GET" id="filterForm" class="mb-4">
                    <div class="row gx-2 gy-3 align-items-center">
                        
                        {{-- Ô Tìm kiếm Realtime --}}
                        <div class="col-md-5 position-relative">
                            <div class="input-group custom-search-group">
                                <span class="input-group-text bg-white"><i class="mdi mdi-magnify fs-5 text-dark"></i></span>
                                <input type="text" name="search" id="searchInput" class="form-control custom-input" placeholder="Nhập mã đơn, tên, SĐT khách hàng..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                            
                            {{-- Hộp chứa gợi ý Realtime --}}
                            <div id="searchSuggestions" class="position-absolute w-100 bg-white border rounded shadow d-none" style="z-index: 1000; top: 100%; max-height: 350px; overflow-y: auto;">
                                {{-- Gợi ý sẽ được AJAX đổ vào đây --}}
                            </div>
                        </div>
                        
                        {{-- Lọc Trạng thái Đơn hàng --}}
                        <div class="col-md-3">
                            <select name="status" class="form-select custom-select" onchange="this.form.submit()">
                                <option value="" class="fw-bold">-- Trạng thái đơn --</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã huỷ</option>
                            </select>
                        </div>

                        {{-- Lọc Trạng thái Thanh toán --}}
                        <div class="col-md-3">
                            <select name="payment_status" class="form-select custom-select" onchange="this.form.submit()">
                                <option value="" class="fw-bold">-- Trạng thái thanh toán --</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                            </select>
                        </div>

                        {{-- Nút Reset --}}
                        <div class="col-md-1">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-light w-100 text-center custom-btn-reset" title="Làm mới">
                                <i class="mdi mdi-refresh fs-5 mx-0 text-dark"></i>
                            </a>
                        </div>
                    </div>
                </form>

                {{-- =============================================== --}}
                {{-- BẢNG DANH SÁCH ĐƠN HÀNG                         --}}
                {{-- =============================================== --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead class="bg-light">
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Khách Hàng</th>
                                <th>Tổng Giá Trị</th>
                                <th>Trạng Thái</th>
                                <th>Thanh Toán</th>
                                <th class="text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    {{-- Cột 1: Mã Đơn --}}
                                    <td>
                                        <strong class="text-primary">#{{ $order->order_code ?? $order->id }}</strong><br>
                                        <small class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}</small>
                                    </td>
                                    
                                    {{-- Cột 2: Khách Hàng --}}
                                    <td>
                                        <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                                        <div class="text-muted small mt-1"><i class="mdi mdi-phone me-1"></i>{{ $order->customer_phone }}</div>
                                    </td>
                                    
                                    {{-- Cột 3: Tổng Giá Trị --}}
                                    <td>
                                        <strong class="text-danger fs-6">{{ number_format($order->total_amount) }} VNĐ</strong>
                                    </td>
                                    
                                    {{-- Cột 4: Trạng Thái Đơn Hàng --}}
                                    <td>
                                        @if($order->order_status == 'pending')
                                            <span class="badge badge-opacity-warning border border-warning">Chờ xác nhận</span>
                                        @elseif($order->order_status == 'confirmed')
                                            <span class="badge badge-opacity-info border border-info">Đã xác nhận</span>
                                        @elseif($order->order_status == 'shipping')
                                            <span class="badge badge-opacity-primary border border-primary">Đang giao</span>
                                        @elseif($order->order_status == 'completed')
                                            <span class="badge badge-opacity-success border border-success">Hoàn thành</span>
                                        @elseif($order->order_status == 'cancelled')
                                            <span class="badge badge-opacity-danger border border-danger">Đã huỷ</span>
                                        @else
                                            <span class="badge badge-opacity-dark border">{{ ucfirst($order->order_status) }}</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Cột 5: Trạng Thái & Phương Thức Thanh Toán --}}
                                    <td>
                                        <div class="mb-1">
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success text-white"><i class="mdi mdi-check-circle-outline me-1"></i>Đã thanh toán</span>
                                            @elseif($order->payment_status == 'refunded')
                                                <span class="badge bg-secondary text-white"><i class="mdi mdi-backup-restore me-1"></i>Đã hoàn tiền</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="mdi mdi-clock-outline me-1"></i>Chưa thanh toán</span>
                                            @endif
                                        </div>
                                        
                                        <div class="small fw-bold mt-1">
                                            @if($order->payment_method == 'momo')
                                                <span style="color: #a50064;"><i class="mdi mdi-wallet me-1"></i>MoMo</span>
                                            @elseif($order->payment_method == 'vnpay')
                                                <span class="text-primary"><i class="mdi mdi-credit-card me-1"></i>VNPay</span>
                                            @elseif($order->payment_method == 'vietqr')
                                                <span class="text-info"><i class="mdi mdi-qrcode me-1"></i>VietQR</span>
                                            @else
                                                <span class="text-secondary"><i class="mdi mdi-cash me-1"></i>COD</span>
                                            @endif
                                        </div>

                                        {{-- ĐÃ FIX: CHẶN HIỂN THỊ MÃ GIAO DỊCH ẢO CỦA ĐƠN COD --}}
                                        @php
                                            $paymentLog = \App\Models\Payment::where('order_id', $order->id)->latest()->first();
                                        @endphp

                                        @if($order->payment_method != 'cod' && $paymentLog && $paymentLog->transaction_code && $paymentLog->transaction_code != 'Unknown')
                                            <div class="mt-1 text-info small fw-bold" style="font-size: 11px;">
                                                Mã GD: {{ $paymentLog->transaction_code }}
                                            </div>
                                        @endif

                                        @if($order->order_status == 'cancelled' && $paymentLog && $paymentLog->payment_status == 'failed')
                                            <div class="mt-1 text-danger small fw-bold" style="font-size: 11px; line-height: 1.2; max-width: 150px; white-space: normal;">
                                                <i class="mdi mdi-alert-circle me-1"></i>{{ $paymentLog->status }}
                                            </div>
                                        @elseif($order->order_status == 'cancelled' && $order->payment_status == 'unpaid' && in_array($order->payment_method, ['vnpay', 'momo']))
                                             <div class="mt-1 text-danger small fw-bold" style="font-size: 11px; line-height: 1.2; max-width: 150px; white-space: normal;">
                                                <i class="mdi mdi-alert-circle me-1"></i>Lỗi: Khách hủy/Lỗi giao dịch
                                            </div>
                                        @endif
                                    </td>
                                    
                                    {{-- Cột 6: Hành Động --}}
                                    <td class="text-center">
                                        <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-sm btn-primary text-white py-1 px-2 rounded">
                                            <i class="mdi mdi-eye"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                {{-- Khi bảng rỗng hoặc tìm không thấy --}}
                                <tr class="empty-state-row">
                                    <td colspan="6" class="text-center py-5">
                                        <i class="mdi mdi-text-box-search-outline text-muted" style="font-size: 50px;"></i>
                                        <h5 class="mt-2 text-dark">Không tìm thấy đơn hàng nào!</h5>
                                        <p class="text-muted">Thử thay đổi từ khóa tìm kiếm hoặc làm mới lại bộ lọc.</p>
                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-light border mt-2"><i class="mdi mdi-refresh"></i> Tải lại trang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Phân trang --}}
                @if(method_exists($orders, 'links') && $orders->hasPages())
                    <div class="mt-4 d-flex justify-content-end">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS TRỊ BỆNH MỜ ÁM CỦA TEMPLATE STAR ADMIN */
    
    /* Ô Input Tìm Kiếm */
    #filterForm .custom-input {
        border: 1px solid #ced4da !important;
        border-left: none !important;
        background-color: #ffffff !important;
        color: #212529 !important;
        font-weight: 500;
        height: 44px;
    }
    #filterForm .custom-input::placeholder {
        color: #6c757d !important;
        opacity: 0.8;
    }
    #filterForm .input-group-text {
        border: 1px solid #ced4da !important;
        border-right: none !important;
        background-color: #ffffff !important;
    }

    /* Dropdown Select */
    #filterForm .custom-select {
        border: 1px solid #ced4da !important;
        background-color: #ffffff !important;
        color: #212529 !important;
        font-weight: 500;
        height: 44px;
        box-shadow: none !important;
    }

    /* Nút Reset */
    #filterForm .custom-btn-reset {
        border: 1px solid #ced4da !important;
        background-color: #f8f9fa !important;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #filterForm .custom-btn-reset:hover {
        background-color: #e2e6ea !important;
    }

    /* Hiệu ứng khi Focus (Bấm vào) */
    #filterForm .custom-input:focus, 
    #filterForm .custom-select:focus {
        border-color: #4b49ac !important;
        box-shadow: 0 0 0 0.2rem rgba(75, 73, 172, 0.25) !important;
    }
    #filterForm .custom-input:focus + .input-group-text {
        border-color: #4b49ac !important;
    }

    .suggestion-item:hover { background-color: #f8f9fa; }
    .badge { font-weight: 600; padding: 6px 10px; }
</style>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let searchTimer;
        
        // Bắt sự kiện người dùng gõ phím vào ô tìm kiếm
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimer);
            let query = $(this).val().trim();
            let suggestBox = $('#searchSuggestions');
            
            if (query.length >= 2) {
                // Đang gõ thì hiện trạng thái Loading
                suggestBox.removeClass('d-none').html('<div class="p-3 text-center text-muted"><i class="mdi mdi-loading mdi-spin me-2"></i>Đang tìm kiếm...</div>');
                
                // Đợi 400ms sau khi ngừng gõ mới gọi dữ liệu
                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('admin.orders.index') }}",
                        data: { search: query },
                        success: function(res) {
                            let suggestionsHTML = '';
                            let validRows = 0;
                            
                            $(res).find('.table tbody tr').each(function() {
                                if ($(this).hasClass('empty-state-row')) return; 
                                
                                if (validRows < 5) {
                                    let orderCode = $(this).find('td:eq(0) strong').text().trim();
                                    let customerName = $(this).find('td:eq(1) .fw-bold').text().trim();
                                    let phone = $(this).find('td:eq(1) .text-muted').text().trim();
                                    let amount = $(this).find('td:eq(2) strong').text().trim();
                                    
                                    suggestionsHTML += `
                                        <div class="p-3 border-bottom suggestion-item" style="cursor: pointer;" onclick="submitSearch('${orderCode.replace('#', '')}')">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge badge-opacity-primary border me-2">${orderCode}</span>
                                                    <span class="fw-bold text-dark">${customerName}</span>
                                                </div>
                                                <span class="text-danger fw-bold">${amount}</span>
                                            </div>
                                            <div class="text-muted small mt-1"><i class="mdi mdi-phone me-1"></i>${phone}</div>
                                        </div>
                                    `;
                                    validRows++;
                                }
                            });
                            
                            if (suggestionsHTML !== '') {
                                suggestBox.html(suggestionsHTML);
                            } else {
                                suggestBox.html('<div class="p-4 text-center text-danger"><i class="mdi mdi-alert-circle-outline fs-4 d-block mb-1"></i> Không tìm thấy đơn hàng nào!</div>');
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

        // Click vào 1 gợi ý -> Đẩy chữ lên ô tìm kiếm và Submit
        window.submitSearch = function(val) {
            $('#searchInput').val(val);
            $('#searchSuggestions').addClass('d-none');
            $('#filterForm').submit();
        };

        // Bấm ra ngoài khoảng trắng thì tự động ẩn cái hộp gợi ý đi
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.position-relative').length) {
                $('#searchSuggestions').addClass('d-none');
            }
        });
    });
</script>
@endpush