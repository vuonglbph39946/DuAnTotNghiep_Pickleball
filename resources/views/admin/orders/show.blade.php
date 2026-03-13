@extends('admin.layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

@php
    // TÍNH TOÁN TRƯỚC TIỀN HÀNG ĐỂ HIỂN THỊ Ở CỘT TRÁI
    $sum = 0;
    foreach($order->items as $item) {
        $sum += $item->price * $item->quantity;
    }
    
    // MẢNG VIỆT HÓA TRẠNG THÁI
    $statusLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã huỷ'
    ];
@endphp

<div class="container-fluid px-4 py-4 premium-layout" style="background-color: #f4f7f9; font-family: 'Inter', sans-serif;">

    <div class="d-flex align-items-center justify-content-between mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i>Danh sách</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Chi tiết #{{ $order->order_code ?? $order->id }}</li>
                </ol>
            </nav>
            <h2 class="fw-extrabold mb-0 text-dark" style="letter-spacing: -0.5px;">Quản lý đơn hàng</h2>
        </div>
        <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-white btn-print shadow-sm text-decoration-none text-dark">
            <i class="fa-solid fa-print text-primary me-2"></i> In vận đơn
        </a>
    </div>

    <div class="row g-4">
        {{-- ================= LEFT COLUMN ================= --}}
        <div class="col-lg-4" id="leftColumn">

            <div class="card premium-card mb-4 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-uppercase text-secondary tracking-wide mb-0 d-flex align-items-center">
                        <div class="icon-box-sm bg-primary-soft text-primary me-2"><i class="fa-solid fa-address-card"></i></div>
                        Thông tin chung
                    </h6>
                </div>
                <div class="card-body px-4 py-3">
                    @php
                        $address = $order->address;
                    @endphp

                    <div class="info-group">
                        <div class="info-label text-muted small mb-1">Khách hàng</div>
                        <div class="info-value fw-bold text-dark d-flex align-items-center">
                            <div class="avatar-circle me-2 bg-gradient-primary text-white shadow-sm"><i class="fa-regular fa-user"></i></div>
                            {{ $address ? $address->receiver_name : 'User '.$order->user_id }}
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label text-muted small mb-1">Thông tin giao hàng</div>
                        <div class="info-value fw-semibold text-dark">
                            <div class="mb-1">
                                <i class="fa-solid fa-phone text-success me-2"></i>SĐT: {{ $address ? $address->phone : 'N/A' }}
                            </div>
                            <div>
                                <i class="fa-solid fa-location-dot text-danger me-2"></i>Địa chỉ: 
                                <span class="fw-normal">
                                    {{ $address ? $address->address . ($address->ward ? ', ' . $address->ward : '') . ($address->district ? ', ' . $address->district : '') . ', ' . $address->city : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-group border-0">
                        <div class="info-label text-muted small mb-1">Trạng thái & Phương thức thanh toán</div>
                        
                        {{-- ĐÃ FIX: BẢO VỆ GIAO DIỆN KHI TRẠNG THÁI LÀ HOÀN TIỀN (REFUNDED) --}}
                        <div class="info-value d-flex align-items-center gap-2">
                            @if($order->payment_status == 'unpaid')
                                <span class="badge-soft badge-soft-warning"><i class="fa-solid fa-circle-exclamation me-1"></i> Chưa thanh toán</span>
                            @elseif($order->payment_status == 'paid')
                                <span class="badge-soft badge-soft-success"><i class="fa-solid fa-circle-check me-1"></i> Đã thanh toán</span>
                            @elseif($order->payment_status == 'refunded')
                                <span class="badge-soft" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;"><i class="fa-solid fa-rotate-left me-1"></i> Đã hoàn tiền</span>
                            @endif

                            <span class="badge bg-light text-dark border shadow-sm">
                                @if(strtolower($order->payment_method) == 'momo')
                                    <i class="fa-solid fa-wallet text-pink-500" style="color: #a50064;"></i> MOMO
                                @elseif(strtolower($order->payment_method) == 'vnpay')
                                    <i class="fa-solid fa-credit-card text-primary"></i> VNPAY
                                @else
                                    <i class="fa-solid fa-truck text-secondary"></i> COD
                                @endif
                            </span>
                        </div>
                        {{-- KẾT THÚC FIX BẢO VỆ GIAO DIỆN HOÀN TIỀN --}}
                    </div>
                </div>
                
                <div class="card-footer border-0 rounded-bottom-4 px-4 py-3 bg-gradient-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-semibold small">Tiền hàng:</span>
                        <span class="fw-bold text-dark">{{ number_format($sum) }} đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-semibold small">Phí vận chuyển:</span>
                        <span class="fw-bold text-dark">{{ number_format($order->shipping_fee ?? 0) }} đ</span>
                    </div>
                    <hr class="my-2 border-secondary opacity-10">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-dark fw-bold">TỔNG THANH TOÁN:</span>
                        <span class="fs-4 fw-extrabold text-danger text-gradient" style="letter-spacing: -0.5px;">
                            {{ number_format($order->total_amount) }}<span class="fs-6 ms-1 text-muted fw-bold">đ</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card premium-card control-card fade-in-up shadow-lg" style="animation-delay: 0.3s; border: 1px solid rgba(13, 110, 253, 0.15);">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-secondary tracking-wide mb-4 d-flex align-items-center">
                        <div class="icon-box-sm bg-primary-soft text-primary me-2"><i class="fa-solid fa-sliders"></i></div>
                        Bảng điều khiển
                    </h6>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold mb-2">Thay đổi trạng thái đơn hàng</label>
                        
                        <select id="statusSelect" class="d-none" {{ in_array($order->order_status, ['cancelled', 'completed']) ? 'disabled' : '' }}>
                            @php
                                $steps = ['pending','confirmed','shipping','completed'];
                                $current = $order->order_status;
                                $currentIndex = in_array($current, $steps) ? array_search($current, $steps) : -1;
                            @endphp
                            @foreach(['pending','confirmed','shipping','completed','cancelled'] as $st)
                                @php
                                    $disabled = 'disabled'; 
                                    if ($current === 'cancelled' || $current === 'completed') { $disabled = 'disabled'; } 
                                    else {
                                        if ($st === $current || $st === 'cancelled') $disabled = ''; 
                                        else {
                                            $stIndex = array_search($st, $steps);
                                            if ($stIndex !== false && $stIndex === $currentIndex + 1) $disabled = ''; 
                                        }
                                    }
                                @endphp
                                <option value="{{ $st }}" {{ $order->order_status==$st?'selected':'' }} {{ $disabled }}>{{ $st }}</option>
                            @endforeach
                        </select>

                        <div class="status-dropdown-wrapper" id="dropdownWrapper">
                            <div class="custom-select-trigger {{ in_array($order->order_status, ['cancelled', 'completed']) ? 'disabled' : '' }}" onclick="toggleStatusDropdown()">
                                <div id="triggerContent" class="d-flex align-items-center gap-2"></div>
                                <i class="fa-solid fa-chevron-down text-muted ms-2"></i>
                            </div>

                            <div class="custom-select-options shadow-xl" id="customSelectOptions">
                                @foreach(['pending','confirmed','shipping','completed','cancelled'] as $st)
                                    @php
                                        $isDisabled = true;
                                        if ($current != 'cancelled' && $current != 'completed') {
                                            if ($st === $current || $st === 'cancelled') $isDisabled = false; 
                                            else {
                                                $stIndex = array_search($st, $steps);
                                                if ($stIndex !== false && $stIndex === $currentIndex + 1) $isDisabled = false; 
                                            }
                                        }
                                        
                                        $icon = ''; $colorClass = ''; $bgClass = '';
                                        if($st == 'pending') { $icon = 'fa-box-open'; $colorClass = 'text-secondary'; $bgClass = 'bg-secondary-soft'; }
                                        elseif($st == 'confirmed') { $icon = 'fa-clipboard-check'; $colorClass = 'text-primary'; $bgClass = 'bg-primary-soft';}
                                        elseif($st == 'shipping') { $icon = 'fa-truck-fast'; $colorClass = 'text-warning'; $bgClass = 'bg-warning-soft';}
                                        elseif($st == 'completed') { $icon = 'fa-house-circle-check'; $colorClass = 'text-success'; $bgClass = 'bg-success-soft';}
                                        elseif($st == 'cancelled') { $icon = 'fa-ban'; $colorClass = 'text-danger'; $bgClass = 'bg-danger-soft';}
                                    @endphp
                                    
                                    <div class="custom-opt {{ $isDisabled ? 'disabled-opt' : '' }} {{ $st === $current ? 'active-opt' : '' }}" 
                                         data-value="{{ $st }}" 
                                         onclick="if(!this.classList.contains('disabled-opt')) setCustomSelect('{{ $st }}')">
                                        <div class="icon-wrap {{ $colorClass }} {{ $bgClass }}"><i class="fa-solid {{ $icon }}"></i></div>
                                        <span class="fw-bold text-dark">{{ $statusLabels[$st] }}</span>
                                        @if($st === $current) 
                                            <span class="ms-auto check-icon {{ $colorClass }}"><i class="fa-solid fa-circle-check fs-5"></i></span> 
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="d-grid gap-3 mt-4">
                        <button onclick="handleUpdate()" id="btnUpdate" class="btn btn-gradient-primary btn-lg shadow-primary" {{ in_array($order->order_status, ['cancelled', 'completed']) ? 'disabled' : '' }}>
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i>Lưu thay đổi
                        </button>

                        @if(!in_array($order->order_status, ['pending', 'cancelled', 'completed']))
                        <button onclick="confirmUndo()" id="btnUndo" class="btn btn-light fw-bold shadow-sm hover-lift text-warning border-warning">
                            <i class="fa-solid fa-rotate-left me-2"></i>Hoàn tác trạng thái
                        </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= RIGHT COLUMN ================= --}}
        <div class="col-lg-8" id="rightColumn" style="min-width: 0;">

            <div class="card premium-card mb-4 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h6 class="fw-bold text-uppercase text-secondary tracking-wide mb-0 d-flex align-items-center">
                            <div class="icon-box-sm bg-primary-soft text-primary me-2"><i class="fa-solid fa-route"></i></div>
                            Hành trình đơn hàng
                        </h6>
                        <span id="currentStatus" data-status="{{ $order->order_status }}" class="badge-premium {{ $order->order_status }} shadow-sm">
                            <span class="status-dot"></span> {{ $statusLabels[$order->order_status] }}
                        </span>
                    </div>

                    <div class="stepper-wrapper">
                        <div class="stepper-progress-bar">
                            @php
                                $progressWidth = 0;
                                if($currentIndex == 1) $progressWidth = 33;
                                elseif($currentIndex == 2) $progressWidth = 66;
                                elseif($currentIndex == 3) $progressWidth = 100;
                                if($current == 'cancelled') $progressWidth = 0;
                            @endphp
                            <div class="stepper-progress-fill" style="width: {{ $progressWidth }}%;"></div>
                        </div>

                        <div class="stepper-steps">
                            @foreach($steps as $index => $step)
                                @php
                                    $isPassed = $index <= $currentIndex;
                                    $isActive = $index == $currentIndex;
                                    $stepClass = 'step-item';
                                    if ($isPassed) $stepClass .= ' passed';
                                    if ($isActive) $stepClass .= ' active';
                                    if ($current == 'cancelled') $stepClass = 'step-item'; 
                                @endphp
                                <div class="{{ $stepClass }}">
                                    <div class="step-icon-box shadow-sm">
                                        @if($step == 'pending') <i class="fa-solid fa-box-open"></i>
                                        @elseif($step == 'confirmed') <i class="fa-solid fa-clipboard-check"></i>
                                        @elseif($step == 'shipping') <i class="fa-solid fa-truck-fast"></i>
                                        @elseif($step == 'completed') <i class="fa-solid fa-house-circle-check"></i>
                                        @endif
                                    </div>
                                    <div class="step-title mt-3">{{ $statusLabels[$step] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="card premium-card mb-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                    <h6 class="fw-bold text-uppercase text-secondary tracking-wide mb-0 d-flex align-items-center">
                        <div class="icon-box-sm bg-primary-soft text-primary me-2"><i class="fa-solid fa-cart-shopping"></i></div>
                        Danh sách sản phẩm
                    </h6>
                </div>
                <div class="card-body p-0 mt-2">
                    <div class="table-responsive">
                        <table class="table align-middle premium-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="width: 45%;">Sản phẩm</th>
                                    <th class="text-end" style="width: 15%;">Đơn giá</th>
                                    <th class="text-center" style="width: 12%;">Tồn kho</th>
                                    <th class="text-center" style="width: 10%;">SL Đặt</th>
                                    <th class="text-end pe-4" style="width: 18%;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php $line = $item->price * $item->quantity; @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @php
                                                    $firstImage = $item->product->images->first();
                                                    $imageUrl = $firstImage ? asset($firstImage->image_path) : 'https://placehold.co/150x150/f8f9fa/adb5bd?text=No+Image';
                                                @endphp
                                                
                                                <div class="product-img-premium shadow-sm popup-trigger" onclick="openImageModal('{{ $imageUrl }}')">
                                                    <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}">
                                                    <div class="eye-overlay"><i class="fa-solid fa-eye"></i></div>
                                                </div>
                                                
                                                <div style="min-width: 0;"> 
                                                    <h6 class="fw-bold mb-1 text-dark" style="word-break: break-word; white-space: normal;">{{ $item->product->name ?? 'Sản phẩm' }}</h6>
                                                    
                                                    @if($item->variant_info)
                                                        <div class="mb-1 mt-1">
                                                            <span class="badge bg-light text-dark border shadow-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                                                                <i class="fa-solid fa-tag text-primary opacity-75 me-1"></i> {{ $item->variant_info }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <span class="text-muted small">SKU: <span class="fw-medium">#{{ $item->variant ? $item->variant->sku : ($item->product->sku ?? $item->product_id) }}</span></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-medium text-secondary">{{ number_format($item->price) }} đ</td>
                                        
                                        <td class="text-center">
                                            @php $stock = $item->variant ? $item->variant->stock : ($item->product->stock ?? 0); @endphp
                                            @if($stock <= 0)
                                                <span class="badge bg-danger-soft text-danger fw-bold border border-danger px-2 py-1">Hết hàng</span>
                                            @elseif($stock <= 5)
                                                <span class="badge bg-warning-soft text-warning fw-bold border border-warning px-2 py-1">{{ $stock }}</span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1">{{ $stock }}</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-dark text-white border px-2 py-1 fs-6 shadow-sm">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end pe-4 fw-extrabold text-danger">{{ number_format($line) }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card premium-card fade-in-up" style="animation-delay: 0.4s;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-uppercase text-secondary tracking-wide mb-0 d-flex align-items-center">
                        <div class="icon-box-sm bg-primary-soft text-primary me-2"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        Nhật ký hoạt động
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-vertical custom-scrollbar" id="history">
                        @php
                            $flowSteps = ['pending', 'confirmed', 'shipping', 'completed'];
                            // Lịch sử xếp theo ID để chuẩn thứ tự thời gian
                            $logsArray = $order->statusLogs->sortByDesc('id')->values();
                        @endphp

                        @forelse($logsArray as $index => $log)
                            @php
                                $isUndo = false;
                                if (isset($logsArray[$index + 1])) {
                                    $prevLog = $logsArray[$index + 1];
                                    $currIdx = array_search($log->status, $flowSteps);
                                    $prevIdx = array_search($prevLog->status, $flowSteps);
                                    
                                    if (($currIdx !== false && $prevIdx !== false && $currIdx < $prevIdx) || ($prevLog->status == 'cancelled' && $log->status != 'cancelled')) {
                                        $isUndo = true;
                                    }
                                }
                            @endphp

                            <div class="tl-item">
                                <div class="tl-dot {{ $isUndo ? 'bg-warning border-0' : $log->status }} shadow-sm" style="{{ $isUndo ? 'border: 3px solid #fff !important;' : '' }}">
                                    @if($isUndo)
                                        <i class="fa-solid fa-rotate-left" style="font-size: 11px; color: #fff;"></i>
                                    @else
                                        @if($log->status=='pending') <i class="fa-solid fa-box-open" style="font-size: 10px; color: #fff; margin-top: -1px; margin-left: -1px;"></i>
                                        @elseif($log->status=='confirmed') <i class="fa-solid fa-check" style="font-size: 10px; color: #fff; margin-top: -1px; margin-left: -1px;"></i>
                                        @elseif($log->status=='shipping') <i class="fa-solid fa-truck" style="font-size: 9px; color: #fff; margin-top: -1px; margin-left: -1px;"></i>
                                        @elseif($log->status=='completed') <i class="fa-solid fa-star" style="font-size: 9px; color: #fff; margin-top: -1px; margin-left: -1px;"></i>
                                        @elseif($log->status=='cancelled') <i class="fa-solid fa-xmark" style="font-size: 11px; color: #fff; margin-top: -1px; margin-left: -1px;"></i>
                                        @endif
                                    @endif
                                </div>
                                
                                <div class="tl-content shadow-sm" style="{{ $isUndo ? 'border-left: 3px solid #f59e0b; background-color: #fefce8;' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $isUndo ? 'Hoàn tác về:' : 'Chuyển trạng thái:' }} 
                                            <span class="badge-soft text-{{ $log->status == 'cancelled' ? 'danger' : ($isUndo ? 'warning' : 'primary') }} bg-{{ $log->status == 'cancelled' ? 'danger' : ($isUndo ? 'warning' : 'primary') }}-soft p-1 px-2 ms-1 rounded">
                                                {{ $statusLabels[$log->status] ?? ucfirst($log->status) }}
                                            </span>
                                        </h6>
                                        <span class="time-badge bg-white shadow-sm border-0"><i class="fa-regular fa-clock text-{{ $isUndo ? 'warning' : 'primary' }} me-1"></i>{{ $log->created_at->format('H:i') }}</span>
                                    </div>
                                    <p class="text-muted small mb-0 mt-2 fw-medium">
                                        <i class="fa-regular fa-calendar me-1"></i>{{ $log->created_at->format('d/m/Y') }}
                                        @if($isUndo)
                                            <span class="ms-1 fst-italic text-warning" style="opacity: 0.9;">(Do thao tác hoàn tác)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4"><i class="fa-regular fa-folder-open fs-3 mb-2 opacity-50"></i><br>Chưa có dữ liệu nhật ký.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ================= SUPER CSS UI/UX LỘT XÁC ================= --}}
<style>
    /* Typography & Utils */
    .fw-extrabold { font-weight: 800; }
    .tracking-wide { letter-spacing: 0.5px; }
    .icon-box-sm { width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; }
    
    /* System Soft Colors */
    .bg-primary-soft { background: #eff6ff; }
    .bg-success-soft { background: #f0fdf4; }
    .bg-warning-soft { background: #fffbeb; }
    .bg-danger-soft { background: #fef2f2; }
    .bg-secondary-soft { background: #f8fafc; }

    .text-gradient { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    /* Cards */
    .premium-card {
        border-radius: 16px; border: none; background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .premium-card:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025); }
    .bg-gradient-light { background: linear-gradient(to right, #f8fafc, #f1f5f9); }

    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Buttons */
    .btn-white { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; font-weight: 600; color: #475569; padding: 8px 16px; transition: all 0.2s; }
    .btn-white:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
    .btn-gradient-primary { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; border-radius: 12px; font-weight: 600; color: #fff; transition: all 0.2s; }
    .shadow-primary { box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3); }
    .btn-gradient-primary:hover { box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4); transform: translateY(-2px); color: #fff;}
    .btn-gradient-primary:disabled { background: #94a3b8; box-shadow: none; transform: none; cursor: not-allowed; }
    .hover-lift { transition: all 0.2s ease; border-radius: 12px; }
    .hover-lift:hover { transform: translateY(-2px); }

    /* Info list */
    .info-group { padding: 14px 0; border-bottom: 1px dashed #e2e8f0; }
    .bg-gradient-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .avatar-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
    .badge-soft { padding: 6px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
    
    /* SUPER POLISHED SELECT DROPDOWN */
    .status-dropdown-wrapper { position: relative; user-select: none; }
    .custom-select-trigger {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; background: #fff; border: 2px solid #e2e8f0;
        border-radius: 12px; cursor: pointer; transition: all 0.2s; font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .custom-select-trigger:hover { border-color: #3b82f6; }
    .custom-select-trigger.disabled { background: #f8fafc; cursor: not-allowed; opacity: 0.7; border-color: #e2e8f0 !important; }
    
    .custom-select-options {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0;
        background: #fff; border-radius: 16px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); /* Float shadow */
        border: 1px solid #f1f5f9; opacity: 0; visibility: hidden; transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); z-index: 100;
        padding: 8px; /* Inner padding for floating items */
    }
    .custom-select-options.show { opacity: 1; visibility: visible; transform: translateY(0); }
    
    .custom-opt {
        padding: 10px 14px; display: flex; align-items: center; cursor: pointer; 
        transition: all 0.2s ease; border-radius: 10px; margin-bottom: 4px;
        border: 1px solid transparent;
    }
    .custom-opt:last-child { margin-bottom: 0; }
    .custom-opt:hover { background: #f8fafc; transform: translateX(4px); } /* Smooth slide right instead of jerky padding */
    .custom-opt.active-opt { background: #f8fafc; border-color: #e2e8f0; }
    .custom-opt.disabled-opt { opacity: 0.4; cursor: not-allowed; background: transparent; }
    .custom-opt.disabled-opt:hover { transform: none; background: transparent; }
    .icon-wrap { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 14px; font-size: 1.1rem;}

    /* Badges */
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

    /* Timeline Ngang */
    .stepper-wrapper { position: relative; padding: 20px 20px 10px; margin-top: 10px;}
    .stepper-progress-bar { position: absolute; top: 40px; left: 12.5%; width: 75%; height: 4px; background: #f1f5f9; border-radius: 4px; z-index: 1; }
    .stepper-progress-fill { height: 100%; border-radius: 4px; z-index: 2; transition: width 0.6s cubic-bezier(0.22, 1, 0.36, 1); background: linear-gradient(90deg, #3b82f6, #0ea5e9); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); }
    .stepper-steps { display: flex; justify-content: space-between; position: relative; z-index: 3; }
    .step-item { text-align: center; width: 25%; }
    .step-icon-box { width: 44px; height: 44px; margin: 0 auto; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #fff; border: 3px solid #e2e8f0; color: #94a3b8; font-size: 1.1rem; transition: all 0.4s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .step-title { font-weight: 700; font-size: 0.8rem; color: #94a3b8; transition: color 0.4s ease; word-break: break-word; padding: 0 5px;}
    .step-item.passed .step-icon-box { background: #3b82f6; border-color: #3b82f6; color: #fff; }
    .step-item.passed .step-title { color: #2563eb; }
    .step-item.active .step-icon-box { border-color: #3b82f6; color: #3b82f6; box-shadow: 0 0 0 6px rgba(59, 130, 246, 0.15) !important; animation: pulse-ring 2s infinite; }
    .step-item.active .step-title { color: #0f172a; font-weight: 800; }
    @keyframes pulse-ring { 0% { box-shadow: 0 0 0 0 rgba(59,130,246,0.2); } 70% { box-shadow: 0 0 0 10px rgba(59,130,246,0); } 100% { box-shadow: 0 0 0 0 rgba(59,130,246,0); } }

    /* Bảng Sản Phẩm */
    .premium-table { border-collapse: separate; border-spacing: 0 8px; table-layout: fixed !important; width: 100% !important; min-width: 0 !important; }
    .premium-table th, .premium-table td { white-space: normal !important; word-wrap: break-word !important; overflow-wrap: break-word !important; }
    .premium-table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding-bottom: 8px; border: none; background: transparent; color: #64748b;}
    .premium-table tbody tr { background: #fff; transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
    .premium-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.03); border-radius: 12px; background: #fafafa;}
    .premium-table td { border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .premium-table td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .premium-table td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    /* Ảnh Popup Eye Overlay */
    .product-img-premium { width: 60px; height: 60px; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; flex-shrink: 0; background: #fff; position: relative; cursor: pointer;}
    .product-img-premium img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
    .eye-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4); display: flex; align-items: center; justify-content: center; color: #fff; opacity: 0; transition: opacity 0.3s ease; font-size: 1.2rem; }
    .product-img-premium:hover img { transform: scale(1.1); }
    .product-img-premium:hover .eye-overlay { opacity: 1; }

    /* Timeline Lịch sử dọc */
    .timeline-vertical { position: relative; padding-left: 12px; max-height: 350px; overflow-y: auto; padding-right: 10px; margin-top: 10px; }
    .timeline-vertical::before { content: ''; position: absolute; top: 10px; left: 21px; bottom: 10px; width: 2px; background: #f1f5f9; border-radius: 2px; }
    .tl-item { position: relative; margin-bottom: 20px; padding-left: 36px;}
    .tl-item:last-child { margin-bottom: 0; }
    .tl-dot { position: absolute; left: 0; top: 5px; width: 24px; height: 24px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; z-index: 2; border: 3px solid #fff; }
    .tl-dot.confirmed { background: #3b82f6; }
    .tl-dot.shipping { background: #f59e0b; }
    .tl-dot.completed { background: #22c55e; }
    .tl-dot.cancelled { background: #ef4444; }
    .tl-content { background: #fff; padding: 12px 16px; border-radius: 12px; border: 1px solid #f1f5f9; transition: box-shadow 0.2s, border-color 0.2s;}
    .tl-item:hover .tl-content { box-shadow: 0 4px 12px rgba(0,0,0,0.03); border-color: #e2e8f0;}
    .time-badge { font-size: 0.75rem; font-weight: 700; color: #64748b; background: #f8fafc; padding: 4px 8px; border-radius: 6px; border: 1px solid #f1f5f9;}

    /* Cuộn mượt */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; } 
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Modal & Toast */
    .toast{ position:fixed; bottom:30px; right:30px; background:#1e293b; color:white; padding:14px 24px; border-radius:12px; opacity:0; transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index:99999; box-shadow: 0 15px 35px rgba(0,0,0,0.15); font-weight: 600; transform: translateY(20px);}
    .toast.show{opacity:1; transform: translateY(0);}
    .cancel-modal{ position:fixed; inset:0; background:rgba(15, 23, 42, 0.5); display:none; align-items:center; justify-content:center; z-index:9999; backdrop-filter: blur(4px);}
    .cancel-box{ background:#fff; padding:35px; border-radius:24px; width:380px; text-align:center; animation:popModal .4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 25px 50px rgba(0,0,0,0.2);}
    @keyframes popModal{ from{transform:scale(.9) translateY(20px);opacity:0} to{transform:scale(1) translateY(0);opacity:1} }
    .modal-icon-wrap { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;}
    
    /* Modal Image Zoom */
    .image-modal-content { background: transparent; box-shadow: none; width: auto; max-width: 90vw; padding: 0; position: relative;}
    .image-modal-content img { max-height: 85vh; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); object-fit: contain; background: #fff; padding: 10px;}
    .close-img-btn { position: absolute; top: -15px; right: -15px; width: 40px; height: 40px; border-radius: 50%; background: #fff; color: #ef4444; border: none; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer; transition: transform 0.2s; z-index: 10; }
    .close-img-btn:hover { transform: scale(1.1); background: #fee2e2; }
</style>

<div id="toast" class="toast"><i class="fa-solid fa-circle-check me-2"></i>Cập nhật thành công</div>

{{-- ================= JAVASCRIPT LOGIC ================= --}}
<script>
function setCustomSelect(val) {
    let select = document.getElementById('statusSelect');
    if(select) select.value = val;
    
    let opt = document.querySelector(`.custom-opt[data-value="${val}"]`);
    if(opt) {
        let triggerContent = document.getElementById('triggerContent');
        let clone = opt.cloneNode(true);
        let check = clone.querySelector('.check-icon'); 
        if(check) check.remove();
        triggerContent.innerHTML = clone.innerHTML;
    }
    document.getElementById('customSelectOptions').classList.remove('show');
}

function toggleStatusDropdown() {
    let trigger = document.querySelector('.custom-select-trigger');
    if(trigger.classList.contains('disabled')) return;
    document.getElementById('customSelectOptions').classList.toggle('show');
}

document.addEventListener('click', function(e) {
    if(!e.target.closest('.status-dropdown-wrapper')) {
        let opts = document.getElementById('customSelectOptions');
        if(opts) opts.classList.remove('show');
    }
});

function initOrderJS() {
    const select = document.getElementById('statusSelect');
    if (select) {
        setCustomSelect(select.value);
    }
}
document.addEventListener("DOMContentLoaded", initOrderJS);

function sendAjaxRequest(url, bodyData, successMsg, bgColor) {
    let btnUp = document.getElementById('btnUpdate');
    let btnUn = document.getElementById('btnUndo');
    
    if(btnUp) btnUp.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i>Đang xử lý...';
    if(btnUp) btnUp.disabled = true;
    if(btnUn) btnUn.disabled = true;

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
            let errorMsg = 'Có lỗi xảy ra trong quá trình xử lý!';
            try {
                let errorData = await response.json();
                errorMsg = errorData.error || errorData.message || errorMsg;
            } catch(e) {
                errorMsg = "Lỗi Server (500). F12 -> Network để xem chi tiết!";
            }
            throw new Error(errorMsg); 
        }
        return response.text(); 
    })
    .then(html => {
        let parser = new DOMParser();
        let doc = parser.parseFromString(html, 'text/html');
        let newLeft = doc.getElementById('leftColumn');
        let newRight = doc.getElementById('rightColumn');

        if (newLeft && newRight) {
            document.getElementById('leftColumn').innerHTML = newLeft.innerHTML;
            document.getElementById('rightColumn').innerHTML = newRight.innerHTML;
            initOrderJS();
            showToast(successMsg, bgColor);
        } else { location.reload(); }
    })
    .catch(error => { 
        console.error("Lỗi:", error); 
        showToast(error.message, "#dc3545");
        
        if(btnUp) {
            btnUp.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-2"></i>Lưu thay đổi';
            btnUp.disabled = false;
        }
        if(btnUn) btnUn.disabled = false;
        initOrderJS(); 
    });
}

function updateStatus(){
    let select = document.getElementById('statusSelect');
    if (!select) return;
    let status = select.value;
    let originalStatus = document.getElementById('currentStatus').getAttribute('data-status');
    if (status === originalStatus) return; 
    sendAjaxRequest("{{ url('/admin/orders/'.$order->id.'/status') }}", { status: status }, "Cập nhật thành công", "#10b981");
}

function handleUpdate(){
    let select = document.getElementById('statusSelect');
    if(select && select.value === 'cancelled'){
        document.getElementById('cancelModal').style.display='flex';
        return;
    }
    updateStatus();
}

function executeUndo(){
    document.getElementById('undoModal').style.display='none';
    sendAjaxRequest("{{ url('/admin/orders/'.$order->id.'/undo') }}", {}, "Đã hoàn tác trạng thái", "#f59e0b");
}

function closeCancel(){ document.getElementById('cancelModal').style.display='none'; }
function confirmCancel(){ document.getElementById('cancelModal').style.display='none'; updateStatus(); }
function confirmUndo(){ document.getElementById('undoModal').style.display='flex'; }
function closeUndo(){ document.getElementById('undoModal').style.display='none'; }

function openImageModal(imgSrc) {
    document.getElementById('zoomedImage').src = imgSrc;
    document.getElementById('imagePopupModal').style.display = 'flex';
}
function closeImageModal() {
    document.getElementById('imagePopupModal').style.display = 'none';
}

function showToast(msg, bgColor){
    let t = document.getElementById('toast');
    let icon = bgColor === '#dc3545' ? 'fa-triangle-exclamation' : 'fa-circle-check';
    t.innerHTML = `<i class="fa-solid ${icon} me-2"></i>${msg}`;
    
    t.style.background = bgColor;
    t.style.color = "#fff";
    if(bgColor === '#f59e0b') t.style.color = "#000"; 
    t.classList.add('show');
    
    let timeoutDelay = bgColor === '#dc3545' ? 4500 : 2500;
    setTimeout(()=>t.classList.remove('show'), timeoutDelay); 
}
</script>

{{-- ================= MODALS ================= --}}

<div id="imagePopupModal" class="cancel-modal" onclick="closeImageModal()">
    <div class="cancel-box image-modal-content" onclick="event.stopPropagation()">
        <button class="close-img-btn" onclick="closeImageModal()"><i class="fa-solid fa-xmark"></i></button>
        <img id="zoomedImage" src="" alt="Zoom">
    </div>
</div>

<div id="cancelModal" class="cancel-modal">
    <div class="cancel-box">
        <div class="modal-icon-wrap bg-danger-soft text-danger mb-3"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h4 class="fw-extrabold text-dark mb-2">Huỷ đơn hàng?</h4>
        <p class="text-muted mb-4">Bạn có chắc chắn muốn huỷ đơn hàng này?<br>Thao tác này <strong>không thể hoàn tác</strong>.</p>
        <div class="d-flex gap-2">
            <button class="btn btn-light w-50 fw-bold border hover-lift" onclick="closeCancel()">Đóng</button>
            <button class="btn btn-danger w-50 fw-bold hover-lift" onclick="confirmCancel()">Xác nhận Huỷ</button>
        </div>
    </div>
</div>

<div id="undoModal" class="cancel-modal">
    <div class="cancel-box">
        <div class="modal-icon-wrap bg-warning-soft text-warning mb-3"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <h4 class="fw-extrabold text-dark mb-2">Xác nhận hoàn tác</h4>
        <p class="text-muted mb-4">Hệ thống sẽ lùi trạng thái giao hàng lại 1 bước so với tiến trình hiện tại.</p>
        <div class="d-flex gap-2">
            <button class="btn btn-light w-50 fw-bold border hover-lift" onclick="closeUndo()">Đóng</button>
            <button class="btn btn-warning w-50 fw-bold hover-lift" style="color: #000;" onclick="executeUndo()">Đồng ý</button>
        </div>
    </div>
</div>

@endsection