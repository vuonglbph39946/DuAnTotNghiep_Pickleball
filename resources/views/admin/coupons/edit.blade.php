@extends('admin.layouts.app')
@section('title', 'Chỉnh Sửa Mã Giảm Giá')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="card-title text-warning mb-4"><i class="mdi mdi-pencil"></i> Chỉnh Sửa Mã: {{ $coupon->code }}</h4>

                {{-- Báo lỗi tổng quát từ Server --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="mdi mdi-alert-circle"></i> Vui lòng kiểm tra lại các thông tin báo đỏ bên dưới!
                    </div>
                @endif

                {{-- Nơi hiển thị Popup báo lỗi Real-time bằng JS --}}
                <div id="js-error-box" class="alert alert-danger d-none">
                    <i class="mdi mdi-alert-circle"></i> <span id="js-error-text"></span>
                </div>

                <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" id="couponForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mã CODE <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $coupon->code) }}" required style="text-transform: uppercase;">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="1" {{ old('status', $coupon->status) == 1 ? 'selected' : '' }}>Kích hoạt (Hoạt động)</option>
                                <option value="0" {{ old('status', $coupon->status) == 0 ? 'selected' : '' }}>Tạm khóa</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại giảm giá <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                                <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Giảm thẳng (VNĐ)</option>
                                <option value="percent" {{ old('discount_type', $coupon->discount_type) == 'percent' ? 'selected' : '' }}>Giảm theo Phần trăm (%)</option>
                            </select>
                            @error('discount_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mức giảm <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', intval($coupon->discount_value)) }}" required>
                            @error('discount_value') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3" id="max_discount_box" style="display: none;">
                            <label class="form-label fw-bold">Giảm Tối Đa (VNĐ) <small class="text-muted">(Dành cho loại %)</small></label>
                            <input type="number" name="max_discount_value" min="0" class="form-control @error('max_discount_value') is-invalid @enderror" value="{{ old('max_discount_value', intval($coupon->max_discount_value)) }}" placeholder="Để trống nếu không giới hạn">
                            @error('max_discount_value') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Đơn hàng tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="min_order_value" min="0" class="form-control @error('min_order_value') is-invalid @enderror" value="{{ old('min_order_value', intval($coupon->min_order_value)) }}" required>
                            @error('min_order_value') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tổng số lượng phát hành <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $coupon->quantity) }}" required>
                            @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số lần 1 KH được dùng <span class="text-danger">*</span></label>
                            <input type="number" name="max_usage_per_user" min="1" class="form-control @error('max_usage_per_user') is-invalid @enderror" value="{{ old('max_usage_per_user', $coupon->max_usage_per_user) }}" required>
                            @error('max_usage_per_user') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $coupon->start_date ? \Carbon\Carbon::parse($coupon->start_date)->format('Y-m-d') : '') }}" required>
                            @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $coupon->end_date ? \Carbon\Carbon::parse($coupon->end_date)->format('Y-m-d') : '') }}" required>
                            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning fw-bold text-dark">Cập Nhật Mã Giảm Giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        function toggleMaxDiscount() {
            var inputDiscount = $('#discount_value');
            if ($('#discount_type').val() === 'percent') {
                $('#max_discount_box').slideDown();
                inputDiscount.attr('min', '5');
                inputDiscount.attr('max', '100');
            } else {
                $('#max_discount_box').slideUp();
                inputDiscount.attr('min', '10000');
                inputDiscount.removeAttr('max');
            }
        }
        $('#discount_type').on('change', toggleMaxDiscount);
        toggleMaxDiscount();

        // Xử lý Popup lỗi ngày tháng
        $('#couponForm').on('submit', function(e) {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var errorBox = $('#js-error-box');
            var errorText = $('#js-error-text');

            if (startDate && endDate) {
                if (new Date(endDate) < new Date(startDate)) {
                    e.preventDefault(); // Chặn việc gửi form đi
                    
                    // Hiện popup lỗi đẹp mắt
                    errorText.text('Lỗi: Ngày kết thúc không thể nhỏ hơn ngày bắt đầu!');
                    errorBox.removeClass('d-none').addClass('d-block');
                    
                    // Đổi viền ô lỗi thành màu đỏ
                    $('#end_date').addClass('is-invalid');
                    $('#end_date').focus();
                    
                    // Cuộn lên trên
                    $('html, body').animate({ scrollTop: 0 }, 'fast');
                } else {
                    // Ẩn lỗi nếu đúng
                    errorBox.removeClass('d-block').addClass('d-none');
                    $('#end_date').removeClass('is-invalid');
                }
            }
        });
    });
</script>
@endpush
@endsection